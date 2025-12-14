#!/bin/bash

VPS_USER="root"
VPS_IP="72.61.65.31"
PROJECT_PATH="/var/www/pos_system"

echo "🔍 Fetching Laravel Logs from VPS..."
ssh ${VPS_USER}@${VPS_IP} "tail -n 50 ${PROJECT_PATH}/backend/storage/logs/laravel.log"

echo "---------------------------------------------------"
echo "🔍 Checking Storage Permissions..."
ssh ${VPS_USER}@${VPS_IP} "ls -la ${PROJECT_PATH}/backend/storage"

echo "---------------------------------------------------"
echo "🔍 Checking .env file existence..."
ssh ${VPS_USER}@${VPS_IP} "ls -la ${PROJECT_PATH}/backend/.env"

echo "---------------------------------------------------"
echo "🔍 Checking Payment Methods in DB..."
ssh ${VPS_USER}@${VPS_IP} "cd ${PROJECT_PATH}/backend && php artisan tinker --execute='dump(App\Models\PaymentMethod::count())'"
