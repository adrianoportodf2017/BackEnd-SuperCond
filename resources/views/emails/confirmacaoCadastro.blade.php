<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .email-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .logo {
            display: block;
            margin: 0 auto 40px;
            max-width: 200px;
        }

        .content-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .content-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            opacity: 0.05;
        }

        .welcome-title {
            color: #1e293b;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
        }

        .info-section {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .info-item {
            margin: 10px 0;
            color: #1e293b;
        }

        .info-label {
            font-weight: 600;
            color: #1e3a8a;
        }

        .status-alert {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #1e3a8a;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            color: #64748b;
            font-size: 14px;
        }

        .footer p {
            margin: 5px 0;
        }

        @media (max-width: 640px) {
            .email-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Sol & Mar" class="logo">
        
        <div class="content-card">
            @if($newAssociacao->tipo_associado === 'PF')
                <h1 class="welcome-title">Bem-vindo(a) à Sol & Mar!</h1>
                <p style="text-align: center; color: #64748b;">
                    Agradecemos por escolher nossos serviços para pessoa física.
                </p>
                
                <div class="info-section">
                    <div class="info-item">
                        <span class="info-label">Nome:</span> {{ $newAssociacao->nome }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">CPF:</span> {{ $newAssociacao->cpf }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">RG:</span> {{ $newAssociacao->rg }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span> {{ $newAssociacao->email }}
                    </div>
                </div>
            @else
                <h1 class="welcome-title">Bem-vindo(a) à Sol & Mar!</h1>
                <p style="text-align: center; color: #64748b;">
                    Agradecemos por escolher nossos serviços empresariais.
                </p>
                
                <div class="info-section">
                    <div class="info-item">
                        <span class="info-label">Razão Social:</span> {{ $newAssociacao->razao_social }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">CNPJ:</span> {{ $newAssociacao->cnpj }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Inscrição Estadual:</span> {{ $newAssociacao->inscricao_estadual }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span> {{ $newAssociacao->email_contato }}
                    </div>
                </div>
            @endif

            <div class="status-alert">
                <p style="margin: 0; color: #1e293b;">
                    <strong>Status do Cadastro:</strong> Em Análise<br>
                    Prazo estimado: 2-3 dias úteis
                </p>
            </div>
        </div>

        <div class="footer">
            <p>Em caso de dúvidas, entre em contato:</p>
            <p>Email: suporte@empresa.com.br</p>
            <p>Telefone: (11) 1234-5678</p>
            <p style="margin-top: 20px;">Cooperativa Sol & Mar de Turismo e Lazer</p>
            <p>CNPJ: 06.923.373/0001-10</p>
        </div>
    </div>
</body>
</html>