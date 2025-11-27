#!/bin/bash

# Configuration
VPS_USER="root"
VPS_IP="72.61.65.31"
VPS_PASS="105codeF1001504182."

echo "🚀 Fixing Nginx Configuration on VPS..."

# 1. Copy the fixed Nginx config to the VPS
echo "📂 Copying Nginx config..."
scp deploy/nginx-site-fixed.conf ${VPS_USER}@${VPS_IP}:/etc/nginx/sites-available/pos_system

# 2. Restart Nginx
echo "🔄 Restarting Nginx..."
ssh ${VPS_USER}@${VPS_IP} "nginx -t && systemctl restart nginx"

echo "✅ Nginx configuration updated and restarted!"
