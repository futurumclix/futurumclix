# -*- mode: ruby -*-
# vi: set ft=ruby :

$provision = <<SCRIPT
sudo apt-get update
DEBIAN_FRONTEND=noninteractive apt-get -y upgrade
sudo apt-get install php5.6-bcmath
sudo rm -rf /var/www/html
sudo ln -s /vagrant/app/webroot /var/www/html
echo | sudo tee /etc/nginx/sites-available/default <<'CONFIGURATION'
server {
 listen      80;
 server_name futurumclix.local;
 access_log  /vagrant/access.log;
 error_log   /vagrant/error.log;
 rewrite_log on;
 root        /vagrant/app/webroot;
 index       index.php index.html index.htm;

 if (!-e $request_filename) {
     rewrite ^/(.+)$ /index.php last;
     break;
 }

 location ~ \.php$ {
     fastcgi_pass   unix:/var/run/php/php5.6-fpm.sock;
     fastcgi_index  index.php;
     fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
     fastcgi_intercept_errors on;
     include        fastcgi_params;
 }

 location ~* \favicon.ico$ {
     access_log off;
     expires 1d;
     add_header Cache-Control public;
 }

 location ~ ^/(img|cjs|ccss)/ {
     access_log off;
     expires 7d;
     add_header Cache-Control public;
 }

 location ~ /(\.ht|\.git|\.svn) {
     deny  all;
 }
}
CONFIGURATION
sudo /etc/init.d/nginx reload
export CAKEVERSION=2.10.6
wget -nv "https://github.com/cakephp/cakephp/archive/$CAKEVERSION.zip" -O "/vagrant/$CAKEVERSION.zip"
(cd /vagrant && unzip -n "$CAKEVERSION.zip")
rm -rf "/vagrant/$CAKEVERSION.zip"
cp -R "/vagrant/cakephp-$CAKEVERSION/lib" "/vagrant/"
cp -R "/vagrant/cakephp-$CAKEVERSION/.htaccess" "/vagrant/"
rm -rf "/vagrant/app/tmp" "/vagrant/cakephp-$CAKEVERSION"
ln -s /tmp /vagrant/app/tmp
mkdir -p "/vagrant/app/Media/Banners/Cache"
touch "/vagrant/app/Config/core.ini.php" "/vagrant/app/Config/database.ini.php"
echo "127.0.0.1 futurumclix.local" | sudo tee -a /etc/hosts
sudo usermod -a -G vagrant www-data
SCRIPT

Vagrant.configure("2") do |config|
  config.vm.box = "loadsys/cakephp-nginx-php5.6-dev"
  config.vm.network :private_network, ip: "192.168.68.8"
  config.vm.provision "shell", inline: $provision
end
