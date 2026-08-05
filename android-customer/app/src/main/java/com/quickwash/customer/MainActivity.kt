package com.quickwash.customer

import android.Manifest
import android.app.DownloadManager
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Bundle
import android.os.Environment
import android.webkit.CookieManager
import android.webkit.DownloadListener
import android.webkit.GeolocationPermissions
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.URLUtil
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.activity.OnBackPressedCallback
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout

class MainActivity : AppCompatActivity() {
    private data class NavigationItem(
        val container: LinearLayout,
        val icon: ImageView,
        val label: TextView,
        val path: String
    )

    private lateinit var webView: WebView
    private lateinit var refreshLayout: SwipeRefreshLayout
    private lateinit var navigationItems: List<NavigationItem>
    private var fileSelectionCallback: ValueCallback<Array<Uri>>? = null
    private var geolocationCallback: GeolocationPermissions.Callback? = null
    private var geolocationOrigin: String? = null
    private val filePicker = registerForActivityResult(ActivityResultContracts.StartActivityForResult()) { result ->
        val selectedFiles = WebChromeClient.FileChooserParams.parseResult(result.resultCode, result.data)
        fileSelectionCallback?.onReceiveValue(selectedFiles)
        fileSelectionCallback = null
    }
    private val locationPermissionLauncher = registerForActivityResult(ActivityResultContracts.RequestMultiplePermissions()) { permissions ->
        val granted = permissions[Manifest.permission.ACCESS_FINE_LOCATION] == true ||
            permissions[Manifest.permission.ACCESS_COARSE_LOCATION] == true
        geolocationCallback?.invoke(geolocationOrigin, granted, false)
        geolocationCallback = null
        geolocationOrigin = null
        if (!granted) Toast.makeText(this, "Location permission is needed for live order tracking", Toast.LENGTH_LONG).show()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        webView = findViewById(R.id.webView)
        refreshLayout = findViewById(R.id.refreshLayout)
        navigationItems = listOf(
            NavigationItem(findViewById(R.id.navDashboard), findViewById(R.id.navDashboardIcon), findViewById(R.id.navDashboardLabel), "/customer/dashboard"),
            NavigationItem(findViewById(R.id.navBookings), findViewById(R.id.navBookingsIcon), findViewById(R.id.navBookingsLabel), "/customer/bookings"),
            NavigationItem(findViewById(R.id.navTracking), findViewById(R.id.navTrackingIcon), findViewById(R.id.navTrackingLabel), "/customer/tracking"),
            NavigationItem(findViewById(R.id.navProfile), findViewById(R.id.navProfileIcon), findViewById(R.id.navProfileLabel), "/customer/profile")
        )
        navigationItems.forEach { item ->
            item.container.setOnClickListener { webView.loadUrl(BuildConfig.PORTAL_ROOT + item.path) }
        }

        webView.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            allowFileAccess = false
            allowContentAccess = true
            setGeolocationEnabled(true)
            mixedContentMode = WebSettings.MIXED_CONTENT_NEVER_ALLOW
            setSupportZoom(false)
            builtInZoomControls = false
            displayZoomControls = false
            loadWithOverviewMode = false
            useWideViewPort = false
            userAgentString = "$userAgentString QuickWashCustomer/1.0"
        }
        CookieManager.getInstance().apply {
            setAcceptCookie(true)
            setAcceptThirdPartyCookies(webView, true)
        }

        webView.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView, request: WebResourceRequest): Boolean {
                val uri = request.url
                val host = uri.host.orEmpty()
                val isQuickWashUrl = host.equals("quickwashsystem.com", ignoreCase = true) ||
                    host.endsWith(".quickwashsystem.com", ignoreCase = true)
                return if ((uri.scheme == "http" || uri.scheme == "https") && isQuickWashUrl) {
                    false
                } else {
                    runCatching { startActivity(Intent(Intent.ACTION_VIEW, uri)) }
                    true
                }
            }

            override fun onPageFinished(view: WebView, url: String) {
                refreshLayout.isRefreshing = false
                updateSelectedNavigation(Uri.parse(url).path.orEmpty())
            }
        }
        webView.webChromeClient = object : WebChromeClient() {
            override fun onGeolocationPermissionsShowPrompt(origin: String, callback: GeolocationPermissions.Callback) {
                val host = Uri.parse(origin).host.orEmpty()
                val trustedOrigin = host.equals("quickwashsystem.com", ignoreCase = true) ||
                    host.endsWith(".quickwashsystem.com", ignoreCase = true)
                if (!trustedOrigin) {
                    callback.invoke(origin, false, false)
                    return
                }

                val hasPermission = ContextCompat.checkSelfPermission(this@MainActivity, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED ||
                    ContextCompat.checkSelfPermission(this@MainActivity, Manifest.permission.ACCESS_COARSE_LOCATION) == PackageManager.PERMISSION_GRANTED
                if (hasPermission) {
                    callback.invoke(origin, true, false)
                    return
                }

                geolocationCallback?.invoke(geolocationOrigin, false, false)
                geolocationOrigin = origin
                geolocationCallback = callback
                locationPermissionLauncher.launch(arrayOf(
                    Manifest.permission.ACCESS_FINE_LOCATION,
                    Manifest.permission.ACCESS_COARSE_LOCATION
                ))
            }

            override fun onShowFileChooser(
                webView: WebView,
                filePathCallback: ValueCallback<Array<Uri>>,
                fileChooserParams: WebChromeClient.FileChooserParams
            ): Boolean {
                fileSelectionCallback?.onReceiveValue(null)
                fileSelectionCallback = filePathCallback
                return runCatching {
                    filePicker.launch(fileChooserParams.createIntent())
                    true
                }.getOrElse {
                    fileSelectionCallback = null
                    Toast.makeText(this@MainActivity, "No file picker is available", Toast.LENGTH_SHORT).show()
                    false
                }
            }
        }

        webView.setDownloadListener(DownloadListener { url, userAgent, contentDisposition, mimeType, _ ->
            val request = DownloadManager.Request(Uri.parse(url)).apply {
                setMimeType(mimeType)
                addRequestHeader("User-Agent", userAgent)
                addRequestHeader("Cookie", CookieManager.getInstance().getCookie(url))
                setTitle(URLUtil.guessFileName(url, contentDisposition, mimeType))
                setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED)
                setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS, URLUtil.guessFileName(url, contentDisposition, mimeType))
            }
            (getSystemService(Context.DOWNLOAD_SERVICE) as DownloadManager).enqueue(request)
            Toast.makeText(this, "Download started", Toast.LENGTH_SHORT).show()
        })

        refreshLayout.setColorSchemeResources(R.color.brand_blue)
        refreshLayout.setOnRefreshListener { webView.reload() }

        if (savedInstanceState == null) webView.loadUrl(BuildConfig.PORTAL_URL) else webView.restoreState(savedInstanceState)

        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                if (webView.canGoBack()) webView.goBack() else finish()
            }
        })
    }

    private fun updateSelectedNavigation(currentPath: String) {
        val activeColor = ContextCompat.getColor(this, R.color.brand_blue)
        val inactiveColor = ContextCompat.getColor(this, R.color.nav_inactive)
        navigationItems.forEach { item ->
            val isSelected = currentPath == item.path ||
                (item.path == "/customer/bookings" && currentPath.startsWith("/customer/bookings/"))
            item.container.isSelected = isSelected
            item.icon.setColorFilter(if (isSelected) activeColor else inactiveColor)
            item.label.setTextColor(if (isSelected) activeColor else inactiveColor)
        }
    }

    override fun onSaveInstanceState(outState: Bundle) {
        webView.saveState(outState)
        super.onSaveInstanceState(outState)
    }
}
