#!/bin/bash

# Setup Script untuk Progres SPT dengan Google Drive Integration
# Run: bash progres-setup.sh

echo "======================================"
echo "Progres SPT Setup Script"
echo "======================================"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "❌ .env file not found"
    echo "Please copy .env.example to .env first"
    exit 1
fi

echo "✓ .env file found"
echo ""

# Install dependencies
echo "📦 Installing dependencies..."
composer install
echo "✓ Dependencies installed"
echo ""

# Check if migrations exist
echo "🗄️ Running migrations..."
php artisan migrate
echo "✓ Migrations completed"
echo ""

# Check Google Drive setup
echo "🔍 Checking Google Drive setup..."
php artisan google-drive:check-setup

if [ $? -ne 0 ]; then
    echo ""
    echo "⚠️  Google Drive setup incomplete"
    echo ""
    echo "Please follow these steps:"
    echo "1. Visit: https://console.cloud.google.com/"
    echo "2. Create a project and enable Google Drive API"
    echo "3. Create a Service Account and download JSON credentials"
    echo "4. Create a folder in Google Drive for SPT Progress"
    echo "5. Share the folder with the service account email"
    echo "6. Update .env with:"
    echo "   - GOOGLE_DRIVE_CREDENTIALS_JSON (or GOOGLE_DRIVE_CREDENTIALS_PATH)"
    echo "   - GOOGLE_DRIVE_ROOT_FOLDER_ID"
    echo ""
    exit 1
fi

echo ""
echo "✅ Setup completed successfully!"
echo ""
echo "Next steps:"
echo "1. Run the application: php artisan serve"
echo "2. Visit: http://localhost:8000/progres"
echo "3. Start adding SPT entries"
echo ""
