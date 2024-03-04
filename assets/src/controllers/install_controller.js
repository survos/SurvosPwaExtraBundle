import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['install', 'launch']

  connect() {
    console.log('checking installation status');
    let installPrompt = null;
    const installButton = this.installTarget;
    const launchElement = this.launchTarget;

    // https://stackoverflow.com/questions/41742390/javascript-to-check-if-pwa-or-mobile-web
    const isInStandaloneMode = () =>
        (window.matchMedia('(display-mode: standalone)').matches) || (window.navigator.standalone) || document.referrer.includes('android-app://');

    if (isInStandaloneMode()) {
      console.log("webapp is installed");
      launchElement.setAttribute("hidden", "hidden");
    }
    window.addEventListener("beforeinstallprompt", (event) => {
      event.preventDefault();
      installPrompt = event;
      installButton.removeAttribute("hidden");
      launchElement.setAttribute("hidden", "hidden");
    });

// main.js

    installButton.addEventListener("click", async () => {
      if (!installPrompt) {
        return;
      }
      const result = await installPrompt.prompt();
      console.log(`Install prompt was: ${result.outcome}`);
      disableInAppInstallPrompt();
    });

    function disableInAppInstallPrompt() {
      installPrompt = null;
      installButton.setAttribute("hidden", "");
    }
  }


  install() {
      console.log('install');
  }

disconnect() {
    window.removeEventListener("offline");
    window.removeEventListener("online");
  }

  showOnline() {
    console.log('online');
    this.offlineTarget.hidden = true
    this.onlineTarget.hidden = false

    // this.cleanupClasses();
    // this.colorTarget.classList.add('text-green-800', 'border-green-300', 'bg-green-50', 'dark:text-green-300', 'dark:border-green-800');
    // this.pillTarget.innerHTML = 'Online';
    // this.descriptionTarget.innerHTML = 'You are currently online. You can now continue using the application.';
  }

  showOffline() {
    console.log('offline');
    this.offlineTarget.hidden = false
    this.onlineTarget.hidden = true
    // this.cleanupClasses();
    // this.colorTarget.classList.add('text-red-800', 'border-red-300', 'bg-red-50', 'dark:text-red-300', 'dark:border-red-800');
    // this.pillTarget.innerHTML = 'Offline';
    // this.descriptionTarget.innerHTML = 'You are currently offline. Please check your internet connection and try again.';
  }

}
