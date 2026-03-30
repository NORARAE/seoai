<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserOnboardingController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user->isApproved()) {
            return redirect()->route('pending-approval');
        }

        if (! is_null($user->onboarding_completed_at)) {
            return redirect()->route('app.dashboard');
        }

        return view('user-onboarding');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->isApproved()) {
            return redirect()->route('pending-approval');
        }

        $validated = $request->validate([
            'business_name'     => ['required', 'string', 'max:255'],
            'website_url'       => ['nullable', 'url', 'max:500'],
            'industry'          => ['required', 'string', 'max:255'],
            'role_at_company'   => ['required', 'string', 'max:255'],
            'primary_market'    => ['required', 'string', 'max:255'],
            'services'          => ['nullable', 'array'],
            'services.*'        => ['string', 'max:100'],
            'top_goal'          => ['required', 'string', 'max:255'],
            'biggest_challenge' => ['required', 'string', 'max:2000'],
        ]);

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        $user->update(['onboarding_completed_at' => now()]);

        return redirect()->route('app.dashboard');
    }
}
