{{--
    Cloudflare Turnstile Script Include
    Add once per page, ideally in <head> or before </body>.
    Only renders if TURNSTILE_SITE_KEY is configured.
--}}
@if(config('services.turnstile.site_key'))
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=onTurnstileLoad" async defer></script>
@endif
