<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #003366;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #f7941d;
            margin: 0;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 15px;
        }
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section {
            margin: 25px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        .section-title {
            color: #003366;
            font-size: 1.2em;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .steps {
            margin-left: 20px;
        }
        .steps li {
            margin-bottom: 12px;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #666;
        }
        .highlight {
            color: #003366;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Sol e Mar" class="logo">
        <h1>Sol e Mar</h1>
    </div>
    
    <div class="content">
        <h2>Prezado(a) Cliente,</h2>
        
        <p>Esperamos que esteja bem. Conforme solicitado, enviamos em anexo o seu contrato para validação e assinatura.</p>

        <div class="alert">
            <strong>Importante:</strong> O contrato deve ser assinado para sua validação legal. Você tem duas opções para realizar a assinatura:
        </div>

        <div class="section">
            <div class="section-title">Opção 1: Assinatura Digital via gov.br (Recomendado)</div>
            <p>A assinatura digital via gov.br é gratuita, tem validade jurídica e pode ser feita de forma rápida e segura. Siga os passos:</p>
            
            <ol class="steps">
                <li>Acesse o portal <a href="https://www.gov.br/governodigital/pt-br/assinatura-eletronica">gov.br</a></li>
                <li>Faça login com sua conta gov.br (caso não tenha, crie uma conta seguindo as instruções do site)</li>
                <li>No menu, selecione "Assinador gov.br"</li>
                <li>Faça upload do contrato em anexo</li>
                <li>Siga as instruções na tela para assinar o documento</li>
                <li>Após a assinatura, envie o contrato assinado digitalmente para o nosso email</li>
            </ol>

            <p><strong>Observação:</strong> Para utilizar a assinatura gov.br, certifique-se de que sua conta tenha nível Prata ou Ouro. <a href="https://www.gov.br/governodigital/pt-br/conta-gov.br/saiba-mais-sobre-os-niveis-da-conta-govbr">Saiba mais sobre os níveis da conta</a></p>
        </div>

        <div class="section">
            <div class="section-title">Opção 2: Assinatura com Reconhecimento de Firma em Cartório</div>
            <p>Caso prefira, você também pode:</p>
            <ol class="steps">
                <li>Imprimir o contrato anexo</li>
                <li>Assinar todas as vias</li>
                <li>Reconhecer firma da sua assinatura em cartório</li>
                <li>Enviar o documento físico para nosso endereço ou entregar pessoalmente</li>
            </ol>
        </div>

        <p class="highlight">Prazo para Assinatura: 5 dias úteis</p>

        <p>Em caso de dúvidas sobre o processo de assinatura ou sobre o contrato, não hesite em nos contatar através dos seguintes canais:</p>
        <ul>
            <li>Telefone: (XX) XXXX-XXXX</li>
            <li>WhatsApp: (XX) XXXXX-XXXX</li>
            <li>Email: contato@solemar.com.br</li>
        </ul>
    </div>

    <div class="footer">
        <p>Este é um email automático. Por favor, não responda.</p>
        <p>Sol e Mar - Tornando seus sonhos realidade</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>