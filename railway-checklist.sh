#!/bin/bash
# Quick Railway Deployment Checklist Script

echo "========================================="
echo "🚀 Jabalega Admin - Railway Deployment"
echo "========================================="
echo ""

# Check if .env file exists
if [ ! -f .env ]; then
    echo "⚠️  .env file not found!"
    echo "📋 Creating .env from template..."
    cp .env.example .env
    echo "✅ .env created. Edit it with your Railway database credentials."
    echo ""
fi

# Check git repo
if [ ! -d .git ]; then
    echo "⚠️  Git repository not initialized!"
    echo "💡 Run these commands:"
    echo "   git init"
    echo "   git add ."
    echo "   git commit -m 'Initial commit - Jabalega Admin Panel'"
    echo ""
else
    echo "✅ Git repository found"
    echo ""
fi

# Check required files
echo "📁 Checking required files..."
files=("index.php" "api.php" "includes/config.php" "jabalega.sql" "Dockerfile" "railway.json")
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "   ✅ $file"
    else
        echo "   ❌ $file (MISSING)"
    fi
done
echo ""

# Display next steps
echo "========================================="
echo "📋 NEXT STEPS:"
echo "========================================="
echo ""
echo "1️⃣  Setup GitHub Repository"
echo "   - Create new repo at https://github.com/new"
echo "   - Push code: git push -u origin main"
echo ""
echo "2️⃣  Setup Railway Project"
echo "   - Login to https://railway.app"
echo "   - Create MySQL database"
echo "   - Copy DB credentials to .env"
echo ""
echo "3️⃣  Configure Environment Variables"
echo "   - Add to Railway Variables:"
echo "   - DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_NAME"
echo ""
echo "4️⃣  Import Database Schema"
echo "   - Connect to Railway MySQL"
echo "   - Import jabalega.sql"
echo ""
echo "5️⃣  Test Your Deployment"
echo "   - Open your Railway domain URL"
echo "   - Login with admin/admin"
echo "   - Change admin password immediately!"
echo ""
echo "========================================="
echo "📚 Documentation: See RAILWAY_DEPLOYMENT.md"
echo "========================================="
