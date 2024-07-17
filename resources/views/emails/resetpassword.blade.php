@component('mail::message')
# Olá!

Esqueceu sua senha?

@component('mail::button', ['url' => $url])
Clique para redefinir
@endcomponent

Obrigado por usar nossa aplicação!

Atenciosamente,<br>
Ouro Vermelho II

@component('mail::subcopy')
Se você estiver tendo problemas para clicar no botão "Clique para redefinir", copie e cole a URL abaixo no seu navegador web: [{{ $url }}]({{ $url }})
@endcomponent

@endcomponent
