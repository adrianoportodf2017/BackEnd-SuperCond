<!DOCTYPE html>
<html>
<head>
    <title>Nova Mensagem de Contato</title>
</head>
<body>
    <h1>Mensagem de Contato</h1>
    <p><strong>Nome:</strong> {{ $data['name'] }}</p>
    <p><strong>Email:</strong> {{$data['email'] }}</p>
    <p><strong>Mensagem:</strong> {{ $data['message'] }}</p>
</body>
</html>