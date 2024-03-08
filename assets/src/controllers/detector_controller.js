import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['online', 'offline']

  connect() {
    console.log('navigator status at connect', navigator.onLine);

    // https://github.com/gokulkrishh/demo-progressive-web-app/blob/master/js/offline.js
    document.addEventListener('DOMContentLoaded', (event) => {
      console.log('checking for navigator.online');
      //On initial load to check connectivity
      if (navigator.onLine) {
        this.showOnline();
      } else {
        this.showOffline();
      }

      window.ononline = (event) => {
        console.log("You ARE now connected to the network.");
      };
      window.onoffline = (event) => {
        console.error("You are NOT connected to the network.");
      };

      window.addEventListener("offline", () => {
        this.showOffline();
      });
      window.addEventListener("online", () => {
        this.showOnline();
      });

    });

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
