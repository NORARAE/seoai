@extends('layouts.app')

@section('title', 'Notification Preferences')

@section('content')
<div class="min-h-screen bg-[#090805] text-[#ede8de]">
  <style>
    .pref-module{border:1px solid rgba(200,168,75,.18);background:linear-gradient(155deg,rgba(22,19,13,.94) 0%,rgba(13,11,8,.97) 100%);box-shadow:0 8px 32px rgba(0,0,0,.42),inset 0 1px 0 rgba(255,255,255,.03);position:relative;overflow:hidden}
    .pref-module::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(200,168,75,.36),transparent);pointer-events:none}
    .pref-row{display:flex;align-items:flex-start;justify-content:space-between;gap:24px;padding:18px 0;border-bottom:1px solid rgba(200,168,75,.08)}
    .pref-row:last-child{border-bottom:none;padding-bottom:0}
    .pref-label{font-size:.84rem;color:#ede8de;font-weight:400;margin-bottom:3px}
    .pref-desc{font-size:.72rem;color:rgba(168,168,160,.72);line-height:1.5}
    /* Toggle switch */
    .pref-toggle{position:relative;display:inline-block;width:42px;height:24px;flex-shrink:0;margin-top:2px}
    .pref-toggle input{opacity:0;width:0;height:0;position:absolute}
    .pref-toggle .slider{position:absolute;cursor:pointer;inset:0;background:rgba(200,168,75,.12);border:1px solid rgba(200,168,75,.25);transition:background .25s,border-color .25s;border-radius:34px}
    .pref-toggle .slider::before{content:'';position:absolute;height:16px;width:16px;left:3px;bottom:3px;background:rgba(200,168,75,.5);border-radius:50%;transition:transform .25s,background .25s}
    .pref-toggle input:checked + .slider{background:rgba(200,168,75,.22);border-color:rgba(200,168,75,.6)}
    .pref-toggle input:checked + .slider::before{transform:translateX(18px);background:#c8a84b}
    .sys-btn-primary{display:inline-flex;align-items:center;justify-content:center;padding:10px 20px;font-size:.68rem;letter-spacing:.11em;text-transform:uppercase;border:1px solid rgba(200,168,75,.5);background:linear-gradient(135deg,#c8a84b,#e3c97f);color:#0a0805;font-weight:600;text-decoration:none;transition:filter .2s,transform .2s;cursor:pointer}
    .sys-btn-primary:hover{filter:brightness(1.04);transform:translateY(-1px)}
    .flash-banner{border:1px solid rgba(106,175,144,.38);background:rgba(106,175,144,.08);color:rgba(106,175,144,.95);font-size:.74rem;letter-spacing:.04em;padding:10px 14px;margin-bottom:20px}
  </style>

  <div class="mx-auto max-w-3xl px-6 py-8 lg:px-8">

    <div class="mb-6">
      <p class="text-xs uppercase tracking-[.22em] text-[#c8a84b]/80 mb-1">Settings</p>
      <h1 class="font-['Cormorant_Garamond'] text-3xl font-light text-[#ede8de]">Notification Preferences</h1>
      <p class="mt-1 text-sm text-[#a8a18f]">Control which emails you receive from SEO AI Co. Transactional messages (scan results, receipts) are always delivered.</p>
    </div>

    @if(session('status'))
      <div class="flash-banner rounded">{{ session('status') }}</div>
    @endif

    <div class="pref-module rounded-2xl p-6 lg:p-7">
      <form method="POST" action="{{ route('app.settings.notifications.update') }}">
        @csrf

        <div class="pref-row">
          <div>
            <p class="pref-label">Marketing &amp; promotional emails</p>
            <p class="pref-desc">Tips, offers, and announcements from the SEO AI Co team.</p>
          </div>
          <label class="pref-toggle">
            <input type="checkbox" name="email_marketing_opt_in" value="1"
              {{ $user->email_marketing_opt_in ? 'checked' : '' }}>
            <span class="slider"></span>
          </label>
        </div>

        <div class="pref-row">
          <div>
            <p class="pref-label">Product updates</p>
            <p class="pref-desc">New features, improvements, and platform changes.</p>
          </div>
          <label class="pref-toggle">
            <input type="checkbox" name="email_product_updates" value="1"
              {{ $user->email_product_updates ? 'checked' : '' }}>
            <span class="slider"></span>
          </label>
        </div>

        <div class="pref-row">
          <div>
            <p class="pref-label">Scan result notifications</p>
            <p class="pref-desc">Email alerts when a new SEO scan completes for your sites.</p>
          </div>
          <label class="pref-toggle">
            <input type="checkbox" name="email_scan_notifications" value="1"
              {{ $user->email_scan_notifications ? 'checked' : '' }}>
            <span class="slider"></span>
          </label>
        </div>

        <div class="mt-6 flex items-center gap-4">
          <button type="submit" class="sys-btn-primary rounded">Save Preferences</button>
          <a href="{{ route('app.dashboard') }}" class="text-xs text-[#a8a18f] hover:text-[#c8a84b] transition-colors">← Back to Dashboard</a>
        </div>
      </form>
    </div>

    <p class="mt-5 text-xs text-[rgba(140,133,117,.75)]">
      Transactional emails (scan results, receipts, booking confirmations) are always sent regardless of these settings.
      Need help? <a href="mailto:hello@seoaico.com" class="text-[#c8a84b] hover:text-[#e2c97d] transition-colors">hello@seoaico.com</a>
    </p>

  </div>
</div>
@endsection
