<x-filament-panels::page>
<div class="space-y-6 text-sm">

  {{-- Header --}}
  <x-filament::section>
    <x-slot name="heading">SEO AI Co™ — Brand System</x-slot>
    <x-slot name="description">Internal reference only. All agents and developers must follow these rules when modifying UI, copy, or branding.</x-slot>
    <div class="flex flex-wrap gap-3 text-xs text-gray-500">
      <span class="rounded bg-gray-100 px-2 py-1 font-mono">v1.0</span>
      <span class="rounded bg-gray-100 px-2 py-1">Effective April 3, 2026</span>
      <span class="rounded bg-amber-50 px-2 py-1 text-amber-700 font-medium">Internal — not for public distribution</span>
    </div>
  </x-filament::section>

  {{-- 1. Brand Name --}}
  <x-filament::section>
    <x-slot name="heading">1. Canonical Brand Name</x-slot>
    <div class="space-y-4">
      <div class="overflow-x-auto">
        <table class="w-full text-xs border-collapse">
          <thead>
            <tr class="border-b border-gray-200 text-left">
              <th class="py-2 pr-4 font-semibold text-gray-700">Context</th>
              <th class="py-2 pr-4 font-semibold text-green-700">✓ Correct</th>
              <th class="py-2 font-semibold text-red-700">✗ Incorrect</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr><td class="py-2 pr-4 text-gray-600">First mention per page/email</td><td class="py-2 pr-4 font-mono text-green-700">SEO AI Co™</td><td class="py-2 font-mono text-red-600">SEOAIco, SEO AI Co, SEOAI Co</td></tr>
            <tr><td class="py-2 pr-4 text-gray-600">Subsequent mentions</td><td class="py-2 pr-4 font-mono text-green-700">SEO AI Co</td><td class="py-2 font-mono text-red-600">SEOAIco</td></tr>
            <tr><td class="py-2 pr-4 text-gray-600">Code (slugs, env, routes)</td><td class="py-2 pr-4 font-mono text-green-700">seoaico</td><td class="py-2 text-gray-400">n/a</td></tr>
            <tr><td class="py-2 pr-4 text-gray-600">Display URL</td><td class="py-2 font-mono text-green-700">seoaico.com</td><td class="py-2 text-gray-400">n/a</td></tr>
          </tbody>
        </table>
      </div>
      <p class="text-xs text-gray-500">The ™ must be rendered superscript and never styled with color, weight, or size matching the brand name. In HTML: <code class="rounded bg-gray-100 px-1 py-0.5 font-mono text-xs">SEO AI Co™</code> using the Unicode character directly.</p>
    </div>
  </x-filament::section>

  {{-- 2. Wordmark --}}
  <x-filament::section>
    <x-slot name="heading">2. Wordmark Rendering</x-slot>
    <div class="space-y-4">
      <div class="rounded-lg bg-gray-950 px-6 py-5 flex items-center gap-1 w-fit">
        <span style="color:#ede8de;font-family:Georgia,serif;font-size:1.5rem;letter-spacing:-.01em">SEO</span>
        <span style="color:#c8a84b;font-family:Georgia,serif;font-size:1.5rem;letter-spacing:-.01em">AI</span>
        <span style="color:rgba(237,232,222,.45);font-family:Georgia,serif;font-size:1.15rem;letter-spacing:-.01em">co</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-xs border-collapse">
          <thead>
            <tr class="border-b border-gray-200 text-left">
              <th class="py-2 pr-4 font-semibold text-gray-700">Segment</th>
              <th class="py-2 pr-4 font-semibold text-gray-700">Color</th>
              <th class="py-2 pr-4 font-semibold text-gray-700">CSS</th>
              <th class="py-2 font-semibold text-gray-700">Intent</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr><td class="py-2 pr-4 font-mono font-semibold">SEO</td><td class="py-2 pr-4">Ivory</td><td class="py-2 pr-4 font-mono text-gray-500">#ede8de</td><td class="py-2 text-gray-600">Establishes domain</td></tr>
            <tr><td class="py-2 pr-4 font-mono font-semibold text-amber-600">AI</td><td class="py-2 pr-4">Gold</td><td class="py-2 pr-4 font-mono text-gray-500">#c8a84b</td><td class="py-2 text-gray-600">Differentiator — the intelligence layer</td></tr>
            <tr><td class="py-2 pr-4 font-mono text-gray-400">co</td><td class="py-2 pr-4">Subdued Ivory</td><td class="py-2 pr-4 font-mono text-gray-500">rgba(237,232,222,.45)</td><td class="py-2 text-gray-600">Company marker — present, not competing</td></tr>
          </tbody>
        </table>
      </div>
      <div class="rounded bg-red-50 border border-red-100 p-3 text-xs text-red-700 space-y-1">
        <p class="font-semibold">Prohibited:</p>
        <ul class="list-disc list-inside space-y-0.5 text-red-600">
          <li>Do NOT bold any segment of the wordmark</li>
          <li>Do NOT add shadow, glow, or letterpress</li>
          <li>Do NOT split the wordmark across lines</li>
          <li>Do NOT animate the wordmark</li>
        </ul>
      </div>
    </div>
  </x-filament::section>

  {{-- 3. Color System --}}
  <x-filament::section>
    <x-slot name="heading">3. Color System</x-slot>
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
      @foreach([
        ['Gold', '#c8a84b', 'bg-amber-400', 'Primary accent — CTAs, active states'],
        ['Gold Light', '#e2c97d', 'bg-amber-300', 'Hover state of gold elements'],
        ['Gold Dim', '#9a7a30', 'bg-amber-600', 'Inactive/done state'],
        ['Ivory', '#ede8de', 'bg-stone-100', 'Primary text — headings, labels'],
        ['Deep Black', '#080808', 'bg-gray-950', 'Primary background'],
        ['Surface', '#0b0b0b', 'bg-gray-950', 'Card backgrounds, form fields'],
        ['Muted', '#a8a8a0', 'bg-gray-400', 'Secondary text, labels'],
        ['Faint', '#555', 'bg-gray-600', 'Tertiary text, disabled states'],
        ['Invisible', '#3a3a35', 'bg-gray-700', 'Micro-lines, availability notes'],
      ] as [$name, $hex, $tailwind, $usage])
      <div class="flex items-start gap-3 rounded-lg border border-gray-100 p-3">
        <div class="mt-0.5 h-6 w-6 shrink-0 rounded border border-gray-200 {{ $tailwind }}"></div>
        <div>
          <p class="font-medium text-gray-800">{{ $name }}</p>
          <p class="font-mono text-[11px] text-gray-500">{{ $hex }}</p>
          <p class="mt-0.5 text-[11px] text-gray-500">{{ $usage }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </x-filament::section>

  {{-- 4. Booking Card Reference --}}
  <x-filament::section>
    <x-slot name="heading">4. Booking Card Reference (Current)</x-slot>
    <div class="overflow-x-auto">
      <table class="w-full text-xs border-collapse">
        <thead>
          <tr class="border-b border-gray-200 text-left">
            <th class="py-2 pr-4 font-semibold text-gray-700">Slug</th>
            <th class="py-2 pr-4 font-semibold text-gray-700">Name</th>
            <th class="py-2 pr-4 font-semibold text-gray-700">Outcome Line</th>
            <th class="py-2 pr-4 font-semibold text-gray-700">Qualification</th>
            <th class="py-2 font-semibold text-gray-700">CSS Class</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr>
            <td class="py-2 pr-4 font-mono text-gray-500">discovery</td>
            <td class="py-2 pr-4 font-medium">Market Clarity Session</td>
            <td class="py-2 pr-4 text-gray-600">See exactly where your visibility is being lost — and why.</td>
            <td class="py-2 pr-4 text-gray-500">For businesses seeking clarity before committing</td>
            <td class="py-2 font-mono text-gray-400">.secondary</td>
          </tr>
          <tr>
            <td class="py-2 pr-4 font-mono text-gray-500">audit</td>
            <td class="py-2 pr-4 font-medium">Strategic Direction Session</td>
            <td class="py-2 pr-4 text-gray-600">Define the path to take control of your market position.</td>
            <td class="py-2 pr-4 text-gray-500">For operators ready to move with direction</td>
            <td class="py-2 font-mono text-amber-600">.featured</td>
          </tr>
          <tr>
            <td class="py-2 pr-4 font-mono text-gray-500">agency-review</td>
            <td class="py-2 pr-4 font-medium">Market Control Deployment</td>
            <td class="py-2 pr-4 text-gray-600">Deploy the system that reshapes how your business is found across markets.</td>
            <td class="py-2 pr-4 text-gray-500">For teams prepared to execute at scale</td>
            <td class="py-2 font-mono text-gray-600">.reserved</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p class="mt-3 text-xs text-amber-700 bg-amber-50 rounded px-3 py-2">⚠ After any BookingSeeder change, run <code class="font-mono">php artisan db:seed --class=BookingSeeder</code> manually on server. Deploy does not auto-seed.</p>
  </x-filament::section>

  {{-- 5. Tone --}}
  <x-filament::section>
    <x-slot name="heading">5. Tone &amp; Forbidden Language</x-slot>
    <div class="grid gap-4 md:grid-cols-2">
      <div>
        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-green-700">Approved positioning keywords</p>
        <ul class="space-y-1 text-xs text-gray-600">
          @foreach(['programmatic AI SEO system','structured deployment','system-level engagement','entry point','market position','market control','market clarity','territory','active markets','qualified operators','visibility','part of a system, not a standalone service'] as $kw)
          <li class="flex items-start gap-1.5"><span class="mt-0.5 text-green-500">✓</span> {{ $kw }}</li>
          @endforeach
        </ul>
      </div>
      <div>
        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-red-700">Forbidden patterns</p>
        <ul class="space-y-1 text-xs text-gray-600">
          @foreach(['revolutionary / game-changing / best-in-class','Limited time / Act now / Don\'t miss out','Starter / Pro / Enterprise / plan / subscribe','Guaranteed results','Most Popular / Best Value badges','Save X% / Special offer / Free trial','consultation / package / audit as primary label','Hey! / Super excited / Awesome','SEOAIco in any UI copy'] as $bad)
          <li class="flex items-start gap-1.5"><span class="mt-0.5 text-red-400">✗</span> {{ $bad }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  </x-filament::section>

  {{-- 6. Anti-Drift --}}
  <x-filament::section>
    <x-slot name="heading">6. Anti-Drift Rules</x-slot>
    <div class="grid gap-4 md:grid-cols-3">
      <div>
        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">Identity</p>
        <ul class="space-y-1 text-xs text-red-600">
          <li>✗ SEOAIco in any UI label or copy</li>
          <li>✗ Capitalising Co as CO</li>
          <li>✗ Adding . or ® after brand name</li>
          <li>✗ Splitting wordmark across lines</li>
        </ul>
      </div>
      <div>
        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">Visual</p>
        <ul class="space-y-1 text-xs text-red-600">
          <li>✗ Enlarging or coloring the ™</li>
          <li>✗ SaaS-style UI patterns</li>
          <li>✗ CSS @keyframes on SVG attributes</li>
          <li>✗ Entrance/exit transitions on page blocks</li>
          <li>✗ Text shadow on headings</li>
        </ul>
      </div>
      <div>
        <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-gray-500">Structure</p>
        <ul class="space-y-1 text-xs text-red-600">
          <li>✗ Exposing tier keys in public copy</li>
          <li>✗ New public sections without approval</li>
          <li>✗ Auto-seeding on deploy</li>
          <li>✗ Linking public pages to admin routes</li>
        </ul>
      </div>
    </div>
  </x-filament::section>

  {{-- 7. File Map --}}
  <x-filament::section>
    <x-slot name="heading">7. File Reference Map</x-slot>
    <div class="overflow-x-auto">
      <table class="w-full text-xs border-collapse">
        <thead>
          <tr class="border-b border-gray-200 text-left">
            <th class="py-2 pr-6 font-semibold text-gray-700">Surface</th>
            <th class="py-2 font-semibold text-gray-700">File</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach([
            ['Public booking page', 'resources/views/public/book.blade.php'],
            ['Booking modal / card UI', 'resources/views/components/booking-modal.blade.php'],
            ['Booking session types (DB)', 'database/seeders/BookingSeeder.php'],
            ['Onboarding start', 'resources/views/public/onboarding-start.blade.php'],
            ['Onboarding done', 'resources/views/public/onboarding-done.blade.php'],
            ['Email: step 2', 'resources/views/emails/onboarding-step2.blade.php'],
            ['Email: step 3', 'resources/views/emails/onboarding-step3.blade.php'],
            ['Stripe tier config', 'config/services.php'],
            ['Lead intelligence', 'app/Models/OnboardingSubmission.php'],
            ['Admin lead view', 'app/Filament/Resources/Leads/Pages/ViewLead.php'],
            ['Landing page', 'resources/views/public/landing.blade.php'],
          ] as [$surface, $file])
          <tr>
            <td class="py-2 pr-6 text-gray-700">{{ $surface }}</td>
            <td class="py-2 font-mono text-gray-500">{{ $file }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </x-filament::section>

</div>
</x-filament-panels::page>
