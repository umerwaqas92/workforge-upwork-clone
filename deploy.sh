#!/usr/bin/env bash

# WorkForge One-Click Deploy Script for InfinityFree
# Usage: ./deploy.sh [DATABASE_NAME]

set -e

DB_NAME="${1:-if0_42654988_workforge}"

echo "========================================================"
echo "⚡ WorkForge Deployment Preparation"
echo "========================================================"
echo "Target Host:    ftpupload.net (InfinityFree)"
echo "FTP User:       if0_42654988"
echo "MySQL Host:     sql301.infinityfree.com"
echo "MySQL Database: $DB_NAME"
echo "========================================================"

# 1. Build frontend assets
echo "📦 Step 1: Building frontend assets with Vite..."
npm run build

# 2. Prepare production .env for InfinityFree
echo "⚙️ Step 2: Preparing production .env file..."
cat <<EOF > .env
APP_NAME="WorkForge"
APP_ENV=production
APP_KEY=$(grep '^APP_KEY=' .env | cut -d '=' -f2- || echo "base64:7B5hWf8s24n9MvT0hQ9hC1v9kM1v0==")
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=http://yourdomain.infinityfreeapp.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

LOG_CHANNEL=stack
LOG_LEVEL=error

# InfinityFree MySQL Database Connection
DB_CONNECTION=mysql
DB_HOST=sql301.infinityfree.com
DB_PORT=3306
DB_DATABASE=$DB_NAME
DB_USERNAME=if0_42654988
DB_PASSWORD=2UwmsMo2RskP

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Email Configuration (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=um.waqas.khan@gmail.com
MAIL_PASSWORD=mcxgzewqpglqfqzu
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=um.waqas.khan@gmail.com
MAIL_FROM_NAME="WorkForge Marketplace"

# Dodo Payments (Merchant of Record)
DODO_PAYMENTS_API_KEY=GW2OYhBxgOoc4O4T.oULE8Zq8yUondBbJy-sZBJ_4eROE5fdkg3dAP07f35sA4kXx
DODO_PAYMENTS_ENVIRONMENT=test_mode
DODO_PAYMENTS_WEBHOOK_KEY=

VITE_APP_NAME="WorkForge"
EOF

echo "✓ Production .env created with MySQL configuration!"

# 3. Trigger FTP deployment
echo "🚀 Step 3: Uploading files to InfinityFree via FTP..."
php scripts/deploy-ftp.php

echo ""
echo "========================================================"
echo "🎉 DEPLOYMENT COMPLETE!"
echo "========================================================"
echo "Next Step on InfinityFree:"
echo "1. Create the MySQL Database '$DB_NAME' in your InfinityFree Control Panel (if not done already)."
echo "2. Visit the Web Installer in your browser to run database migrations:"
echo "   http://yourdomain.infinityfreeapp.com/installer.php?secret=workforge2026"
echo "========================================================"
