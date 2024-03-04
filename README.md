# PWA Extra Bundle

This bundle is a playground for ideas to improve https://github.com/Spomky-Labs/phpwa

The idea is that after these tools are developed they'll be incorporated into that the core bundle and this one won't be necessary.  

## Extra Features

Before generating the initial configuration, generate the icons.  Yes, this is a bit of a pain, but it's a requirement for the manifest.  A manifest is required to run offline.



To generate the initial PWA Yaml configuration, run 

```bash
bin/console survos:pwa:config
```




symfony new pwa-demo --webapp --php=8.2 && cd pwa-demo
composer config extra.symfony.allow-contrib true
composer req symfony/asset-mapper symfony/stimulus-bundle

bin/console importmap:require bootstrap
echo "import 'bootstrap/dist/css/bootstrap.min.css'" >> assets/app.js
bin/console importmap:require bootstrap-icons/font/bootstrap-icons.min.css
echo "import 'bootstrap-icons/font/bootstrap-icons.min.css'" >> assets/app.js

bin/console make:controller AppController
sed -i "s|Route('/app'|Route('/'|" src/Controller/AppController.php
sed -i "s|'app_app'|'app_homepage'|" src/Controller/AppController.php

cat > templates/app/index.html.twig <<END
{% extends 'base.html.twig' %}
{% block body %}
<div class="container">
<span class="bi bi-clock h1"></span>
<div {{ stimulus_controller('epoch') }}></div>

        <div class="text text-muted">
            created from {{ _self }} on {{ 'now'|date('U') }}
        </div>
    <a class="btn btn-primary">
        <span class="bi bi-download"></span>
        Install as PWA</a>
    </div>
{% endblock %}
END

cat > assets/controllers/epoch_controller.js <<END
import { Controller } from '@hotwired/stimulus';
export default class extends Controller {
connect() {
this.element.textContent = "epoch time is now " +  Date.now();
}
}
END

symfony server:start -d
symfony open:local

composer config minimum-stability dev
composer require --dev spomky-labs/phpwa
