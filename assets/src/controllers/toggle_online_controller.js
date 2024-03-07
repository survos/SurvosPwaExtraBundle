import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['online', 'offline']

    connect() {
        console.warn('navigator status at connect', navigator.onLine);
        if (navigator.onLine) {
            this.showOnline();
        } else {
            // this.showOffline();
            this.showOnline();
        }

        window.ononline = (event) => {
            console.log("You ARE now connected to the network.");
        };
        window.onoffline = (event) => {
            console.error("You are NOT connected to the network.");
        };

        window.addEventListener("offline", () => {
            this.showOnline();
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
        console.log('connecting an onlineTarget! set ' + element.id + ' style.display to ');
        element.style.display = navigator.onLine ? "block" : "none";

        // let countryCode = element.innerText;
    }


    showOnline() {
        const display = navigator.onLine ? "block" : "none";
        console.log('Fix targets ' + display);
        // go through all the targets and hide/show as appropriate
        console.error(this.hasOnlineTarget);
        if (this.hasOnlineTarget) {
            console.log(this.onlineTargets.length + ' online targets');
            this.onlineTargets.forEach((t) => {
                    console.log(t.id, display);
                    t.style.display = display;
                }
            );
        } else {
            console.error('no online targets in ' + this.identifier);
        }
    }

    showOffline() {
        console.log('fixing offline only elements');
        // this.offlineTarget.hidden = false
        // this.onlineTarget.hidden = true
        // this.cleanupClasses();
        // this.colorTarget.classList.add('text-red-800', 'border-red-300', 'bg-red-50', 'dark:text-red-300', 'dark:border-red-800');
        // this.pillTarget.innerHTML = 'Offline';
        // this.descriptionTarget.innerHTML = 'You are currently offline. Please check your internet connection and try again.';
    }

}
