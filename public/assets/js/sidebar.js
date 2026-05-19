// Sidebar Component Manager
class SidebarManager {
  constructor() {
    this.currentPage = this.getCurrentPage();
  }

  // Get current page from URL
  getCurrentPage() {
    const path = window.location.pathname;
    const filename = path.substring(path.lastIndexOf("/") + 1);
    return filename || "dashboard.html";
  }

  // Load sidebar component
  async loadSidebar() {
    try {
      const response = await fetch("sidebar.html", {
        headers: {
          "Cache-Control": "no-cache",
        },
      });
      const sidebarHTML = await response.text();

      // Find sidebar placeholder and replace with loaded content
      const sidebarPlaceholder = document.getElementById("sidebar");
      if (sidebarPlaceholder) {
        sidebarPlaceholder.outerHTML = sidebarHTML;
        this.setActiveState();
      }
    } catch (error) {
      console.error("Error loading sidebar:", error);
    }
  }

  // Set active state for current page
  setActiveState() {
    const navLinks = document.querySelectorAll(".sidebar-nav .nav-link");
    navLinks.forEach((link) => {
      const href = link.getAttribute("href");
      if (href === this.currentPage) {
        link.classList.add("active");
      } else {
        link.classList.remove("active");
      }
    });
  }

  // Initialize sidebar
  init() {
    // Load sidebar when DOM is ready
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", () => this.loadSidebar());
    } else {
      this.loadSidebar();
    }
  }
}

// Initialize sidebar manager
const sidebarManager = new SidebarManager();
sidebarManager.init();

// Export for use in other files
window.SidebarManager = SidebarManager;
