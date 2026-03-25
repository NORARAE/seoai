<?php

namespace App\Http\Controllers;

use App\Mail\LicensingInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Show the public landing page
     */
    public function landing(): View
    {
        return view('public.landing');
    }

    /**
     * Handle the licensing enquiry form submission
     */
    public function storeLicensingInquiry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'type' => ['required', 'in:agency,business,both'],
            'tier' => ['required', 'in:starter,5k,10k,legacy'],
            'niche' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'website_url' => ['max:0'], // honeypot — must be empty
        ]);

        // Honeypot check — bots fill hidden fields
        if (! empty($validated['website_url'])) {
            return redirect(url('/').'#contact');
        }

        unset($validated['website_url']);

        $inquiry = array_merge($validated, [
            'ip' => $request->ip(),
        ]);

        $recipient = config('mail.from.address', 'hello@seoaico.com');

        Mail::to($recipient)->queue(new LicensingInquiry($inquiry));

        Log::info('Licensing enquiry submitted', [
            'company' => $inquiry['company'],
            'email' => $inquiry['email'],
            'tier' => $inquiry['tier'],
        ]);

        return redirect(url('/').'#contact')
            ->with('inquiry_success', 'Your enquiry has been submitted. We review every application individually and will be in touch within 1–2 business days.');
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }

    public function terms(): View
    {
        return view('public.terms');
    }
}
