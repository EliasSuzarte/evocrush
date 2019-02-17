<?php

class MailHandler
{
    public $status,$msg;

    public function send($to, $subject, $msg, $from = 'suporte@site.com')
    {
        $subject = htmlspecialchars($subject);
        if (self::isMailValid($to) && self::isMailValid($from)) {
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= "From: EvoCrush <{$from}>" . "\r\n";

            if (mail($to, $subject, $msg, $headers, '-r'.'suporte@site.com')) {
                $this->status = true;
                $this->msg = 'Mensagem enviada com sucesso';
            } else {
                $this->status = false;
                $this->msg = 'Falha no envio do email';
            }
        } else {
            $this->status = false;
            $this->msg = "O e-mail de origem ou o de destinatário não é válido";
        }

    } // send

    private function isMailValid($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    } // isMailValid


    /*@function sendTokenToNewUser Enviar token para usuários assim que eles se cadastram */
    public function sendTokenToNewUser($nome, $email, $confmailtoken)
    {
        $nome = htmlentities($nome);
        $mensagem = "{$nome} você se cadastrou com sucesso no EvoCrush.com<br>Agora é só clicar no link a seguir para confirmar seu e-mail " .
            "<a href='https://evocrush.com/tokens/?confirmail={$confmailtoken}'>Confirmar Email</a><br>Caso o link não funcione copie o link e cole na" .
            "barra de navegação de seu navegador https://evocrush.com/tokens/?confirmail={$confmailtoken}";

        $subject = "{$nome} Confirme Seu Email";
        self::send($email,$subject, $mensagem);

    } // sendTokenToNewUser


    public function supportSend()
    {
        // mensagem é enviada para o administradar para informar que há uma nova mensagem
        self::send('send@servidor.com', 'Nova mensagem de suporte - EvoCrush', 'Alguém enviou uma nova mensagem no EvoCrush, abra o painel de controle para ver');
    } // supportSend


    public function recoverAccount($email, $token)
    {
        $subject = "Recuperação de Conta - Evocrush.com";
        $mensagem = "Recebemos sua solicitação de recuperação de conta<br>" .
            "Clique no link a seguir para criar uma nova senha <a href='https://evocrush.com/recover/new.php?token={$token}'>Criar Nova Senha</a>".
        "<br> Caso o link não esteja aparecendo copie https://evocrush.com/recover/new.php?token={$token} e cole na barra de navegação de seu navegador web";

        self::send($email,$subject,$mensagem);

    } // recoverAccount


}


























