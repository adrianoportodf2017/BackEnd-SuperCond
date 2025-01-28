<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .header {
            text-align: center;
            padding: 30px 0;
            background: linear-gradient(135deg, #003366 0%, #004d99 100%);
            border-radius: 12px 12px 0 0;
            margin-bottom: 0;
            border-bottom: 4px solid #f7941d;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 32px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        .logo {
            max-height: 100px;
            margin-bottom: 20px;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.2));
        }
        .content {
            background-color: white;
            padding: 40px;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .section {
            margin: 30px 0;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #003366;
        }
        .section-title {
            color: #003366;
            font-size: 1.3em;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .alert {
            background: linear-gradient(45deg, #fff3cd, #fff8e6);
            border: 1px solid #ffeeba;
            border-left: 4px solid #f7941d;
            color: #856404;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 30px;
            border-top: 2px solid #eee;
            font-size: 0.9em;
            color: #666;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .highlight {
            color: #003366;
            font-weight: bold;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 6px;
            display: inline-block;
            margin: 15px 0;
        }
        .contact-info {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin: 30px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background-color: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        a {
            color: #004d99;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #f7941d;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px;
            }
            .section {
                padding: 15px;
            }
            .contact-info {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ $logo_url ?? asset('images/logo.png') }}" alt="{{ $company_name ?? 'Empresa' }}" class="logo">
        <h1>{{ $company_name ?? 'Empresa' }}</h1>
    </div>
    
    <div class="content">
         {!! $content !!}     
    </div>

    <div class="footer">
        <p>{{ $footer_message ?? 'Este é um email automático. Por favor, não responda.' }}</p>
        <p>{{ $company_name ?? ' ' }} - {{ $company_slogan ?? ' ' }}</p>
        <p><span>{{ $phone  ?? ' ' }}</span></p>
        <p><span>{{ $email  ?? ' ' }}</span></p>      
        <p><span>{{ $cnpj  ?? ' ' }}</span></p>
        <p><span>{{ $address  ?? '' }}</span></p>

        <p>{{ now()->format('d/m/Y H:i:s') }}</p>

    </div>
</body>
</html>