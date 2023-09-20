<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed' => 'A confirmação do campo :attribute não corresponde.',
    'date' => 'O campo :attribute não é uma data válida.',
    'date_equals' => 'O campo :attribute deve ser uma data igual a :date.',
    'date_format' => 'O campo :attribute não corresponde ao formato :format.',
    'different' => 'O campo :attribute e :other devem ser diferentes.',
    'digits' => 'O campo :attribute deve ter :digits dígitos.',
    'digits_between' => 'O campo :attribute deve ter entre :min e :max dígitos.',
    'dimensions' => 'O campo :attribute possui dimensões de imagem inválidas.',
    'distinct' => 'O campo :attribute possui um valor duplicado.',
    'email' => 'O :attribute deve ser um endereço de e-mail válido.',
    'ends_with' => 'O campo :attribute deve terminar com um dos seguintes: :values.',
    'exists' => 'O :attribute selecionado é inválido.',
    'file' => 'O campo :attribute deve ser um arquivo.',
    'filled' => 'O campo :attribute deve ter um valor.',
    
    'gt' => [
        'numeric' => 'O campo :attribute deve ser maior que :value.',
        'file' => 'O campo :attribute deve ter mais de :value kilobytes.',
        'string' => 'O campo :attribute deve ter mais de :value caracteres.',
        'array' => 'O campo :attribute deve ter mais de :value itens.',
    ],
    'gte' => [
        'numeric' => 'O campo :attribute deve ser maior ou igual a :value.',
        'file' => 'O campo :attribute deve ter :value kilobytes ou mais.',
        'string' => 'O campo :attribute deve ter :value caracteres ou mais.',
        'array' => 'O campo :attribute deve ter :value itens ou mais.',
    ],
    'image' => 'O campo :attribute deve ser uma imagem.',
    'in' => 'O :attribute selecionado é inválido.',
    'in_array' => 'O campo :attribute não existe em :other.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'ip' => 'O campo :attribute deve ser um endereço IP válido.',
    'ipv4' => 'O campo :attribute deve ser um endereço IPv4 válido.',
    'ipv6' => 'O campo :attribute deve ser um endereço IPv6 válido.',
    'json' => 'O campo :attribute deve ser uma string JSON válida.',
    'lt' => [
        'numeric' => 'O campo :attribute deve ser menor que :value.',
        'file' => 'O campo :attribute deve ter menos de :value kilobytes.',
        'string' => 'O campo :attribute deve ter menos de :value caracteres.',
        'array' => 'O campo :attribute deve ter menos de :value itens.',
    ],
    'lte' => [
        'numeric' => 'O campo :attribute deve ser menor ou igual a :value.',
        'file' => 'O campo :attribute deve ter :value kilobytes ou menos.',
        'string' => 'O campo :attribute deve ter :value caracteres ou menos.',
        'array' => 'O campo :attribute não deve ter mais do que :value itens.',
    ],
    'max' => [
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'file' => 'O campo :attribute não pode ter mais de :max kilobytes.',
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
        'array' => 'O campo :attribute não pode ter mais do que :max itens.',
    ],
    'mimes' => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'mimetypes' => 'O campo :attribute deve ser um arquivo do tipo: :values.',
    'min' => [
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
        'file' => 'O campo :attribute deve ter pelo menos :min kilobytes.',
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'array' => 'O campo :attribute deve ter pelo menos :min itens.',
    ],
    'multiple_of' => 'O campo :attribute deve ser um múltiplo de :value.',
    'not_in' => 'O :attribute selecionado é inválido.',
    'not_regex' => 'O formato do campo :attribute é inválido.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'password' => 'A senha está incorreta.',
    'present' => 'O campo :attribute deve estar presente.',
    'regex' => 'O formato do campo :attribute é inválido.',
    'required' => 'O campo :attribute é obrigatório.',
    'required_if' => 'O campo :attribute é obrigatório quando :other é :value.',
    'required_unless' => 'O campo :attribute é obrigatório a menos que :other esteja em :values.',
    'required_with' => 'O campo :attribute é obrigatório quando :values está presente.',
    'required_with_all' => 'O campo :attribute é obrigatório quando :values estão presentes.',
    'required_without' => 'O campo :attribute é obrigatório quando :values não está presente.',
    'required_without_all' => 'O campo :attribute é obrigatório quando nenhum dos :values está presente.',
    'same' => 'O campo :attribute e :other devem ser iguais.',
    'size' => [
        'numeric' => 'O campo :attribute deve ter :size.',
        'file' => 'O campo :attribute deve ter :size kilobytes.',
        'string' => 'O campo :attribute deve ter :size caracteres.',
        'array' => 'O campo :attribute deve conter :size itens.',
    ],
    'starts_with' => 'O campo :attribute deve começar com um dos seguintes: :values.',
    'string' => 'O campo :attribute deve ser uma string.',
    'timezone' => 'O campo :attribute deve ser uma zona válida.',
    'unique' => 'O :attribute já foi utilizado.',
    'uploaded' => 'O arquivo :attribute falhou ao ser carregado.',
    'url' => 'O formato do campo :attribute é inválido.',
    'uuid' => 'O campo :attribute deve ser um UUID válido.',
    
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
