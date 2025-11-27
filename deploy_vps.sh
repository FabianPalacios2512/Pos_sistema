#!/bin/bash

# Configuration
VPS_USER="root"
VPS_IP="72.61.65.31"
VPS_PASS="105codeF1001504182."
PROJECT_PATH="/var/www/pos_system"

echo "🚀 Starting Deployment to Hostinger VPS ($VPS_IP)..."

# 1. SSH Key Setup (Auto-login)
echo "🔑 Setting up SSH Key for passwordless login..."
if ! ssh -o BatchMode=yes -o ConnectTimeout=5 ${VPS_USER}@${VPS_IP} echo "SSH Connection OK" 2>/dev/null; then
    echo "   SSH Key not configured on server. Installing..."
    # Check if key exists, if not generate
    if [ ! -f ~/.ssh/id_rsa.pub ]; then
        echo "   Generating new SSH key..."
        ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa -N ""
    fi
    
    # Copy ID using sshpass
    sshpass -p "${VPS_PASS}" ssh-copy-id -o StrictHostKeyChecking=no ${VPS_USER}@${VPS_IP}
    echo "✅ SSH Key installed!"
else
    echo "✅ SSH Key already configured."
fi

# 2. Copy Setup Scripts
echo "📂 Copying setup scripts to VPS..."
scp deploy/provision.sh deploy/finalize_setup.sh ${VPS_USER}@${VPS_IP}:/root/

# 3. Run Provisioning (Install Software)
echo "🛠 Running Server Provisioning (This may take a while)..."
ssh ${VPS_USER}@${VPS_IP} "chmod +x /root/provision.sh && /root/provision.sh"

# 4. Sync Project Files
echo "📡 Syncing project files..."
# Create directory if not exists
ssh ${VPS_USER}@${VPS_IP} "mkdir -p ${PROJECT_PATH}"

# Rsync
rsync -avz --delete \
    --exclude 'node_modules' \
    --exclude 'vendor' \
    --exclude '.git' \
    --exclude '.env' \
    --exclude 'storage/*.key' \
    --exclude 'deploy' \
    . ${VPS_USER}@${VPS_IP}:${PROJECT_PATH}

# 5. Finalize Setup (Build & Migrate)
echo "🏁 Finalizing setup on server..."
ssh ${VPS_USER}@${VPS_IP} "chmod +x /root/finalize_setup.sh && /root/finalize_setup.sh"

echo "🎉 DEPLOYMENT COMPLETE!"
echo "🌍 Your site should be live at: http://${VPS_IP}"
