import { Controller } from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ["install", "launch"];

  initialize() {
    super.initialize();
    this.installPrompt = null;
  }

  connect() {
    const installButton = this.installTarget;
    const launchElement = this.launchTarget;
    // This doesn't work for some reason
    // const onlineButton = this.onlineTarget;
    // Hide launch element for the moment
    launchElement.style.display = "none";

    if(localStorage.getItem('appInstalled') === 'true'){
      // app is installed hide install button
      installButton.style.display = "none";
    }

    // console.log(installButton,launchElement);

    // https://stackoverflow.com/questions/41742390/javascript-to-check-if-pwa-or-mobile-web
    // const isInStandaloneMode = () =>
    //   window.matchMedia("(display-mode: standalone)").matches ||
    //   window.navigator.standalone ||
    //   document.referrer.includes("android-app://");


    // if (isInStandaloneMode()) {
    //   console.log("webapp is installed");
    //   // launchElement.style.display = "block";
    //   // installButton.style.display = "none";
    // } else {
    //   // launchElement.setAttribute("hidden", "hidden");
    //   // launchElement.style.display = "none";
    //   // installButton.style.display = "block";
    //   console.log("webapp is NOT installed");
    // }
    window.addEventListener("beforeinstallprompt", (event) => {
      event.preventDefault();
      this.installPrompt = event;
      console.error("beforeinstallprompt", this.installPrompt);
      // installButton.removeAttribute("hidden");
      // launchElement.setAttribute("hidden", "hidden");
    });

    // main.js

    installButton.addEventListener("click", async () => {
      if (!this.installPrompt) {
        console.log("no installPrompt");
        return;
      }
      const result = await this.installPrompt.prompt();
      console.log(`Install prompt was: ${result.outcome}`);
      localStorage.setItem('appInstalled', 'true');
      installButton.style.display = "none";
      disableInAppInstallPrompt();
    });

    function disableInAppInstallPrompt() {
      console.log("disableInAppInstallPrompt");
      this.installPrompt = null;
      // installButton.setAttribute("hidden", "");
    }
  }

  install() {
    console.log("install");
  }

  disconnect() {
    // window.removeEventListener("offline");
    // window.removeEventListener("online");
  }

  showOnline() {
    console.log("online");
    this.offlineTarget.hidden = true;
    this.onlineTarget.hidden = false;

    // this.cleanupClasses();
    // this.colorTarget.classList.add('text-green-800', 'border-green-300', 'bg-green-50', 'dark:text-green-300', 'dark:border-green-800');
    // this.pillTarget.innerHTML = 'Online';
    // this.descriptionTarget.innerHTML = 'You are currently online. You can now continue using the application.';
  }

  showOffline() {
    console.log("offline");
    this.offlineTarget.hidden = false;
    this.onlineTarget.hidden = true;
    // this.cleanupClasses();
    // this.colorTarget.classList.add('text-red-800', 'border-red-300', 'bg-red-50', 'dark:text-red-300', 'dark:border-red-800');
    // this.pillTarget.innerHTML = 'Offline';
    // this.descriptionTarget.innerHTML = 'You are currently offline. Please check your internet connection and try again.';
  }
}
