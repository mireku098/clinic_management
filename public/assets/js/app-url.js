/**
 * Prefix root-relative URLs with the application base path (e.g. /clinic-management).
 * Requires <meta name="base-url" content="..."> in the page head.
 */
(function () {
    function getBaseUrl() {
        const meta = document.querySelector('meta[name="base-url"]');
        return meta ? meta.content.replace(/\/$/, "") : "";
    }

    window.getAppBaseUrl = getBaseUrl;

    window.appUrl = function (path) {
        if (!path) {
            return getBaseUrl();
        }
        if (/^https?:\/\//i.test(path)) {
            return path;
        }
        const normalized = path.startsWith("/") ? path : "/" + path;
        return getBaseUrl() + normalized;
    };

    const originalFetch = window.fetch;
    window.fetch = function (input, init) {
        if (typeof input === "string" && input.startsWith("/") && !input.startsWith("//")) {
            input = window.appUrl(input);
        }
        return originalFetch.call(this, input, init);
    };

    const originalOpen = window.open;
    window.open = function (url, target, features) {
        if (typeof url === "string" && url.startsWith("/") && !url.startsWith("//")) {
            url = window.appUrl(url);
        }
        return originalOpen.call(window, url, target, features);
    };
})();
