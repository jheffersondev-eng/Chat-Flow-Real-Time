<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Realizado com Sucesso</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 48px;
        }
        h1 {
            color: #1f2937;
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
        }
        p {
            color: #6b7280;
            text-align: center;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f3f4f6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-box h3 {
            color: #374151;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .token {
            background: #1f2937;
            color: #10b981;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            word-break: break-all;
            line-height: 1.6;
        }
        .user-info {
            display: grid;
            gap: 10px;
        }
        .user-info div {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: white;
            border-radius: 6px;
        }
        .user-info strong {
            color: #374151;
        }
        .user-info span {
            color: #6b7280;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        button {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-copy {
            background: #667eea;
            color: white;
        }
        .btn-copy:hover {
            background: #5568d3;
        }
        .btn-api {
            background: #10b981;
            color: white;
        }
        .btn-api:hover {
            background: #059669;
        }
        .copied {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .copied.show {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>Login Realizado com Sucesso!</h1>
        <p>Você está autenticado via {{ ucfirst($provider) }}</p>

        <div class="info-box">
            <h3>Informações do Usuário</h3>
            <div class="user-info">
                <div>
                    <strong>Nome:</strong>
                    <span>{{ $user->name }}</span>
                </div>
                <div>
                    <strong>Email:</strong>
                    <span>{{ $user->email }}</span>
                </div>
                <div>
                    <strong>Provider:</strong>
                    <span>{{ ucfirst($provider) }}</span>
                </div>
            </div>
        </div>

        <div class="info-box">
            <h3>Seu Token de Acesso</h3>
            <div class="token" id="token">{{ $token }}</div>
        </div>

        <div class="actions">
            <button class="btn-copy" onclick="copyToken()">📋 Copiar Token</button>
            <button class="btn-api" onclick="testAPI()">🚀 Testar API</button>
        </div>
    </div>

    <div class="copied" id="copied">Token copiado!</div>

    <script>
        function copyToken() {
            const token = document.getElementById('token').textContent;
            navigator.clipboard.writeText(token).then(() => {
                const copied = document.getElementById('copied');
                copied.classList.add('show');
                setTimeout(() => copied.classList.remove('show'), 2000);
            });
        }

        function testAPI() {
            const token = document.getElementById('token').textContent;
            window.open(`http://localhost/api/user?token=${token}`, '_blank');
        }
    </script>
</body>
</html>
