#!/bin/bash

VPS_USER="root"
VPS_IP="72.61.65.31"
PROJECT_PATH="/var/www/pos_system"

echo "🛠 Fixing Database on VPS..."

# Run Seeders
ssh ${VPS_USER}@${VPS_IP} "cd ${PROJECT_PATH}/backend && php artisan db:seed --class=PaymentMethodsSeeder --force"
ssh ${VPS_USER}@${VPS_IP} "cd ${PROJECT_PATH}/backend && php artisan db:seed --class=DefaultSupplierSeeder --force"
ssh ${VPS_USER}@${VPS_IP} "cd ${PROJECT_PATH}/backend && php artisan db:seed --class=CategorySeeder --force"

# Clear Cache
ssh ${VPS_USER}@${VPS_IP} "cd ${PROJECT_PATH}/backend && php artisan cache:clear && php artisan config:clear"

echo "✅ Database Fixes Applied!"
