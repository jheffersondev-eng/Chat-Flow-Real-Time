#!/bin/bash

# Script de instalação completa do Chat Application
# Laravel 10 + WebSockets + Clean Architecture

cd /var/www/html

echo "📦 Copiando .env configurado..."
cp /var/www/backend/.env .env

echo "🗄️ Configurando database no .env..."
sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=pgsql/' .env
sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=postgres/' .env
sed -i 's/# DB_PORT=3306/DB_PORT=5432/' .env
sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=chat_app/' .env
sed -i 's/# DB_USERNAME=root/DB_USERNAME=chat_user/' .env
sed -i 's/# DB_PASSWORD=/DB_PASSWORD=chat_password/' .env

echo "⚡ Configurando Redis, Elasticsearch, Broadcasting..."
cat >> .env << 'EOF'

BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PORT=6379

SCOUT_DRIVER=elasticsearch
SCOUT_QUEUE=false
ELASTICSEARCH_HOST=http://elasticsearch:9200

PUSHER_APP_ID=chat-app
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1

OPENAI_API_KEY=your-openai-key-here

GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret

GITHUB_CLIENT_ID=your-github-client-id
GITHUB_CLIENT_SECRET=your-github-client-secret
EOF

echo "📚 Configurando config/broadcasting.php para WebSockets..."
cat > config/broadcasting.php << 'EOF'
<?php

return [
    'default' => env('BROADCAST_DRIVER', 'null'),
    
    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'host' => env('PUSHER_HOST', '127.0.0.1'),
                'port' => env('PUSHER_PORT', 6001),
                'scheme' => env('PUSHER_SCHEME', 'http'),
                'encrypted' => true,
                'useTLS' => false,
                'cluster' => env('PUSHER_APP_CLUSTER'),
            ],
        ],
    ],
];
EOF

echo "🔍 Configurando config/scout.php..."
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"

echo "🚀 Rodando migrations..."
php artisan migrate

echo "✅ Setup completo!"
