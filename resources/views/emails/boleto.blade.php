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
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .details {
            margin: 20px 0;
        }
        .detail-row {
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
<!DOCTYPE html>
<div class="container p-4" style="max-width: 800px; margin: auto;">
    <div class="card border-0 shadow">
        <div class="card-header bg-primary text-white text-center py-4" style="background-color: #003366 !important;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mb-3" style="text-align: center; max-height: 80px;">
            <h1 class="h3" style="color: #f7941d;"> Sol e Mar!</h1>
        </div>

        <div class="card-body p-4">
            <div class="mb-4">
                <h2 class="h4 text-primary mb-3" style="color: #003366;">Prezado(a)</h2>
    
    <div class="content">
     
        
        <p>Seu boleto foi gerado com sucesso. Confira os detalhes abaixo:</p>
        
        <div class="details">
            <div class="detail-row">
                <strong>Título:</strong> {{ $boleto->title }}
            </div>
            <div class="detail-row">
                <strong>Valor:</strong> R$ {{ number_format($boleto->price, 2, ',', '.') }}
            </div>
            <div class="detail-row">
                <strong>Vencimento:</strong> {{ \Carbon\Carbon::parse($boleto->date_vue)->format('d/m/Y') }}
            </div>
            <div class="detail-row">
                <strong>Status:</strong> {{ ucfirst($boleto->status) }}
            </div>
        </div>

        <div class="button-container">
            <a href="{{ $boleto->fileurl }}" class="button">
                Baixar Boleto
            </a>
        </div>
        
        <p>Se você tiver alguma dúvida, entre em contato conosco.</p>
    </div>

    <div class="footer">
        <p>Este é um email automático, por favor não responda.</p>
    </div>
</body>
</html>