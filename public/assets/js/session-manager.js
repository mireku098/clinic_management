// Session Management Utilities
class SessionManager {
    static getBaseUrl() {
        if (typeof window.appUrl === "function") {
            return window.appUrl("");
        }
        const meta = document.querySelector('meta[name="base-url"]');
        return meta ? meta.getAttribute("content").replace(/\/$/, "") : "";
    }

    static checkSession() {
        const maxAge = 8 * 60 * 60 * 1000; // 8 hours in milliseconds

        // Check if session is expired
        const lastActivity = sessionStorage.getItem("lastActivity");
        const now = Date.now();

        if (lastActivity && now - parseInt(lastActivity) > maxAge) {
            console.log("Session expired, clearing data");
            localStorage.clear();
            sessionStorage.clear();

            const loginUrl = typeof window.appUrl === "function"
                ? window.appUrl("/login")
                : this.getBaseUrl() + "/login";

            // Show user-friendly message
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: "warning",
                    title: "Session Expired",
                    text: "Your session has expired due to inactivity. Please login again.",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Login Again",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = loginUrl;
                    }
                });
            } else {
                alert("Session expired. Please login again.");
                window.location.href = loginUrl;
            }
            return false;
        }

        // Update last activity
        sessionStorage.setItem("lastActivity", now.toString());
        return true;
    }

    static extendSession() {
        sessionStorage.setItem("lastActivity", Date.now().toString());
    }

    static setupActivityMonitor() {
        // Update activity every minute
        setInterval(() => {
            this.extendSession();
        }, 60000); // 1 minute

        // Listen for user activity
        ["click", "keydown", "scroll", "mousemove"].forEach((event) => {
            document.addEventListener(event, this.extendSession, true);
        });
    }
}

// Initialize session monitoring
document.addEventListener("DOMContentLoaded", function () {
    SessionManager.setupActivityMonitor();

    // Check session on page load
    SessionManager.checkSession();
});

// Auto-extend session on AJAX requests
if (typeof window.$ !== "undefined") {
    window.$.ajaxSetup({
        beforeSend: function (xhr, settings) {
            SessionManager.extendSession();
        },
    });
}
