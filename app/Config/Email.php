<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'your-email@example.com';
    public string $fromName   = 'Your Name';
    public string $recipients = '';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * SMTP Server Hostname (Replace with your SMTP provider)
     */
    public string $SMTPHost = 'smtp.yourmailprovider.com';

    /**
     * SMTP Username (Your email address)
     */
    public string $SMTPUser = 'your-email@example.com';

    /**
     * SMTP Password (Your email password or app password)
     */
    public string $SMTPPass = 'your-email-password';

    /**
     * SMTP Port
     * - 587 for TLS
     * - 465 for SSL
     * - 25 (not recommended, as many providers block this port)
     */
    public int $SMTPPort = 587;

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 10;

    /**
     * SMTP Encryption: '', 'tls', or 'ssl'
     */
    public string $SMTPCrypto = 'tls';

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'html';

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Newline character (Use “\r\n” to comply with RFC 822)
     */
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode.
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;
}
