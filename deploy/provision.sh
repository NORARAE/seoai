#!/usr/bin/env bash
# ──────────────────────────────────────────────────────
# SEOAIco — DigitalOcean Droplet Provisioning
# Run this ONCE after SSH-ing into a fresh Ubuntu 24.04 droplet
# Usage: ssh root@YOUR_IP 'bash -s' < deploy/provision.sh
# ──────────────────────────────────────────────────────
set -euo pipefail

APP_DIR="/var/www/seoai"
DOMAIN="seoaico.com"
PHP_VER="8.3"

echo "═══ SEOAIco Provisioning ═══"

# ── 1. System updates ──
echo "→ Updating system packages..."
apt-get update -qq && apt-get upgrade -y -qq

# ── 2. Install essentials ──
echo "→ Installing base packages..."
apt-get install -y -qq \
  nginx \
  certbot python3-certbot-nginx \
  git curl unzip zip \
  sqlite3 \
  supervisor \
  ufw \
  fail2ban

# ── 3. PHP 8.3 ──
echo "→ Installing PHP ${PHP_VER}..."
apt-get install -y -qq software-properties-common
add-apt-repository -y ppa:ondrej/php
apt-get update -qq
apt-get install -y -qq \
  php${PHP_VER}-fpm \
  php${PHP_VER}-cli \
  php${PHP_VER}-mbstring \
  php${PHP_VER}-xml \
  php${PHP_VER}-curl \
  php${PHP_VER}-sqlite3 \
  php${PHP_VER}-zip \
  php${PHP_VER}-bcmath \
  php${PHP_VER}-intl \
  php${PHP_VER}-gd \
  php${PHP_VER}-tokenizer \
  php${PHP_VER}-fileinfo

# ── 4. Composer ──
echo "→ Installing Composer..."
if ! command -v composer &>/dev/null; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# ── 5. Create app directory ──
echo "→ Setting up ${APP_DIR}..."
mkdir -p ${APP_DIR}
chown -R www-data:www-data ${APP_DIR}

# ── 6. Firewall ──
echo "→ Configuring firewall..."
ufw --force reset
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw allow 'Nginx Full'
ufw --force enable

# ── 7. Fail2ban ──
echo "→ Configuring fail2ban..."
systemctl enable fail2ban
systemctl start fail2ban

# ── 8. PHP-FPM tuning (for 512MB) ──
echo "→ Tuning PHP-FPM for low memory..."
PHP_POOL="/etc/php/${PHP_VER}/fpm/pool.d/www.conf"
sed -i 's/^pm = .*/pm = ondemand/' ${PHP_POOL}
sed -i 's/^pm.max_children = .*/pm.max_children = 8/' ${PHP_POOL}
sed -i 's/^;pm.process_idle_timeout = .*/pm.process_idle_timeout = 10s/' ${PHP_POOL}
sed -i 's/^;pm.max_requests = .*/pm.max_requests = 200/' ${PHP_POOL}

# Increase upload/post limits
PHP_INI="/etc/php/${PHP_VER}/fpm/php.ini"
sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 10M/' ${PHP_INI}
sed -i 's/^post_max_size = .*/post_max_size = 12M/' ${PHP_INI}
sed -i 's/^memory_limit = .*/memory_limit = 256M/' ${PHP_INI}

systemctl restart php${PHP_VER}-fpm

# ── 9. Swap (important for 512MB droplet) ──
echo "→ Creating 1GB swap..."
if [ ! -f /swapfile ]; then
  fallocate -l 1G /swapfile
  chmod 600 /swapfile
  mkswap /swapfile
  swapon /swapfile
  echo '/swapfile none swap sw 0 0' >> /etc/fstab
  sysctl vm.swappiness=10
  echo 'vm.swappiness=10' >> /etc/sysctl.conf
fi

# ── 10. Nginx config ──
echo "→ Writing Nginx config..."
cat > /etc/nginx/sites-available/seoai << 'NGINX'
server {
    listen 80;
    listen [::]:80;
    server_name seoaico.com www.seoaico.com;

    root /var/www/seoai/public;
    index index.php;

    charset utf-8;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/javascript image/svg+xml;
    gzip_min_length 256;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        # Tell PHP the connection is HTTPS so $request->isSecure() is correct
        # after certbot rewrites this block to sit behind SSL termination.
        fastcgi_param HTTPS on;
        fastcgi_hide_header X-Powered-By;
    }

    # Deny dotfiles (except .well-known for certbot)
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Livewire JS/CSS served by Laravel, not static files
    location ^~ /livewire {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # Cache static assets
    location ~* \.(css|js|ico|gif|jpe?g|png|svg|woff2?|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    client_max_body_size 12M;
}
NGINX

ln -sf /etc/nginx/sites-available/seoai /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

# ── 11. Supervisor for queue & scheduler ──
echo "→ Setting up Supervisor..."
cat > /etc/supervisor/conf.d/seoai.conf << 'SUPER'
[program:seoai-queue]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /var/www/seoai/artisan queue:work --queue=crawl,generation,publishing,default --tries=3 --sleep=3 --timeout=120 --memory=256 --max-time=3600
directory=/var/www/seoai
user=www-data
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
numprocs=1
startsecs=5
stopwaitsecs=60
stdout_logfile=/var/www/seoai/storage/logs/supervisor-queue.log
stderr_logfile=/var/www/seoai/storage/logs/supervisor-queue-error.log

[program:seoai-scheduler]
command=/bin/bash -c "while true; do /usr/bin/php /var/www/seoai/artisan schedule:run --no-interaction >> /dev/null 2>&1; sleep 60; done"
directory=/var/www/seoai
user=www-data
autostart=true
autorestart=true
stdout_logfile=/var/www/seoai/storage/logs/supervisor-scheduler.log
stderr_logfile=/var/www/seoai/storage/logs/supervisor-scheduler-error.log
SUPER

supervisorctl reread
supervisorctl update

echo ""
echo "═══════════════════════════════════════════════════"
echo "  ✓ Provisioning complete!"
echo "  Next steps:"
echo "    1. Deploy your code (see deploy/deploy.sh)"
echo "    2. Point DNS: seoaico.com → $(curl -s ifconfig.me)"
echo "    3. Run: certbot --nginx -d seoaico.com -d www.seoaico.com"
echo "═══════════════════════════════════════════════════"
