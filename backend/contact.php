<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Dotenv\Dotenv;

// Load Composer's autoloader
require 'vendor/autoload.php';

class Contact
{
    private $error = [];
    private $request;
    private $statusCode=200;

    public function __construct($post){
        $this->request=$post;
        $dotenv = Dotenv::createImmutable("../");
        $dotenv->load();
    }

    public function validate()
    {

        if (!isset($this->request['plano']) || !$this->request['plano']):
            $this->error[] = "Plano inválido";
        endif;

        if (!isset($this->request['nome']) || !$this->request['nome']):
            $this->error[] = "Nome inválido";
        endif;

        if (!isset($this->request['email']) || !$this->request['email']):
            $this->error[] = "Email inválido";
        endif;

        if (!isset($this->request['telefone']) || !$this->request['telefone']):
            $this->error[] = "Telefone inválido";
        endif;

        if (!isset($this->request['endereco']) || !$this->request['endereco']):
            $this->error[] = "Endereço inválido";
        endif;

        if (count($this->error) > 0) {
            $this->statusCode=400;
        }
        
    }

    public function setResponse()
    {
        header('Content-Type: application/json');
         http_response_code($this->statusCode);
            if($this->statusCode==200){
                echo json_encode(["<div class='text-center' >Solicitação Enviada com Sucesso!<br>
                <p>Aguarde que verificaremos a disponibilidade para seu Endereço</p>
                <p>Entraremos em Contato em Breve</p>
                </div>
                "]);
            }
            else{
               echo json_encode($this->error);
            }
    }

    public function run(){
        $this->validate();
        $this->sendEmail();
        $this->setResponse();
    }

    public function sendEmail()
    {
        if (count($this->error) > 0) {
            return false;
        }
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP(); // Send using SMTP
            $mail->Host = $_ENV['MAIL_HOST']; // Set the SMTP server to send through
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = $_ENV['MAIL_USERNAME']; // SMTP username
            $mail->Password = $_ENV['MAIL_PASSWORD']; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port = $_ENV['MAIL_PORT']; // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            // $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
            $mail->addAddress($_ENV['MAIL_TO_ADDRESS']); // Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Solicitação de Plano';
            $mail->Body = $this->getBodyEmail();
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            return true;
        } catch (Exception $e) {
            $this->statusCode=500;
            $this->error[]="Mensagem não pode ser enviada. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    private function getBodyEmail(){
        $body = "<p>Plano: {$this->request['plano']} </p>";
        $body.= "<p>Nome: {$this->request['nome']} </p>";
        $body.= "<p>Email: {$this->request['email']} </p>";
        $body.= "<p>Telefone: {$this->request['telefone']} </p>";
        $body.= "<p>Endereço: {$this->request['endereco']} </p>";
        return $body;
    }

   
}

$contact = new Contact($_POST);
$contact->run();



