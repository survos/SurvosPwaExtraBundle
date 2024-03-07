import {Controller} from '@hotwired/stimulus';

// /* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['online', 'offline']

    connect() {
        console.log('navigator status at connect', navigator.onLine);
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
    }

    disconnect() {
        window.removeEventListener("offline");
        window.removeEventListener("online");
    }

    onlineTargetConnected(element) {
        console.log(element);
        // let countryCode = element.innerText;
    }


    showOnline() {
        console.log('online');
        // go through all the targets and hide/show as appropriate
        if (this.hasOnlineTargets) {
            this.onlineTargets.foreach((t) => {
                    t.hidden = false;
                }
            );
        } else {
            // console.error('no language targets in ' + this.identifier);
        }
    }

    showOffline() {
        console.log('offline');
        // this.offlineTarget.hidden = false
        // this.onlineTarget.hidden = true
        // this.cleanupClasses();
        // this.colorTarget.classList.add('text-red-800', 'border-red-300', 'bg-red-50', 'dark:text-red-300', 'dark:border-red-800');
        // this.pillTarget.innerHTML = 'Offline';
        // this.descriptionTarget.innerHTML = 'You are currently offline. Please check your internet connection and try again.';
    }

}
