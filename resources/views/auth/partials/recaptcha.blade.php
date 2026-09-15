<div>
    @if (config('services.recaptcha.site_key'))
        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-size="compact"></div>
        @once
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endonce
    @else
        <p class="text-sm text-red-700" role="alert">Login verification is unavailable. Please contact support.</p>
    @endif
</div>
