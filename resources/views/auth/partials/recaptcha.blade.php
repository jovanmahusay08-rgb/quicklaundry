<div class="my-3 flex w-full justify-center overflow-x-auto">
    @if (config('services.recaptcha.site_key'))
        <div class="g-recaptcha inline-block" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-size="normal"></div>
        @once
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endonce
    @else
        <p class="text-center text-sm text-red-700" role="alert">Login verification is unavailable. Please contact support.</p>
    @endif
</div>

