{{-- SYSTEM DESIGN RULES --}}

{{-- This file defines the visual and interaction system for SEOAIco.
   All new UI must follow these rules strictly. --}}

SYSTEM DESIGN DIRECTIVE:

You MUST reuse existing design system styles and components.

DO NOT:
- create new visual styles unless absolutely necessary
- introduce new color values
- introduce new typography scales
- create duplicate component patterns

DO:
- reuse existing classes from:
  - landing.blade.php
  - pricing.blade.php
  - how-it-works.blade.php
- match spacing, font sizes, opacity, and gold accent system
- reuse button styles (btn-primary, etc.)
- reuse animation patterns (fade, translateY, ambient motion)
- maintain dark luxe aesthetic (gold + charcoal + ivory)

ANIMATION:
- use existing motion patterns (no new animation styles)
- prefer subtle transitions (opacity, transform)
- no aggressive or fast motion

COMPONENT REUSE:
- if a similar section exists, reuse its structure
- extend existing components instead of rebuilding

CONSISTENCY IS CRITICAL.