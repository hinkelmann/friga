# Universidade Federal de Santa Maria - Núcleo de Tecnologia Educacional
# Script para instalação do sistema Friga 2.0
# Luiz Guilherme  <luizguilherme@nte.ufsm.br>
#
# Este script foi escrito para ser executado no Debian 10.x
#

#!/bin/bash

echo "Adicionando repositório"

apt-get -yqq install software-properties-common dirmngr  ca-certificates apt-transport-https  > /dev/null

wget -q https://packages.sury.org/php/apt.gpg -O- | apt-key add -
cat > /etc/apt/sources.list.d/php.list <<'EOF'
deb https://packages.sury.org/php/ buster main
EOF

apt-key adv --fetch-keys 'https://mariadb.org/mariadb_release_signing_key.asc'  > /dev/null
cat > /etc/apt/sources.list.d/mariadb.list <<'EOF'
deb [arch=amd64] http://sfo1.mirrors.digitalocean.com/mariadb/repo/10.5/debian buster main
deb-src http://sfo1.mirrors.digitalocean.com/mariadb/repo/10.5/debian buster main
EOF

echo "Atualizando pacotes"
apt-get -qq update  > /dev/null
apt-get -yqq upgrade  > /dev/null

echo 'mariadb-server-10.5 mysql-server/root_password password root' > /tmp/deb-conf-mariadb.txt
echo 'mariadb-server-10.5 mysql-server/root_password_again password root' >> /tmp/deb-conf-mariadb.txt
debconf-set-selections /tmp/deb-conf-mariadb.txt
rm -fr /tmp/deb-conf-mariadb.txt


echo "Instalando pacotes"
apt-get -yqq install  vim net-tools sudo p7zip-full curl whois  git htop nginx-full letsencrypt mariadb-server php-curl php7.4-soap php-cgi php-fpm php7.4-mysql php7.4-bz2 php7.4-cli  php7.4-xml php-pear php7.4-intl php7.4-xmlrpc php-imagick php7.4-zip php7.4-gd php7.4-mbstring php7.4-bcmath libnss-mdns exim4 ntpdate  > /dev/null

curl -sS https://getcomposer.org/installer | php -- --quiet --install-dir=/usr/local/bin --filename=composer

echo "Configurando arquivos do sistema"

#sed -i.bak '32,38 s/#//' /etc/bash.bashrc
#sed -i.bak '/^short_open_tag/c short_open_tag = On'              /etc/php/7.0/{cgi,cli,fpm}/php.ini
#sed -i.bak '/^post_max_size/c post_max_size = 2048M'             /etc/php/7.0/{cgi,cli,fpm}/php.ini
#sed -i.bak '/^upload_max_filesize/c upload_max_filesize = 2048M' /etc/php/7.0/{cgi,cli,fpm}/php.ini
#sed -i.bak '/^;date.timezone/c date.timezone = UTC'              /etc/php/7.0/{cgi,cli,fpm}/php.ini
#sed -i.bak '/^display_errors = Off/c display_errors = On'        /etc/php/7.0/{cgi,cli,fpm}/php.ini

cat << EOF > /etc/nginx/sites-available/default
server {

    listen     80;
    listen [::]:80;
    server_name   _;

    root /var/www/friga/web;
    error_log /var/www/friga/var/logs/nginx.error.log;
    access_log /var/www/friga/var/logs/nginx.access.log;
    rewrite ^/app\.php/?(.*)\$ /\$1 permanent;

    client_max_body_size 3072m;
    client_body_timeout 3000;
    client_header_timeout 3000;
    keepalive_timeout 38000s;

    proxy_read_timeout 3800s;
    proxy_connect_timeout 3800s;
    proxy_ignore_client_abort on;

    location / {
        index index.html app.php;
        try_files \$uri @rewriteapp;
    }
    location @rewriteapp {
        rewrite ^(.*)\$ /app.php/\$1 last;
    }
    location ~ ^/(app|app_dev|config)\.php(/|$) {
        fastcgi_send_timeout 3800s;
        fastcgi_read_timeout 3800s;
        fastcgi_buffers 8 128k;
        fastcgi_buffer_size 128k;

        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param  HTTPS off;
    }
}
EOF

echo "Configurando Banco de Dados"
mysql -uroot -proot -e "CREATE DATABASE friga;"
mysql -uroot -proot -e "CREATE USER friga@localhost IDENTIFIED BY 'friga';"
mysql -uroot -proot -e "GRANT ALL PRIVILEGES ON friga.* TO 'friga'@'localhost';"
mysql -uroot -proot -e "FLUSH PRIVILEGES;"

echo "Instalando Friga 2.0"
cd /var/www/friga/
php bin/console doctrine:schema:create
php bin/console doctrine:schema:update --force
php bin/console fos:user:create admin admin@admin admin
php bin/console fos:user:promote admin ROLE_ADMIN
php bin/console cache:clear --env=prod && chown www-data. /var/www/friga/var/ -R

echo "Inicializando serviços"
/etc/init.d/nginx      restart
/etc/init.d/php7.4-fpm restart
/etc/init.d/mysql      restart

#mkdir -p /var/www/friga && chown www-data. /var/www/
#curl -sS https://nte.ufsm.br/friga/instalador |bash -