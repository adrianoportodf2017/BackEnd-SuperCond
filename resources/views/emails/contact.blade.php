<!DOCTYPE html>
<html>
<head>
    <title>Nova Mensagem de Contato</title>
</head>
<body>
<div style="background-color: white;border: 2px solid #0f0870;box-shadow: 20px -13px 1px 1px #0f0870;
        width: fit-content;padding: 1rem 1rem;font-family: system-ui;">
            <h4 style="text-align: center; font-size: large;"> Mensagem de Contato</h4>
    <p><strong>Nome:</strong> {{ $data['name'] }}</p>
    <p><strong>Email:</strong> {{$data['email'] }}</p>
    <p><strong>Mensagem:</strong> {{ $data['message'] }}</p>
</div>
</body>
</html>