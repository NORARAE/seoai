<?php

namespace App\Enums;

enum SystemTier: string
{
    case SCAN_BASIC = 'scan-basic';
    case SIGNAL_EXPANSION = 'signal-expansion';
    case STRUCTURAL_LEVERAGE = 'structural-leverage';
    case SYSTEM_ACTIVATION = 'system-activation';

    public function label(): string
    {
        return match ($this) {
            self::SCAN_BASIC => 'Base Scan',
            self::SIGNAL_EXPANSION => 'Signal Expansion',
            self::STRUCTURAL_LEVERAGE => 'Structural Leverage',
            self::SYSTEM_ACTIVATION => 'System Activation',
        };
    }

    public function rank(): int
    {
        return match ($this) {
            self::SCAN_BASIC => 1,
            self::SIGNAL_EXPANSION => 2,
            self::STRUCTURAL_LEVERAGE => 3,
            self::SYSTEM_ACTIVATION => 4,
        };
    }

    public function price(): string
    {
        return match ($this) {
            self::SCAN_BASIC => '$2',
            self::SIGNAL_EXPANSION => '$99',
            self::STRUCTURAL_LEVERAGE => '$249',
            self::SYSTEM_ACTIVATION => '$489',
        };
    }

    public function configKey(): string
    {
        return match ($this) {
            self::SCAN_BASIC => 'base-scan',
            self::SIGNAL_EXPANSION => 'diagnostic',
            self::STRUCTURAL_LEVERAGE => 'fix-strategy',
            self::SYSTEM_ACTIVATION => 'optimization',
        };
    }

    public function nextTier(): ?self
    {
        return match ($this) {
            self::SCAN_BASIC => self::SIGNAL_EXPANSION,
            self::SIGNAL_EXPANSION => self::STRUCTURAL_LEVERAGE,
            self::STRUCTURAL_LEVERAGE => self::SYSTEM_ACTIVATION,
            self::SYSTEM_ACTIVATION => null,
        };
    }

    public function completedLayers(): array
    {
        return match ($this) {
            self::SCAN_BASIC => ['Base Scan'],
            self::SIGNAL_EXPANSION => ['Base Scan', 'Signal Expansion'],
            self::STRUCTURAL_LEVERAGE => ['Base Scan', 'Signal Expansion', 'Structural Leverage'],
            self::SYSTEM_ACTIVATION => ['Base Scan', 'Signal Expansion', 'Structural Leverage', 'System Activation'],
        };
    }

    public function nextStep(): ?string
    {
        return match ($this) {
            self::SCAN_BASIC => 'Expand your signals to see the full picture — $99',
            self::SIGNAL_EXPANSION => 'Take structural control of your visibility — $249',
            self::STRUCTURAL_LEVERAGE => 'Activate the full system — $489',
            self::SYSTEM_ACTIVATION => null,
        };
    }

    public function nextRoute(): ?string
    {
        return match ($this) {
            self::SCAN_BASIC => 'checkout.signal-expansion',
            self::SIGNAL_EXPANSION => 'checkout.structural-leverage',
            self::STRUCTURAL_LEVERAGE => 'checkout.system-activation',
            self::SYSTEM_ACTIVATION => null,
        };
    }
}
