<?php

namespace App\Models;

use App\Enums\SystemTier;
use App\Models\UserProfile;
use App\Notifications\AdminPasswordResetNotification;
use App\Support\ActiveSiteContext;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery, Notifiable;

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active !== false
            && ($this->isPrivilegedStaff() || $this->isFrontendDev());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    /**
     * Mass-assignable attributes.
     *
     * Note on privileged fields (role, approved, permissions, is_active):
     * These are intentionally fillable so Filament admin operations work.
     * They are safe because (a) no public-facing route calls User::create/update
     * with raw request data, and (b) the Filament registration form schema only
     * exposes name/email/password — Livewire ignores extra POST body parameters.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'client_id',
        'role',
        'permissions',
        'last_login_at',
        'onboarding_completed_at',
        'is_active',
        'approved',
        'use_case',
        'google_id',
        'google_avatar',
        'auth_provider',
        'signup_ip',
        'signup_user_agent',
        'signup_referrer',
        'signup_source',
        'signup_timezone',
        'signup_utm',
        'system_tier',
        'system_tier_upgraded_at',
        'stripe_checkout_session_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'last_login_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'is_active' => 'boolean',
            'approved' => 'boolean',
            'system_tier' => SystemTier::class,
            'system_tier_upgraded_at' => 'datetime',
        ];
    }

    /**
     * Get the client that owns the user
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Onboarding profile collected during workspace setup.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the roles assigned to the user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'site_user')->withTimestamps();
    }

    public function quickScans(): HasMany
    {
        return $this->hasMany(QuickScan::class);
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(UserEntitlement::class);
    }

    /**
     * Check if user has a role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is owner
     */
    public function isOwner(): bool
    {
        return in_array($this->role, ['owner', 'buyer'], true);
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'superadmin'], true);
    }

    /**
     * Whether this user's account has been approved for dashboard access.
     */
    public function isApproved(): bool
    {
        return (bool) $this->approved;
    }

    /**
     * Whether this user is allowed to approve/revoke other users.
     * SuperAdmin, Admin, AccountManager only.
     */
    public function canApproveUsers(): bool
    {
        return in_array($this->role, [
            'super_admin',
            'superadmin',
            'admin',
            'owner',
            'account_manager',
        ], true);
    }

    /**
     * Whether this user may bypass the approval gate entirely (privileged staff).
     * These users are always allowed into the app regardless of the approved flag.
     */
    public function isPrivilegedStaff(): bool
    {
        return $this->canApproveUsers();
    }

    public function isOperator(): bool
    {
        return in_array($this->role, ['operator', 'team_member', 'member'], true);
    }

    /**
     * Whether this user is a temporary frontend contractor with restricted admin access.
     * frontend_dev users may only access pages listed in App\Support\FrontendDevAccess.
     */
    public function isFrontendDev(): bool
    {
        return $this->role === 'frontend_dev';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || in_array($this->role, ['owner', 'buyer', 'admin'], true);
    }

    /**
     * @return Collection<int, Site>
     */
    public function accessibleSites(): Collection
    {
        if ($this->isSuperAdmin()) {
            return Site::query()->orderBy('domain')->get();
        }

        $assigned = $this->sites()->orderBy('domain')->get();

        if ($assigned->isNotEmpty()) {
            return $assigned;
        }

        $effectiveClientId = $this->client_id ?: ActiveSiteContext::resolveClientIdForUser($this);

        if ($effectiveClientId) {
            return Site::query()
                ->where('client_id', $effectiveClientId)
                ->orderBy('domain')
                ->get();
        }

        return collect();
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(string $permission): bool
    {
        // Owners have all permissions
        if ($this->isOwner()) {
            return true;
        }

        // Check direct permissions
        if (in_array($permission, $this->permissions ?? [])) {
            return true;
        }

        // Check role permissions
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Send the password reset notification via the Filament admin route.
     * Laravel's default ResetPassword notification uses route('password.reset')
     * which does not exist in this Filament-only application — overriding here
     * so the reset link in the email points to the correct admin reset page.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new AdminPasswordResetNotification($token));
    }

    /**
     * Upgrade the user's system tier if the new tier is higher.
     */
    public function upgradeSystemTier(SystemTier $tier): bool
    {
        $currentRank = $this->system_tier?->rank() ?? 0;

        if ($tier->rank() > $currentRank) {
            $this->update([
                'system_tier' => $tier,
                'system_tier_upgraded_at' => now(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Get the tier rank (0 = no tier, 1-4 = scan-basic through system-activation).
     */
    public function tierRank(): int
    {
        return $this->system_tier?->rank() ?? 0;
    }

    public function hasAccessTo(string $entitlementKey): bool
    {
        return $this->entitlements()
            ->where('entitlement_key', $entitlementKey)
            ->where('status', 'active')
            ->exists();
    }
}
