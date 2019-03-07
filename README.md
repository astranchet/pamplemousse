Pamplemousse
=============

Requirements
------------

* mysql
* PHP 5.6+ with `ext-exif` and `imagick` extensions

Installation
------------

Run the following:

```bash
git clone https://github.com/astranchet/pamplemousse.git
cd pamplemousse
curl -s http://getcomposer.org/installer | php
php composer.phar install
cp config/app.yml.dist config/app.yml
```

Create database (see `app.yml` for parameters) then run migrations:
```bash
./bin/doctrine migrations:migrate
```

Settings
--------

Edit `config/app.yml`:
* Create upload dir (`web/upload` by default) 
* Configure `database` parameters
* Configure `users` (see Tools)
* Configure homepage with `site` parameters
* Configure photo filters with `kids` and `tags` parameters

Run locally
-----------

```sh
php -S localhost:8000 -t web .router.php
compass watch
```

* [Frontend](http://localhost:8000/)
* [Backend](http://localhost:8000/admin/) (login/pwd: admin/foo)

Migrations
-----------

Run migration on your server:
```bash
./bin/doctrine migrations:migrate
```

Generate migration template file with:

```bash
./bin/doctrine migrations:generate
```

Tools
-----

### Generate password

Generate encoded password:
```bash
./bin/generate-password
Password: foo
5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==%
```

### Generate thumbnails

Generate new thumbnails (after changing size for instance):
```bash
./bin/generate-thumbnails
```

Trouble shooting
----------------

__Display errrors__
- Set `debug:true` in `app.yml` and read `log/app.log`

__An exception occured in driver: SQLSTATE[HY000] [2002] No such file or directory__
- Database parameters in `app.yml` are incorrect. Sometimes, locally, `127.0.0.1` works better for host than `localhost`. 

__Error when uploading a photo in admin__
- Make sure upload dir exists.
- Make sure `ext-exif` and `imagick` PHP extensions are installed:
```bash
php --modules | grep imagick
php --modules | grep exif
```

Used components
---------------

## Frameworks

* Composer - http://getcomposer.org/
* Twig - http://twig.sensiolabs.org/
* SASS - http://sass-lang.com/install
* Compass - http://compass-style.org/

## Front
* Bootstrap - http://getbootstrap.com/
* HTML5 boilerplate - http://www.initializr.com/
* Photoswipe - https://photoswipe.com/
* Masonry - https://masonry.desandro.com/

## Admin
* Dropzone.js - http://www.dropzonejs.com/