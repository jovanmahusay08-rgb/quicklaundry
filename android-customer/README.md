# QuickWash Customer Android app

This Kotlin Android app presents the existing customer portal as an installable mobile app. It supports authenticated cookies, pull-to-refresh, Android back navigation, GCash proof file selection, and receipt downloads.

## Configure and build

1. Open this `android-customer` directory in Android Studio (JDK 17).
2. In `app/build.gradle.kts`, replace `https://your-domain.example/customer/login` with the public HTTPS URL of the Laravel customer login.
3. Select **Build > Generate Signed Bundle / APK > APK** and create a signed release APK.
4. Rename the output to `quickwash-customer.apk`.
5. Copy it to `storage/app/releases/quickwash-customer.apk` in the Laravel project.

The public landing page provides the APK download button. The route returns a clear 404 until the release APK is copied into place.
