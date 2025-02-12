<?php

/******************************************************************************/
/* Cada entrada desse ficheiro pode ser associada a um comentário para indicar o seu */
/* estado. Quando não há comentário, significa que a entrada está totalmente traduzida. */
/* Os comentários reconhecidos são (a correspondência de comentários não diferencia maiúsculas de minúsculas): */
/* + TODO: a entrada nunca foi traduzida. */
/* + DIRTY: a entrada foi traduzida, mas precisa de ser atualizada. */
/* + IGNORE: a entrada não precisa de ser traduzida. */
/* Quando um comentário não é reconhecido, ele é ignorado. */
/******************************************************************************/

return array(
    'email' => array(
        'feedback' => array(
            'invalid' => 'Endereço de email inválido',
            'required' => 'O endereço de email é necessário',
        ),
        'validation' => array(
            'change_email' => 'Pode mudar o seu endereço de email <a href="%s">na página do perfil</a>.',
            'email_sent_to' => 'Enviámos um email para <strong>%s</strong>. Por favor, siga as instruções contidas nele para verificar a sua conta.',
            'feedback' => array(
                'email_failed' => 'Não foi possível enviar um email para si devido a um erro de configuração no servidor.',
                'email_sent' => 'Um email foi enviado para o seu endereço',
                'error' => 'Falha na verificação do endereço de email',
                'ok' => 'O endereço de email foi verificado com sucesso.',
                'unnecessary' => 'Esse endereço de email já foi verificado.',
                'wrong_token' => 'A verificação do endereço de email falhou por causa do token incorreto.',
            ),
            'need_to' => 'Para poder utilizar o %s, deve verificar o seu endereço de email.',
            'resend_email' => 'Reenviar o email',
            'title' => 'Validação do endereço de email',
        ),
    ),
    'mailer' => array(
        'email_need_validation' => array(
            'body' => 'Registou-se no %s. Mas ainda é necessário verificar o seu endereço de email. Para isso, basta seguir o link:',
            'title' => 'Precisa de verificar a sua conta',
            'welcome' => 'Bem-vindo %s,',
        ),
    ),
    'password' => array(
        'invalid' => 'Senha incorreta',
    ),
    'tos' => array(
        'feedback' => array(
            'invalid' => 'Para se registar, tem de aceitar os Termos de serviço.',
        ),
    ),
    'username' => array(
        'invalid' => 'Nome de utilizador inválido.',
        'taken' => 'O nome de utilizador %s já está a ser utilizado',
    ),
);