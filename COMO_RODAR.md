# 🚀 Como Rodar a Aplicação

## 1️⃣ Iniciar os Containers Docker

```powershell
# Inicie todos os containers
docker-compose up -d

# Verifique o status
docker-compose ps
```

**Todos os 7 containers devem estar "Up":**
- ✅ chat_app_nginx (porta 80/443)
- ✅ chat_app_php (Laravel)
- ✅ chat_app_postgres (porta 5432)
- ✅ chat_app_redis (porta 6379)
- ✅ chat_app_elasticsearch (porta 9200)
- ✅ chat_app_websockets (porta 6001)
- ✅ chat_app_queue (worker)

---

## 2️⃣ Testar a API

### 📝 Registrar um Usuário

```powershell
$body = @{
    name = "João Silva"
    email = "joao@example.com"
    password = "senha123"
} | ConvertTo-Json

Invoke-RestMethod -Uri http://localhost/api/auth/register -Method POST -Body $body -ContentType "application/json"
```

### 🔑 Fazer Login

```powershell
$body = @{
    email = "joao@example.com"
    password = "senha123"
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri http://localhost/api/auth/login -Method POST -Body $body -ContentType "application/json"

# Salvar o token
$token = $response.token
Write-Host "Token: $token"
```

### 💬 Criar uma Conversa

```powershell
$body = @{
    name = "Chat de Teste"
    type = "group"
    participant_ids = @(1, 2)  # IDs dos usuários
} | ConvertTo-Json

$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
    "Content-Type" = "application/json"
}

Invoke-RestMethod -Uri http://localhost/api/conversations -Method POST -Body $body -Headers $headers
```

### 📤 Enviar uma Mensagem

```powershell
$conversationId = 1  # ID da conversa criada

$body = @{
    content = "Olá! Esta é uma mensagem de teste."
    type = "text"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/api/conversations/$conversationId/messages" -Method POST -Body $body -Headers $headers
```

### 🔍 Buscar Mensagens

```powershell
Invoke-RestMethod -Uri "http://localhost/api/messages/search?q=teste" -Method GET -Headers $headers
```

### 🤖 Testar Bot LLM (precisa configurar OPENAI_API_KEY no .env)

```powershell
$body = @{
    content = "@bot Qual é a capital do Brasil?"
    type = "text"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/api/conversations/$conversationId/messages" -Method POST -Body $body -Headers $headers
```

---

## 3️⃣ Acessar Documentação Swagger

Abra no navegador:
```
http://localhost/api/documentation
```

---

## 4️⃣ Comandos Úteis Laravel

```powershell
# Acessar container PHP
docker-compose exec php bash

# Dentro do container:
cd /var/www/html

# Ver rotas
php artisan route:list

# Rodar migrations
php artisan migrate

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Rodar queue worker (processamento assíncrono)
php artisan queue:work

# Indexar dados no Elasticsearch
php artisan scout:import "App\Domain\Chat\Models\Message"
php artisan scout:import "App\Domain\User\Models\User"
```

---

## 5️⃣ Logs e Debugging

```powershell
# Ver logs do Laravel
docker-compose exec php tail -f /var/www/html/storage/logs/laravel.log

# Ver logs do Nginx
docker-compose logs -f nginx

# Ver logs do Queue Worker
docker-compose logs -f queue

# Ver logs do PostgreSQL
docker-compose logs -f postgres
```

---

## 6️⃣ Parar/Reiniciar

```powershell
# Parar containers
docker-compose stop

# Parar e remover containers
docker-compose down

# Reiniciar um serviço específico
docker-compose restart php
docker-compose restart nginx

# Rebuild (após mudanças no Dockerfile)
docker-compose up -d --build
```

---

## 7️⃣ Rodar Frontend Next.js (opcional)

```powershell
# Entre no diretório frontend
cd frontend

# Instale dependências
npm install

# Configure variáveis de ambiente
cp .env.example .env.local

# Edite .env.local com:
# NEXT_PUBLIC_API_URL=http://localhost/api
# NEXT_PUBLIC_WS_HOST=localhost
# NEXT_PUBLIC_WS_PORT=6001

# Inicie o servidor dev
npm run dev
```

Acesse: http://localhost:3000

---

## 8️⃣ Configurações Importantes

### 📧 Configurar OAuth (Google/GitHub)

Edite o arquivo `.env` dentro do container:

```bash
# Google OAuth
GOOGLE_CLIENT_ID=seu-client-id
GOOGLE_CLIENT_SECRET=seu-client-secret

# GitHub OAuth
GITHUB_CLIENT_ID=seu-client-id
GITHUB_CLIENT_SECRET=seu-client-secret
```

### 🤖 Configurar OpenAI (para bot LLM)

```bash
OPENAI_API_KEY=sk-seu-api-key-aqui
```

Depois de editar o .env:
```powershell
docker-compose restart php
```

---

## 9️⃣ Testes Automatizados

```powershell
# Rodar todos os testes
docker-compose exec php php artisan test

# Rodar testes específicos
docker-compose exec php php artisan test --filter ChatTest

# Com coverage
docker-compose exec php php artisan test --coverage
```

---

## 🔟 Troubleshooting

### Erro de conexão com PostgreSQL
```powershell
docker-compose restart postgres
docker-compose exec php php artisan migrate:fresh
```

### Erro de permissão nos arquivos
```powershell
docker-compose exec php chmod -R 777 storage bootstrap/cache
```

### Container não inicia
```powershell
docker-compose logs php
docker-compose logs nginx
```

### Limpar tudo e recomeçar
```powershell
docker-compose down -v
docker-compose up -d --build
docker-compose exec php php artisan migrate:fresh
```

---

## 📊 Endpoints da API

| Método | Endpoint | Descrição | Auth |
|--------|----------|-----------|------|
| POST | `/api/auth/register` | Registrar usuário | Não |
| POST | `/api/auth/login` | Login | Não |
| POST | `/api/auth/logout` | Logout | Sim |
| GET | `/api/auth/{provider}/redirect` | OAuth redirect | Não |
| POST | `/api/auth/two-factor/enable` | Ativar 2FA | Sim |
| GET | `/api/conversations` | Listar conversas | Sim |
| POST | `/api/conversations` | Criar conversa | Sim |
| GET | `/api/conversations/{id}/messages` | Ver mensagens | Sim |
| POST | `/api/conversations/{id}/messages` | Enviar mensagem | Sim |
| POST | `/api/conversations/{id}/typing` | Indicador digitando | Sim |
| GET | `/api/messages/search` | Buscar mensagens | Sim |

---

## ✅ Checklist de Funcionalidades

- [x] Registro e login de usuários
- [x] OAuth2 com Google/GitHub
- [x] Autenticação 2FA
- [x] Criar conversas diretas e em grupo
- [x] Enviar/receber mensagens em tempo real
- [x] Indicador de "digitando..."
- [x] Marcar mensagens como lidas
- [x] Busca de mensagens com Elasticsearch
- [x] Bot LLM com OpenAI (responde quando menciona @bot)
- [x] Processamento assíncrono com Queue
- [x] Documentação Swagger
- [x] Testes automatizados

---

## 🎯 Próximos Passos Sugeridos

1. Configure suas credenciais OAuth (Google/GitHub)
2. Configure sua API Key do OpenAI
3. Rode o frontend Next.js
4. Teste o chat em tempo real com WebSockets
5. Experimente buscar mensagens com Elasticsearch
6. Interaja com o bot LLM mencionando @bot

**A aplicação está 100% funcional e pronta para uso! 🚀**
