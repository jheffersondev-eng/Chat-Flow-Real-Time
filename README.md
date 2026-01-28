# Real-Time Chat Application

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.2-purple.svg)
![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)
![Next.js](https://img.shields.io/badge/Next.js-14-black.svg)
![TypeScript](https://img.shields.io/badge/TypeScript-5.3-blue.svg)

A production-ready, enterprise-level real-time chat application built with Laravel 11, Next.js 14, WebSockets, and modern best practices.

## 🌟 Features

### Core Features
- ✅ **Real-time Messaging** - WebSocket-powered instant messaging
- ✅ **User Authentication** - OAuth2 (Google, GitHub) + 2FA support
- ✅ **Advanced Search** - Elasticsearch-powered message and user search
- ✅ **Typing Indicators** - Real-time typing status
- ✅ **Message History** - Paginated conversation history
- ✅ **Read Receipts** - Track message read status
- ✅ **Group Chats** - Support for direct and group conversations

### Technical Features
- ✅ **Clean Architecture** - Domain-driven design with separation of concerns
- ✅ **Multi-layer Caching** - Redis-powered caching at backend and frontend
- ✅ **Queue System** - Asynchronous processing with Laravel Queues
- ✅ **AI Integration** - OpenAI-powered chatbot responses
- ✅ **Internationalization** - Multi-language support (i18n)
- ✅ **Dark Mode** - Full dark mode support
- ✅ **Responsive Design** - Mobile-first approach
- ✅ **Performance Optimized** - Code splitting, lazy loading, image optimization

### Security
- ✅ **SQL Injection Prevention** - Parameterized queries and ORM
- ✅ **XSS Protection** - Input sanitization and CSP headers
- ✅ **CSRF Protection** - Token-based CSRF protection
- ✅ **Data Encryption** - Sensitive data encryption at rest
- ✅ **Rate Limiting** - API rate limiting and DDoS protection
- ✅ **WCAG 2.1 Compliance** - Accessibility standards

## 🏗️ Architecture

### Backend Architecture
```
backend/
├── app/
│   ├── Domain/              # Domain models and business logic
│   │   ├── User/
│   │   │   └── Models/
│   │   └── Chat/
│   │       └── Models/
│   ├── Application/         # Application services
│   │   └── Services/
│   ├── Infrastructure/      # External integrations
│   └── Http/               # Controllers and middleware
│       └── Controllers/
│           └── Api/
├── database/
│   └── migrations/
├── routes/
│   ├── api.php
│   └── channels.php
├── config/
└── tests/
    ├── Feature/
    └── Unit/
```

### Frontend Architecture
```
frontend/
├── src/
│   ├── app/                # Next.js app directory
│   ├── components/         # React components
│   │   └── chat/
│   ├── hooks/             # Custom React hooks
│   ├── store/             # Zustand state management
│   ├── lib/               # Utilities and clients
│   └── styles/            # Global styles
└── tests/                 # E2E and unit tests
```

## 🚀 Tech Stack

### Backend
- **Framework**: Laravel 11
- **Language**: PHP 8.2+
- **Database**: PostgreSQL 15
- **Cache/Queue**: Redis 7
- **Search**: Elasticsearch 8.11
- **WebSockets**: Laravel WebSockets (Pusher protocol)
- **Authentication**: Laravel Sanctum + Fortify
- **Testing**: PHPUnit / Pest

### Frontend
- **Framework**: Next.js 14
- **Language**: TypeScript 5.3
- **Styling**: Tailwind CSS 3
- **State Management**: Zustand
- **Data Fetching**: TanStack Query
- **WebSockets**: Laravel Echo + Pusher JS
- **Testing**: Jest + Playwright
- **Animations**: Framer Motion

### DevOps
- **Containerization**: Docker + Docker Compose
- **Web Server**: Nginx
- **Performance Testing**: k6
- **API Documentation**: Swagger/OpenAPI

## 📋 Prerequisites

- Docker & Docker Compose
- Node.js 20+ (for local development)
- PHP 8.2+ (for local development)
- Composer (for local development)

## 🔧 Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Teste-Tecnico-Edulabzz-Toolzz
```

### 2. Configure Environment Variables

```bash
# Copy environment file
cp .env.example .env

# Edit .env with your configurations
# Add OAuth credentials, OpenAI API key, etc.
```

### 3. Start with Docker

```bash
# Build and start all services
docker-compose up -d

# Install backend dependencies
docker-compose exec php composer install

# Generate application key
docker-compose exec php php artisan key:generate

# Run migrations
docker-compose exec php php artisan migrate

# Install frontend dependencies
docker-compose exec frontend npm install
```

### 4. Access the Application

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost/api
- **API Documentation**: http://localhost/api/documentation
- **WebSockets**: ws://localhost:6001

## 🧪 Running Tests

### Backend Tests

```bash
# Run all tests
docker-compose exec php php artisan test

# Run with coverage
docker-compose exec php php artisan test --coverage --min=80

# Run specific test suite
docker-compose exec php php artisan test --filter ChatTest
```

### Frontend Tests

```bash
# Unit tests
docker-compose exec frontend npm test

# Unit tests with coverage
docker-compose exec frontend npm run test:coverage

# E2E tests
docker-compose exec frontend npm run test:e2e
```

### Performance Tests

```bash
# Install k6
# On Windows: choco install k6
# On Mac: brew install k6
# On Linux: sudo apt-get install k6

# Run performance tests
k6 run k6-performance-test.js
```

## 📚 API Documentation

API documentation is available via Swagger UI:

**URL**: http://localhost/api/documentation

### Key Endpoints

#### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/logout` - Logout user
- `GET /api/auth/{provider}/redirect` - OAuth redirect
- `GET /api/auth/{provider}/callback` - OAuth callback

#### Conversations
- `GET /api/conversations` - List conversations
- `POST /api/conversations` - Create conversation
- `GET /api/conversations/{id}/messages` - Get messages
- `POST /api/conversations/{id}/messages` - Send message
- `POST /api/conversations/{id}/typing` - Update typing status

#### Messages
- `PUT /api/messages/{id}` - Edit message
- `DELETE /api/messages/{id}` - Delete message
- `GET /api/search/messages` - Search messages

## 🔐 Security

### Authentication Flow
1. User registers or logs in (email/password or OAuth)
2. Optional 2FA verification
3. JWT token issued via Laravel Sanctum
4. Token used for API and WebSocket authentication

### Data Protection
- Passwords hashed with bcrypt/argon2
- Sensitive data encrypted at rest
- HTTPS enforced in production
- Security headers configured
- Rate limiting on all API endpoints

## 🌍 Internationalization

Supported languages:
- English (en)
- Portuguese (pt)
- Spanish (es)

Add new translations in:
- Backend: `resources/lang/`
- Frontend: `public/locales/`

## 🎨 Theming

The application supports light and dark modes:

- Toggle in user interface
- Automatically respects system preferences
- Persistent across sessions
- Smooth transitions with Framer Motion

## 📊 Performance

### Optimization Strategies

**Backend**:
- Query optimization with eager loading
- Redis caching for frequently accessed data
- Database indexing on key columns
- Queue-based background processing
- Connection pooling

**Frontend**:
- Code splitting with Next.js
- Image optimization with next/image
- Lazy loading of components
- Virtual scrolling for long lists
- Service Worker for offline support

### Expected Metrics
- API response time: < 200ms (p95)
- WebSocket latency: < 50ms
- Time to First Byte: < 100ms
- Lighthouse score: 90+

## 🤖 AI Chatbot

The application includes an AI-powered chatbot using OpenAI's GPT-4:

1. Configure OpenAI API key in `.env`:
   ```
   OPENAI_API_KEY=your-api-key
   ```

2. Mention `@bot` in any conversation
3. The bot will respond automatically using context from the conversation

## 🔄 CI/CD

### GitHub Actions Workflow (Example)

```yaml
name: CI/CD

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Run tests
        run: |
          docker-compose up -d
          docker-compose exec -T php php artisan test --coverage
          docker-compose exec -T frontend npm test
```

## 📈 Monitoring

### Logging
- Laravel logs: `backend/storage/logs/`
- Structured JSON logging
- Log levels: DEBUG, INFO, WARNING, ERROR, CRITICAL

### Metrics
- Performance metrics tracked via Redis
- WebSocket connection monitoring
- Queue job tracking

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 Code Quality

### Standards
- PSR-12 coding standard for PHP
- ESLint + Prettier for TypeScript
- PHPStan level 8
- TypeScript strict mode

### Pre-commit Hooks
```bash
# Install pre-commit hooks
npm install -g husky
husky install
```

## 🐛 Troubleshooting

### WebSocket Connection Issues
```bash
# Check if WebSocket server is running
docker-compose ps websockets

# View WebSocket logs
docker-compose logs -f websockets
```

### Database Connection Issues
```bash
# Check PostgreSQL status
docker-compose ps postgres

# Access PostgreSQL
docker-compose exec postgres psql -U chat_user -d chat_app
```

### Cache Issues
```bash
# Clear all caches
docker-compose exec php php artisan cache:clear
docker-compose exec php php artisan config:clear
docker-compose exec php php artisan route:clear
```

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Authors

- Technical Test for Edulabzz/Toolzz

## 🙏 Acknowledgments

- Laravel community
- Next.js team
- Open source contributors

## 📧 Support

For support, email support@example.com or open an issue on GitHub.

---

Made with ❤️ using Laravel and Next.js
