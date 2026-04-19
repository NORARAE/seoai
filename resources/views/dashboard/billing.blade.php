@extends('layouts.app')

@section('title', 'Activation Records')

@section('content')
<div class="min-h-screen bg-[#090805] text-[#ede8de]">
  <style>
    .sys-module{border:1px solid rgba(200,168,75,.18);background:linear-gradient(155deg,rgba(22,19,13,.94) 0%,rgba(13,11,8,.97) 100%);box-shadow:0 8px 32px rgba(0,0,0,.42),inset 0 1px 0 rgba(255,255,255,.03);position:relative;overflow:hidden}
    .sys-module::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.36),transparent);pointer-events:none}
    .sys-featured{border-color:rgba(200,168,75,.46);box-shadow:0 0 34px rgba(200,168,75,.14),0 16px 52px rgba(0,0,0,.58),inset 0 1px 0 rgba(255,255,255,.08);background:linear-gradient(155deg,rgba(28,24,16,.96) 0%,rgba(14,12,9,.98) 100%)}
    .sys-featured::after{content:'CURRENT STATE';position:absolute;top:14px;right:16px;padding:3px 9px;border:1px solid rgba(106,175,144,.42);background:rgba(106,175,144,.1);color:rgba(106,175,144,.95);font-size:.55rem;letter-spacing:.14em;text-transform:uppercase}
    .sys-featured .record-headline{font-size:1.56rem}
    .sys-featured .record-copy{color:#d2ccbe}
    .sys-featured .record-frame{border-color:rgba(200,168,75,.28);background:rgba(200,168,75,.03)}
    .sys-pill{display:inline-flex;align-items:center;padding:4px 10px;border:1px solid;font-size:.62rem;letter-spacing:.14em;text-transform:uppercase}
    .sys-pill-active{border-color:rgba(106,175,144,.38);color:rgba(106,175,144,.95);background:rgba(106,175,144,.08)}
    .sys-pill-completed{border-color:rgba(200,168,75,.35);color:rgba(200,168,75,.88);background:rgba(200,168,75,.07)}
    .sys-pill-pending{border-color:rgba(196,120,120,.35);color:rgba(196,120,120,.88);background:rgba(196,120,120,.08)}
    .sys-btn-primary{display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;font-size:.68rem;letter-spacing:.11em;text-transform:uppercase;border:1px solid rgba(200,168,75,.5);background:linear-gradient(135deg,#c8a84b,#e3c97f);color:#0a0805;font-weight:600;text-decoration:none;transition:filter .2s,transform .2s}
    .sys-btn-primary:hover{filter:brightness(1.04);transform:translateY(-1px)}
    .sys-btn-secondary{display:inline-flex;align-items:center;justify-content:center;padding:10px 14px;font-size:.66rem;letter-spacing:.1em;text-transform:uppercase;border:1px solid rgba(200,168,75,.25);background:rgba(200,168,75,.06);color:rgba(230,224,206,.92);text-decoration:none;transition:border-color .2s,background .2s}
    .sys-btn-secondary:hover{border-color:rgba(200,168,75,.45);background:rgba(200,168,75,.11)}
    .sys-btn-disabled{opacity:.45;cursor:not-allowed}
    .sys-meta{font-size:.68rem;color:rgba(159,153,136,.95);letter-spacing:.03em}
    .sys-meta strong{color:rgba(225,217,200,.95);font-weight:500}
    .sys-meta-low{font-size:.62rem;color:rgba(140,133,117,.85);letter-spacing:.04em}
    .record-kicker{font-size:.58rem;letter-spacing:.22em;text-transform:uppercase;color:rgba(200,168,75,.74)}
    .record-frame{border:1px solid rgba(200,168,75,.14);background:rgba(200,168,75,.015);padding:12px 12px 10px}
    .record-amount{font-size:.82rem;color:#f0e8d7;font-weight:500}
    .record-date{font-size:.72rem;color:#b4ad9a}
    .sys-modal-mask{position:fixed;inset:0;background:rgba(5,4,2,.74);backdrop-filter:blur(3px);z-index:120;display:none}
    .sys-modal-mask[data-open='true']{display:block}
    .sys-modal{max-width:860px;width:calc(100% - 40px);margin:6vh auto 0;padding:20px}
    .record-headline{font-family:'Cormorant Garamond',serif;font-size:1.44rem;line-height:1.1;color:#f3eddf}
    .record-copy{font-size:.75rem;line-height:1.48;color:#beb7a8}
    .record-actions{padding-top:2px}

    .cert-shell{position:relative;border:1px solid rgba(200,168,75,.22);background:linear-gradient(160deg,rgba(26,22,14,.92),rgba(13,11,8,.97));box-shadow:inset 0 0 0 1px rgba(200,168,75,.08)}
    .cert-shell::after{content:'';position:absolute;inset:10px;border:1px solid rgba(200,168,75,.1);pointer-events:none}
    .cert-seal{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border:1px solid rgba(200,168,75,.35);color:rgba(200,168,75,.82);font-size:.58rem;letter-spacing:.12em;text-transform:uppercase;background:rgba(200,168,75,.08)}
    .cert-rule{height:1px;background:linear-gradient(90deg,rgba(200,168,75,.02),rgba(200,168,75,.3),rgba(200,168,75,.02))}
    .quiet-support{font-size:.66rem;color:rgba(140,133,117,.8)}
  </style>
  <div class="mx-auto max-w-6xl px-6 py-8 lg:px-8">
    <section class="sys-module mb-5 rounded-2xl p-6 lg:p-7">
      <p class="mb-2 text-xs uppercase tracking-[0.22em] text-[#c8a84b]/80">Activation Records</p>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h1 class="text-3xl font-semibold leading-tight lg:text-4xl">System Activation Log</h1>
          <p class="mt-2 max-w-2xl text-sm text-[#a8a18f]">Official in-platform record of activated layers, completion state, and next recommended actions.</p>
        </div>
        @if($portalUrl)
          <a href="{{ $portalUrl }}" target="_blank" rel="noopener" class="sys-btn-secondary">Open Billing Portal</a>
        @else
          <span class="inline-flex items-center justify-center rounded-xl border border-[#c8a84b]/30 px-5 py-3 text-xs uppercase tracking-[0.12em] text-[#c8a84b]/75">Portal unavailable</span>
        @endif
      </div>
      <p class="mt-4 text-xs text-[#9f9988]">Support: <a class="text-[#c8a84b] hover:text-[#dfc477]" href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
    </section>

    <section class="space-y-2">
      <h2 class="mb-1 text-sm uppercase tracking-[0.2em] text-[#c8a84b]/75">System Log</h2>
      @forelse($payments as $idx => $payment)
        @php
          $badgeClass = $payment['status_badge'] === 'ACTIVATED'
            ? 'sys-pill-active'
            : ($payment['status_badge'] === 'COMPLETED' ? 'sys-pill-completed' : 'sys-pill-pending');
          $expansionAvailable = str_contains(strtolower((string) $payment['layer_unlocked']), 'baseline')
            ? 'Additional signal layers available.'
            : (str_contains(strtolower((string) $payment['layer_unlocked']), 'signal')
              ? 'Authority expansion pathways detected.'
              : 'Geographic signal expansion possible.');
        @endphp
        <article class="sys-module {{ $idx === 0 ? 'sys-featured lg:px-6 lg:py-5' : '' }} rounded-2xl px-4 py-4 lg:px-5 lg:py-4">
          <div class="grid gap-3 lg:grid-cols-[1.45fr_auto] lg:items-start">
            <div class="record-frame">
              <div class="mb-1 flex items-start justify-between gap-2">
                <span class="record-kicker">Activation</span>
                <span class="sys-pill {{ $badgeClass }}">{{ $payment['status_badge'] }}</span>
              </div>
              <p class="record-headline mb-1">{{ $payment['activation_type'] }}</p>
              <p class="record-copy mb-2">Layer Unlocked: {{ $payment['layer_unlocked'] }}. {{ $payment['system_description'] }}</p>
              <div class="mb-1 flex flex-wrap items-center gap-3">
                <span class="record-amount">{{ $payment['amount_label'] }}</span>
                <span class="record-date">{{ $payment['timestamp_label'] }}</span>
              </div>
              <div class="flex flex-wrap items-center gap-3 sys-meta-low">
                <span>{{ $payment['source_type'] }}</span>
                <span>Ref: {{ $payment['session_ref'] }}</span>
              </div>
            </div>
            <div class="record-actions flex flex-wrap items-center gap-2 lg:justify-end">
              <button
                type="button"
                class="sys-btn-primary"
                data-activation-open="details"
                data-type="{{ $payment['activation_type'] }}"
                data-status="{{ $payment['status_badge'] }}"
                data-amount="{{ $payment['amount_label'] }}"
                data-time="{{ $payment['timestamp_label'] }}"
                data-layer="{{ $payment['layer_unlocked'] }}"
                data-summary="{{ $payment['system_description'] }}"
                data-next="{{ $payment['next_action'] }}"
                data-next-label="{{ $payment['next_action_label'] }}"
                data-next-href="{{ $payment['next_action_href'] }}"
                data-explanation="{{ $payment['activation_explanation'] }}"
                data-session-ref="{{ $payment['session_ref'] }}"
                data-source="{{ $payment['source_type'] }}"
                data-receipt-state="{{ $payment['receipt_availability'] }}"
                data-system-change="{{ $payment['system_change'] }}"
                data-expansion="{{ $expansionAvailable }}"
                data-receipt="{{ $payment['receipt_url'] ?? '' }}"
              >
                View Details
              </button>
              @if($payment['receipt_url'])
                <a class="sys-btn-secondary" href="{{ $payment['receipt_url'] }}" target="_blank" rel="noopener">Download Receipt</a>
              @else
                <span class="sys-btn-secondary sys-btn-disabled">Download Receipt</span>
              @endif
            </div>
          </div>
        </article>
      @empty
        <div class="sys-module rounded-2xl p-8 text-center text-sm text-[#9c9788]">No activation records found yet.</div>
      @endforelse
    </section>
  </div>
</div>

<div class="sys-modal-mask" id="activationModal" data-open="false" role="dialog" aria-modal="true" aria-labelledby="activationRecordTitle">
  <div class="sys-module cert-shell sys-modal rounded-2xl">
    <div class="mb-3 flex items-start justify-between gap-4">
      <div class="min-w-0">
        <div class="mb-1 flex items-center gap-2">
          <span class="cert-seal">AR</span>
          <p class="text-[.62rem] uppercase tracking-[0.2em] text-[#c8a84b]/72">Official Activation Record</p>
        </div>
        <h3 id="activationRecordTitle" class="mt-1 text-2xl font-semibold text-[#f0eadb]">Activation Certificate</h3>
      </div>
      <button id="activationClose" type="button" class="sys-btn-secondary">Close</button>
    </div>

    <div class="cert-rule mb-3"></div>

    <div class="mb-3 rounded-xl border border-[#c8a84b]/20 bg-[#121008] p-4">
      <div class="grid gap-3 md:grid-cols-[1fr_auto_auto_auto] md:items-center">
        <div>
          <p class="text-[.6rem] uppercase tracking-[.18em] text-[#9f9988]">System Name</p>
          <p class="mt-1 text-lg text-[#efe8d8]" id="detailType">-</p>
        </div>
        <div>
          <p class="text-[.6rem] uppercase tracking-[.18em] text-[#9f9988]">Status</p>
          <p class="mt-1 text-sm text-[#efe8d8]" id="detailStatus">-</p>
        </div>
        <div>
          <p class="text-[.6rem] uppercase tracking-[.18em] text-[#9f9988]">Layer Unlocked</p>
          <p class="mt-1 text-sm text-[#efe8d8]" id="detailLayer">-</p>
        </div>
        <div>
          <p class="text-[.6rem] uppercase tracking-[.18em] text-[#9f9988]">Amount Paid</p>
          <p class="mt-1 text-sm text-[#efe8d8]" id="detailAmount">-</p>
        </div>
      </div>
    </div>

    <div class="mb-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
      <div class="rounded-xl border border-[#c8a84b]/15 bg-[#131008] p-3">
        <p class="text-[.62rem] uppercase tracking-[.15em] text-[#9f9988]">Date / Time</p>
        <p class="mt-1 text-sm text-[#efe8d8]" id="detailTime">-</p>
      </div>
      <div class="rounded-xl border border-[#c8a84b]/15 bg-[#131008] p-3">
        <p class="text-[.62rem] uppercase tracking-[.15em] text-[#9f9988]">Reference</p>
        <p class="mt-1 text-sm text-[#efe8d8]" id="detailSessionRef">-</p>
      </div>
      <div class="rounded-xl border border-[#c8a84b]/15 bg-[#131008] p-3">
        <p class="text-[.62rem] uppercase tracking-[.15em] text-[#9f9988]">Signal Source / Type</p>
        <p class="mt-1 text-sm text-[#efe8d8]" id="detailSource">-</p>
      </div>
      <div class="rounded-xl border border-[#c8a84b]/15 bg-[#131008] p-3">
        <p class="text-[.62rem] uppercase tracking-[.15em] text-[#9f9988]">Receipt</p>
        <p class="mt-1 text-sm text-[#efe8d8]" id="detailReceiptState">-</p>
      </div>
    </div>

    <div class="grid gap-3">
      <div class="rounded-xl border border-[#c8a84b]/15 bg-[#131008] p-3 sm:col-span-2">
        <p class="text-[.62rem] uppercase tracking-[.15em] text-[#9f9988]">System Extended</p>
        <p class="mt-1 text-sm text-[#d4cec0]" id="detailExplanation">-</p>
      </div>
      <div class="rounded-xl border border-[#c8a84b]/15 bg-[#131008] p-3 sm:col-span-2">
        <p class="text-[.62rem] uppercase tracking-[.15em] text-[#9f9988]">What Changed</p>
        <p class="mt-1 text-sm text-[#d4cec0]" id="detailSystemChange">-</p>
      </div>
      <div class="rounded-xl border border-[#c8a84b]/15 bg-[#131008] p-3 sm:col-span-2">
        <p class="text-[.62rem] uppercase tracking-[.15em] text-[#9f9988]">Next Recommended Action</p>
        <p class="mt-1 text-sm text-[#d4cec0]" id="detailNext">-</p>
      </div>
      <div class="rounded-xl border border-[#c8a84b]/15 bg-[#131008] p-3 sm:col-span-2">
        <p class="text-[.62rem] uppercase tracking-[.15em] text-[#9f9988]">Expansion Available</p>
        <p class="mt-1 text-sm text-[#d4cec0]" id="detailExpansion">-</p>
      </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-[#c8a84b]/15 pt-4">
      <div class="flex flex-wrap items-center gap-2">
        <a id="detailNextCta" class="sys-btn-primary" href="{{ url('/dashboard#ai-scans') }}">Next Recommended Step</a>
        <a id="detailReceipt" class="text-xs text-[#c8a84b] hover:text-[#dfc477]" href="#" target="_blank" rel="noopener">Download official receipt</a>
      </div>
      <span class="quiet-support">Support: <a class="text-[#a08f63] hover:text-[#c8a84b]" href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></span>
    </div>
  </div>
</div>

<script>
  (function () {
    const modal = document.getElementById('activationModal');
    const closeBtn = document.getElementById('activationClose');
    const receiptLink = document.getElementById('detailReceipt');
    const nextCta = document.getElementById('detailNextCta');

    function setText(id, value) {
      const node = document.getElementById(id);
      if (node) node.textContent = value || '-';
    }

    function openModal(dataset) {
      setText('detailType', dataset.type || '-');
      setText('detailStatus', dataset.status || '-');
      setText('detailAmount', dataset.amount || '-');
      setText('detailTime', dataset.time || '-');
      setText('detailLayer', dataset.layer || '-');
      setText('detailSessionRef', dataset.sessionRef || '-');
      setText('detailSource', dataset.source || '-');
      setText('detailReceiptState', dataset.receiptState || '-');
      setText('detailExplanation', dataset.explanation || dataset.summary || '-');
      setText('detailSystemChange', dataset.systemChange || '-');
      setText('detailNext', dataset.next || '-');
      setText('detailExpansion', dataset.expansion || 'Additional signal layers available.');

      nextCta.textContent = dataset.nextLabel || 'Next Recommended Step';
      nextCta.href = dataset.nextHref || '{{ url('/dashboard#ai-scans') }}';

      if (dataset.receipt) {
        receiptLink.href = dataset.receipt;
        receiptLink.style.pointerEvents = 'auto';
        receiptLink.style.opacity = '1';
      } else {
        receiptLink.href = '#';
        receiptLink.style.pointerEvents = 'none';
        receiptLink.style.opacity = '.45';
      }

      modal.dataset.open = 'true';
    }

    function closeModal() {
      modal.dataset.open = 'false';
    }

    document.querySelectorAll('[data-activation-open="details"]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        openModal(btn.dataset);
      });
    });

    closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (evt) {
      if (evt.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (evt) {
      if (evt.key === 'Escape' && modal.dataset.open === 'true') closeModal();
    });
  })();
</script>
@endsection
