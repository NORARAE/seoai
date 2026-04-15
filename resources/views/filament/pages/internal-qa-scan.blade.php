<x-filament-panels::page>
<div class="space-y-6">

  {{-- Warning banner --}}
  <div class="rounded-lg border border-amber-200/50 bg-amber-50/50 dark:border-amber-500/20 dark:bg-amber-900/10 p-4">
    <div class="flex items-start gap-3">
      <x-heroicon-o-shield-exclamation class="h-5 w-5 text-amber-600 dark:text-amber-400 mt-0.5 shrink-0" />
      <div>
        <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Internal QA Tool — Staff Only</p>
        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Scans created here are marked <code class="px-1 py-0.5 bg-amber-100 dark:bg-amber-800/30 rounded text-amber-700 dark:text-amber-300">is_internal=true, source=admin_bypass</code>. No payment is required. All activity is logged.</p>
      </div>
    </div>
  </div>

  {{-- Scan form --}}
  <x-filament::section>
    <x-slot name="heading">Run Internal Scan</x-slot>
    <x-slot name="description">Creates a paid Quick Scan record and runs the real scan pipeline. Emails suppressed by default.</x-slot>

    <form wire:submit="runScan">
      {{ $this->form }}

      <div class="mt-6">
        <x-filament::button type="submit" wire:loading.attr="disabled">
          <span wire:loading.remove wire:target="runScan">Run QA Scan</span>
          <span wire:loading wire:target="runScan">Scanning…</span>
        </x-filament::button>
      </div>
    </form>
  </x-filament::section>

  {{-- Results + test links --}}
  @if($this->lastScanId)
  <x-filament::section>
    <x-slot name="heading">Scan #{{ $this->lastScanId }} — {{ ucfirst($this->lastStatus ?? 'unknown') }}</x-slot>

    @if($this->lastScore !== null)
    <div class="mb-4">
      <span class="text-3xl font-light {{ $this->lastScore >= 70 ? 'text-green-500' : ($this->lastScore >= 40 ? 'text-amber-500' : 'text-red-500') }}">
        {{ $this->lastScore }}/100
      </span>
    </div>
    @endif

    <div class="space-y-2">
      <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Test Flow Links</p>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        @if($this->getResultUrl())
        <a href="{{ $this->getResultUrl() }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-sm hover:border-primary-300 dark:hover:border-primary-600 transition group">
          <x-heroicon-o-document-chart-bar class="h-4 w-4 text-gray-400 group-hover:text-primary-500" />
          <span>Result Page</span>
        </a>
        @endif

        @if($this->getDashboardUrl())
        <a href="{{ $this->getDashboardUrl() }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-sm hover:border-primary-300 dark:hover:border-primary-600 transition group">
          <x-heroicon-o-square-3-stack-3d class="h-4 w-4 text-gray-400 group-hover:text-primary-500" />
          <span>Dashboard (AI Scans)</span>
        </a>
        @endif

        @if($this->getOAuthTestUrl())
        <a href="{{ $this->getOAuthTestUrl() }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-sm hover:border-primary-300 dark:hover:border-primary-600 transition group">
          <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4 text-gray-400 group-hover:text-primary-500" />
          <span>OAuth Flow (scan_id={{ $this->lastScanId }})</span>
        </a>
        @endif

        <a href="{{ url('/quick-scan/status') }}?scan_id={{ $this->lastScanId }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 text-sm hover:border-primary-300 dark:hover:border-primary-600 transition group">
          <x-heroicon-o-arrow-path class="h-4 w-4 text-gray-400 group-hover:text-primary-500" />
          <span>Status API (JSON)</span>
        </a>
      </div>
    </div>
  </x-filament::section>
  @endif

</div>
</x-filament-panels::page>
