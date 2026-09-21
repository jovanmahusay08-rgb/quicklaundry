# Registration phone verification

New customers must verify a Philippine mobile number in the format `09XXXXXXXXX` before registration creates an account. Existing accounts are unaffected.

1. Register with [Semaphore](https://semaphore.co/) and obtain an API key. Add SMS credits and, optionally, configure an approved sender name. The integration uses the [OTP API](https://api.semaphore.co/docs).
2. Set `SEMAPHORE_API_KEY` in the production `.env`. Set `SEMAPHORE_SENDER_NAME` only if you have a sender name configured; otherwise leave it empty to use the provider default.
3. Set `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` to v2 checkbox keys allowed on `quickwashsystem.com`. The CAPTCHA protects requests to send SMS codes.
4. Deploy the changes and run `php artisan optimize:clear`. No database migration is required.
5. Open `/customer/register`, enter your own mobile number, complete CAPTCHA, click **Send OTP**, and enter the code received by SMS. Click **Verify code**, then submit the completed registration form.

Codes expire after five minutes, and successful verification remains valid until that same expiry. If other form fields fail validation, verification remains valid in the same browser session until expiry. Changing the phone number requires verification for the new number. Resending invalidates the previous code and verification. Creating an account consumes the verification.

The server stores only a hash of each OTP in its cache. Five incorrect attempts invalidate the code. Sending is limited to one request per minute and five per hour for each phone number, and five per minute and ten per hour for each IP. Delivery failures do not enable registration, and there is no development bypass or log-based SMS driver. Use shared cache and sessions if the app runs on multiple servers.

Automated tests fake Google and Semaphore requests; they do not send SMS or prove live delivery. Test real delivery on the production domain after configuring the provider.
