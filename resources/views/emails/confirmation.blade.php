<!-- resources/views/emails/confirmation.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Recebemos sua mensagem</title>
</head>
<body>
<div style="background-color: white;border: 2px solid #0f0870;box-shadow: 20px -13px 1px 1px #0f0870;
        width: fit-content;padding: 1rem 1rem;font-family: system-ui;">
            <h4 style="text-align: center; font-size: large;"> 
    <p>Olá {{ $data['name'] }},</p>

    <p>Recebemos sua mensagem e entraremos em contato em breve.</p>
</div>
    <p>Atenciosamente,<br>
    Agência Tecnet</p>
</body>
</html>
