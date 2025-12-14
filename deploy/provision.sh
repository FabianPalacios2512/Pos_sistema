#!/bin/bash

# Exit on error
set -e

# Variables
DB_NAME="pos_system"
DB_USER="pos_user"
DB_PASS="105codeF1001504182." # Using the same password for simplicity as requested
DOMAIN="72.61.65.31" # Using IP as domain for now

echo "🚀 Starting Server Provisioning..."

# 1. Update System
echo "📦 Updating system packages..."
export DEBIAN_FRONTEND=noninteractive
apt-get update && apt-get upgrade -y

# 2. Install Essential Tools
echo "🛠 Installing essential tools..."
apt-get install -y git curl unzip zip software-properties-common ufw supervisor

# 3. Install PHP 8.2
echo "🐘 Installing PHP 8.2..."
add-apt-repository ppa:ondrej/php -y
apt-get update
apt-get install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-intl php8.2-gd php8.2-common

# 4. Install Nginx
echo "🌐 Installing Nginx..."
apt-get install -y nginx

# 5. Install MySQL
echo "🗄 Installing MySQL..."
apt-get install -y mysql-server

# Configure MySQL
echo "⚙️ Configuring MySQL..."
mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# 6. Install Composer
echo "🎼 Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

# 7. Install Node.js & NPM
echo "📦 Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs

# 8. Configure Nginx
echo "🔧 Configuring Nginx..."
cat > /etc/nginx/sites-available/pos_system <<EOF
server {
    listen 80;
    server_name ${DOMAIN};

    # Frontend (Vue.js)
    location / {
        root /var/www/pos_system/dist;
        index index.html;
        try_files \$uri \$uri/ /index.html;
    }

    # Backend (Laravel API)
    location /api {
        alias /var/www/pos_system/backend/public;
        try_files $uri $uri/ @laravel;
    }

    location @laravel {
        rewrite ^/api/(.*)$ /index.php?/$1 last;
    }

    # Handle PHP for Backend
    location ~ \.php$ {
        root /var/www/pos_system/backend/public;
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable Site
ln -sf /etc/nginx/sites-available/pos_system /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl restart nginx

# 9. Setup Firewall
echo "🛡 Configuring Firewall..."
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

echo "✅ Server Provisioning Complete!"
