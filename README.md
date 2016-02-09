Pamplemousse
=============

Requirements
------------

* PHP 5.6
* ImageMagick
* mysql

Components
----------

* Composer - http://getcomposer.org/
* Twig - http://twig.sensiolabs.org/
* Bootstrap - http://getbootstrap.com/
* HTML5 boilerplate - http://www.initializr.com/
* SASS - http://sass-lang.com/install
* Compass - http://compass-style.org/
* Dropzone.js - http://www.dropzonejs.com/

Installation
------------

Run the following:

```bash
git clone https://github.com/astranchet/pamplemousse.git .
cd pamplemousse
curl -s http://getcomposer.org/installer | php
php composer.phar install
cp config/app.yml.dist config/app.yml
```

Run locally
-----------

```sh
php -S localhost:8000 -t web .router.php
compass watch
```

Run migrations
--------------

```bash
./bin/doctrine migrations:migrate
```

Generate migration
------------------

```bash
./bin/doctrine migrations:generate
```
