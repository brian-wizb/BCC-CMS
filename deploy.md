Perfect. Since SSH is working now, here is the full production path from this point onward, with exact commands, what each step does, how to verify, and what to do if something fails.

**Phase 0: Confirm You Are Root On VPS**
1. Run:
~~~bash
whoami
hostname
pwd
~~~
2. Expected:
- whoami returns root
- hostname shows your server name
3. If not root:
- run su - or reconnect with ssh root@187.127.123.17

**Phase 1: Base Server Setup**
1. Update system packages:
~~~bash
apt update
apt upgrade -y
~~~
2. Install core packages:
~~~bash
apt install -y nginx mysql-server git unzip curl software-properties-common ca-certificates gnupg lsb-release
~~~
3. Add PHP repo and install PHP 8.2 stack:
~~~bash
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-intl
~~~
4. Install Composer:
~~~bash
cd /tmp
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer --version
~~~
5. Install Node.js 20 for Vite build:
~~~bash
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
node -v
npm -v
~~~

**Phase 2: MySQL Database Setup**
1. Create database and app user:
~~~bash
mysql -u root <<'SQL'
CREATE DATABASE bcc_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'bcc_user'@'localhost' IDENTIFIED BY 'Use_A_Strong_Unique_Password_Here!';
GRANT ALL PRIVILEGES ON bcc_cms.* TO 'bcc_user'@'localhost';
FLUSH PRIVILEGES;
SQL
~~~
2. Verify:
~~~bash
mysql -u root -e "SHOW DATABASES LIKE 'bcc_cms';"
mysql -u root -e "SELECT user,host FROM mysql.user WHERE user='bcc_user';"
~~~

**Phase 3: Pull Your Laravel Project**
1. Clone your repo:
~~~bash
mkdir -p /var/www
cd /var/www
git clone https://github.com/brian-wizb/BCC-CMS.git bcc-cms
cd /var/www/bcc-cms
~~~
2. If repository is private, use SSH clone instead:
~~~bash
git clone git@github.com:brian-wizb/BCC-CMS.git bcc-cms
~~~

**Phase 4: Configure Laravel Environment**
1. Create environment file and app key:
~~~bash
cd /var/www/bcc-cms
cp .env.example .env
php artisan key:generate
~~~
2. Edit environment:
~~~bash
nano .env
~~~
3. Set at minimum:
- APP_ENV=production
- APP_DEBUG=false
- APP_URL=https://bccoasisoflove.cloud
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=bcc_cms
- DB_USERNAME=bcc_user
- DB_PASSWORD=the exact DB password you created
- MAIL settings you actually use
- queue and cache settings as desired

4. Save and exit nano:
- Ctrl+O, Enter, Ctrl+X

**Phase 5: Install Dependencies And Build**
1. Install PHP dependencies:
~~~bash
cd /var/www/bcc-cms
composer install --no-dev --optimize-autoloader --no-interaction
~~~
2. Install JS dependencies and build assets:
~~~bash
npm ci
npm run build
~~~
3. Set writable permissions:
~~~bash
chown -R www-data:www-data /var/www/bcc-cms
chmod -R 775 /var/www/bcc-cms/storage
chmod -R 775 /var/www/bcc-cms/bootstrap/cache
~~~
4. Run migrations:
~~~bash
php artisan migrate --force
~~~
5. Optimize Laravel:
~~~bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
~~~

**Phase 6: Configure Nginx To Serve Laravel**
1. Create Nginx site config:
~~~bash
cat > /etc/nginx/sites-available/bcc-cms <<'NGINX'
server {
    listen 80;
    server_name bccoasisoflove.cloud www.bccoasisoflove.cloud;
    root /var/www/bcc-cms/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX
~~~
2. Enable new site and disable default:
~~~bash
ln -sf /etc/nginx/sites-available/bcc-cms /etc/nginx/sites-enabled/bcc-cms
rm -f /etc/nginx/sites-enabled/default
~~~
3. Test and reload Nginx:
~~~bash
nginx -t
systemctl reload nginx
~~~
4. Verify HTTP now serves your app:
~~~bash
curl -I http://bccoasisoflove.cloud
~~~

**Phase 7: Enable SSL (HTTPS)**
1. Install Certbot:
~~~bash
apt install -y certbot python3-certbot-nginx
~~~
2. Issue certificate:
~~~bash
certbot --nginx -d bccoasisoflove.cloud -d www.bccoasisoflove.cloud
~~~
3. Choose redirect to HTTPS when prompted.
4. Verify HTTPS:
~~~bash
curl -I https://bccoasisoflove.cloud
~~~
5. Test renewal timer:
~~~bash
systemctl status certbot.timer --no-pager
certbot renew --dry-run
~~~

**Phase 8: Queue Worker And Scheduler**
1. Scheduler cron:
~~~bash
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/bcc-cms && php artisan schedule:run >> /dev/null 2>&1") | crontab -
crontab -l
~~~
2. Install Supervisor:
~~~bash
apt install -y supervisor
~~~
3. Create worker config:
~~~bash
cat > /etc/supervisor/conf.d/bcc-cms-worker.conf <<'SUP'
[program:bcc-cms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/bcc-cms/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/bcc-cms/storage/logs/worker.log
stopwaitsecs=3600
SUP
~~~
4. Start worker:
~~~bash
supervisorctl reread
supervisorctl update
supervisorctl start bcc-cms-worker:*
supervisorctl status
~~~

**Phase 9: Production Deploy Script (Future Updates)**
1. Create deploy script:
~~~bash
cat > /var/www/bcc-cms/deploy.sh <<'BASH'
#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/var/www/bcc-cms"
BRANCH="main"

cd "$APP_DIR"

php artisan down || true

git fetch origin "$BRANCH"
git pull --ff-only origin "$BRANCH"

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci
npm run build

php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart || true

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

php artisan up

echo "Deploy completed at $(date)"
BASH

chmod +x /var/www/bcc-cms/deploy.sh
~~~
2. Use for every release:
~~~bash
cd /var/www/bcc-cms
./deploy.sh
~~~

**Phase 10: Security Hardening After Everything Works**
1. Create non-root admin user:
~~~bash
adduser deploy
usermod -aG sudo deploy
mkdir -p /home/deploy/.ssh
cp /root/.ssh/authorized_keys /home/deploy/.ssh/authorized_keys
chown -R deploy:deploy /home/deploy/.ssh
chmod 700 /home/deploy/.ssh
chmod 600 /home/deploy/.ssh/authorized_keys
~~~
2. Optional firewall:
~~~bash
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
ufw status
~~~
3. Disable root password SSH later:
- set PermitRootLogin prohibit-password
- set PasswordAuthentication no
- restart ssh

Do this only after deploy user key login is confirmed.

**Quick Health Checklist**
1. App loads on https://bccoasisoflove.cloud
2. Login works
3. Database read/write works
4. Queue jobs process
5. Scheduler runs tasks
6. SSL renew dry-run succeeds
7. Nginx and php-fpm are active
8. deploy.sh completes without errors

If you want, next I can walk with you live in strict order:
1. You run Phase 1 now.
2. Paste output only if an error appears.
3. I adapt commands immediately to your exact VPS state until fully live.
