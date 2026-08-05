plugins {
    id("com.android.application")
}

android {
    namespace = "com.quickwash.customer"
    compileSdk = 36

    defaultConfig {
        applicationId = "com.quickwash.customer"
        minSdk = 24
        targetSdk = 35
        versionCode = 2
        versionName = "1.0.1"

        buildConfigField("String", "PORTAL_URL", "\"https://quickwashsystem.com/customer/login\"")
    }

    buildTypes {
        release {
            isMinifyEnabled = false
            proguardFiles(getDefaultProguardFile("proguard-android-optimize.txt"), "proguard-rules.pro")
        }
    }
    buildFeatures { buildConfig = true }
    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }
}

dependencies {
    implementation("androidx.core:core-ktx:1.15.0")
    implementation("androidx.appcompat:appcompat:1.7.0")
    implementation("androidx.activity:activity-ktx:1.10.0")
    implementation("androidx.swiperefreshlayout:swiperefreshlayout:1.1.0")
}
