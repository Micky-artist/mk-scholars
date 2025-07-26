#!/bin/bash
set -e

echo "🚀 Starting MK Scholars Development Setup..."

# Check for Docker and Docker Compose
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose v2.x."
    exit 1
fi

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📄 Creating .env file..."
    cat > .env <<EOL
# Database Configuration
DB_HOST=db
DB_PORT=3306
DB_DATABASE=mkscholars
DB_USERNAME=mkscholars_app
DB_PASSWORD=password123
MYSQL_ROOT_PASSWORD=rootpassword123

# Application Settings
APP_NAME="MK Scholars"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
EOL
    echo "✅ Created .env file with default credentials"
else
    echo "✅ Using existing .env file"
fi

# Load environment variables
set -a
source .env
set +a

echo "🐳 Building and starting Docker containers..."
docker-compose down -v > /dev/null 2>&1 || true
docker-compose up -d --build

echo "⏳ Waiting for database to be ready..."
until docker-compose exec -T db mysqladmin ping -u root -p"$MYSQL_ROOT_PASSWORD" --silent; do
    echo "Waiting for database to be ready..."
    sleep 5
done

echo "💾 Initializing database..."
docker-compose exec -T db mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "
    CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\`;
    CREATE USER IF NOT EXISTS '$DB_USERNAME'@'%' IDENTIFIED BY '$DB_PASSWORD';
    GRANT ALL PRIVILEGES ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'%';
    FLUSH PRIVILEGES;
"

echo "📦 Importing database schema..."
docker-compose exec -T db mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < docker/db/init.sql

echo "🌱 Seeding database..."
docker-compose exec -T db mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < docker/db/seed.sql

echo -e "\n✨ Setup complete!"
echo ""
echo "🌐 Access the application at: http://localhost:8000"
echo "🔑 Admin credentials:"
echo "   Username: admin"
echo "   Password: password123"
echo ""
echo "🛠️  Useful commands:"
echo "   docker-compose up -d    # Start services"
echo "   docker-compose down     # Stop services"
echo "   docker-compose logs -f  # View logs"
echo -e "\n🎉 Happy coding! 🚀"
