# How to deploy

This documentation is an example of how to deploy the application on a Alwaysdata server.

## Getting the server ready

First, we need to be able to log easily on the server:

```bash
ssh-keygen -C "<some comment about the key, such as your email>" -f ~/.ssh/id_rsa.pamplemousse
ssh-add ~/.ssh/id_rsa.pamplemousse
ssh-copy-id -i ~/.ssh/id_rsa.pamplemousse <alwaysdata-user>@ssh-<alwaysdata-project>.alwaysdata.net
```

Now, we can log on the server securly:

```bash
ssh <alwaysdata-user>@ssh-<alwaysdata-project>.alwaysdata.net
```

And make it ready:

```bash
mkdir pamplemousse.git
cd pamplemousse.git
git init --bare
```

Now, we can push code onto the server:

```bash
git remote add prod ssh://<alwaysdata-user>@ssh-<alwaysdata-project>.alwaysdata.net:/home/<alwaysdata-user>/pamplemousse.git
git push prod master
```

But the server still need some setup to be ready.

Let's install ImageMagick for a start:

```bash
cd ~
mkdir extensions
cd extensions
wget http://pecl.php.net/get/imagick
mv imagick imagick.tar.gz
tar xzvf imagick.tar.gz
cd imagick-3.4.0RC6
phpize
./configure
make
```

And add it to the php.ini file via the admin backend:

```
extension = exif.so;
extension = /home/<alwaysdata-project>/extensions/imagick-3.4.0RC6/modules/imagick.so;
```

## Automating deploy

We'll use hooks:

```bash
scp deploy/post-receive <alwaysdata-user>@ssh-<alwaysdata-project>.alwaysdata.net:/home/<alwaysdata-project>/pamplemousse.git/hooks/
```

## Setting up configuration

```bash
# htaccess file for Alwaysdata
scp deploy/.htaccess <alwaysdata-user>@ssh-<alwaysdata-project>.alwaysdata.net:/home/<alwaysdata-project>/pamplemousse/web/.htaccess
# Prod configuration
scp config/app.prod.yml <alwaysdata-user>@ssh-<alwaysdata-project>.alwaysdata.net:/home/<alwaysdata-project>/pamplemousse/config/app.yml
# Background image
scp web/images/bg.jpg <alwaysdata-user>@ssh-<alwaysdata-project>.alwaysdata.net:/home/<alwaysdata-project>/pamplemousse/web/images/bg.jpg
```

Some directories need to be created manually:
```
mkdir pamplemousse/web/{upload,thumbnail}
chmod 777 pamplemousse/web/{upload,thumbnail}
```

## Deploying

```bash
git push prod <branch>
```
