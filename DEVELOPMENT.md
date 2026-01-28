# Development Setup Guide

This guide will help you set up the development environment for the Chat Application.

## Quick Start

### Option 1: Docker (Recommended)

```bash
# Clone repository
git clone <repo-url>
cd Teste-Tecnico-Edulabzz-Toolzz

# Copy environment file
cp .env.example .env

# Start all services
docker-compose up -d

# Install backend dependencies
docker-compose exec php composer install

# Generate app key
docker-compose exec php php artisan key:generate

# Run migrations
docker-compose exec php php artisan migrate --seed

# Install frontend dependencies
docker-compose exec frontend npm install

# Access application
# Frontend: http://localhost:3000
# Backend: http://localhost
# API Docs: http://localhost/api/documentation
```

### Option 2: Local Development

#### Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Update .env with your local database credentials
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=chat_app
# DB_USERNAME=your_user
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate --seed

# Start services
php artisan serve                    # API server
php artisan websockets:serve         # WebSocket server
php artisan queue:work              # Queue worker
```

#### Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Configure environment
cp .env.local.example .env.local

# Update .env.local
# NEXT_PUBLIC_API_URL=http://localhost:8000/api
# NEXT_PUBLIC_WS_URL=ws://localhost:6001

# Start development server
npm run dev

# Access at http://localhost:3000
```

## OAuth Configuration

### Google OAuth

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URI: `http://localhost/auth/google/callback`
6. Copy Client ID and Secret to `.env`:

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
```

### GitHub OAuth

1. Go to [GitHub Developer Settings](https://github.com/settings/developers)
2. Create new OAuth App
3. Set callback URL: `http://localhost/auth/github/callback`
4. Copy Client ID and Secret to `.env`:

```env
GITHUB_CLIENT_ID=your-client-id
GITHUB_CLIENT_SECRET=your-client-secret
```

## OpenAI Integration

1. Get API key from [OpenAI](https://platform.openai.com/)
2. Add to `.env`:

```env
OPENAI_API_KEY=your-api-key
OPENAI_MODEL=gpt-4
```

## Database Setup

### PostgreSQL

```sql
-- Create database
CREATE DATABASE chat_app;

-- Create user
CREATE USER chat_user WITH PASSWORD 'chat_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE chat_app TO chat_user;
```

### Elasticsearch

```bash
# Create indices
docker-compose exec php php artisan scout:import "App\Domain\Chat\Models\Message"
docker-compose exec php php artisan scout:import "App\Domain\User\Models\User"
```

## Testing

### Backend Tests

```bash
# Create test database
php artisan migrate --env=testing

# Run tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter=ChatTest
```

### Frontend Tests

```bash
# Unit tests
npm test

# E2E tests (requires running app)
npm run test:e2e

# Coverage report
npm run test:coverage
```

## IDE Setup

### VS Code Extensions

- PHP Intelephense
- Laravel Extra Intellisense
- ESLint
- Prettier
- Tailwind CSS IntelliSense
- Docker
- GitLens

### PhpStorm

1. Install Laravel Plugin
2. Configure PHP interpreter
3. Enable Blade syntax support
4. Configure database connections

## Debugging

### Backend (Xdebug)

Add to `docker-compose.yml`:

```yaml
php:
  environment:
    XDEBUG_MODE: develop,debug
    XDEBUG_CONFIG: client_host=host.docker.internal
```

### Frontend

```bash
# Use Chrome DevTools
# Or install React Developer Tools extension
```

## Common Issues

### Port Already in Use

```bash
# Change ports in docker-compose.yml
# Or stop conflicting services
```

### Permission Issues

```bash
# Fix storage permissions
docker-compose exec php chmod -R 777 storage bootstrap/cache
```

### WebSocket Connection Failed

```bash
# Check WebSocket server is running
docker-compose ps websockets

# Check firewall settings
# Ensure port 6001 is open
```

## Production Deployment

### Environment Variables

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use strong keys
APP_KEY=base64:...
JWT_SECRET=...

# Production database
DB_HOST=your-db-host
DB_DATABASE=production_db
DB_USERNAME=prod_user
DB_PASSWORD=strong_password

# Redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=strong_password

# HTTPS for WebSockets
PUSHER_SCHEME=https
```

### Optimization

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Build frontend
npm run build
```

### Security Checklist

- [ ] Change all default passwords
- [ ] Enable HTTPS/SSL
- [ ] Configure CORS properly
- [ ] Set up rate limiting
- [ ] Enable security headers
- [ ] Configure firewall rules
- [ ] Set up backup strategy
- [ ] Enable monitoring/logging
- [ ] Configure CSP headers
- [ ] Review and update dependencies

## Monitoring

### Logs

```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View WebSocket logs
docker-compose logs -f websockets

# View queue logs
docker-compose logs -f queue
```

### Performance

```bash
# Run performance tests
k6 run k6-performance-test.js

# Monitor with Laravel Telescope (dev only)
composer require laravel/telescope --dev
php artisan telescope:install
```

## Support

For issues or questions:
- Open an issue on GitHub
- Check documentation
- Contact: support@example.com
