<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class Kohana_Email_Swiftmailer extends Email {

    /**
     * Initialize the mail transport for SMTP.
     *
     * @param   array  $options configuration option parameters
     */
    protected function config_smtp($options = array())
    {
        $port = (int) Arr::get($options, 'port', 25);
        $hostname = Arr::get($options, 'hostname', 'localhost');
        $username = Arr::get($options, 'username', '');
        $password = Arr::get($options, 'password', '');
        $encryption = Arr::get($options, 'encryption', null); // e.g., ssl, tls

        // Construct the DSN string for Symfony Mailer
        $dsn = "smtp://{$username}:{$password}@{$hostname}:{$port}";

        if (!empty($encryption)) {
            $dsn = "smtp://{$username}:{$password}@{$hostname}:{$port}?encryption={$encryption}";
        }

        // Create the transport
        $transport = Transport::fromDsn($dsn);

        // Assign the transport to the mailer
        $this->mailer = new Mailer($transport);
    }

    /**
     * Send an email.
     *
     * @param   array   $to       Recipient(s)
     * @param   string  $subject  Email subject
     * @param   string  $body     Email body
     * @param   array   $headers  Additional headers
     * @return  void
     */
    public function send($to, $subject, $body, $headers = [])
    {
        $email = (new Email())
            ->from(Arr::get($headers, 'from', 'no-reply@example.com'))
            ->to(...(array) $to)
            ->subject($subject)
            ->text($body)
            ->html(Arr::get($headers, 'html', $body));

        // Add CC/BCC if provided
        if ($cc = Arr::get($headers, 'cc')) {
            $email->cc(...(array) $cc);
        }
        if ($bcc = Arr::get($headers, 'bcc')) {
            $email->bcc(...(array) $bcc);
        }

        // Send the email
        $this->mailer->send($email);
    }
}
