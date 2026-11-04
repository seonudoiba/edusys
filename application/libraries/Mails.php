<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

#[AllowDynamicProperties]
class Mails{
    public function send($data){
        try {
            $CI =& get_instance();
            
            echo '<pre>';print_r($data);echo '</pre>';
            $mailData = array(
                'to'      => is_array($data['to']) ? $data['to'] : [ $data['to'] ?? $CI->config->item('from_email') ],
                'subject' => $data['subject'] ?? '',
                'html'    => $data['html'] ?? '',
            );
            echo '<pre>';print_r($mailData);echo '</pre>';

            $mail = new OCMail('smtp');
            $mail->parameter = '';
            $mail->smtp_hostname = $CI->config->item('smtp_host');
            $mail->smtp_username = $CI->config->item('smtp_user');
            $mail->smtp_password = html_entity_decode($CI->config->item('smtp_pass'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $CI->config->item('smtp_port');

            $mail->setSender('smartinnovation');
            $mail->setFrom($CI->config->item('from_email'));

            $mail->setSubject($mailData['subject']);
            $mail->setHtml($mailData['html']);

            if(isset($data['attachment']) && $data['attachment']){
                $mail->addAttachment($data['attachment']);
            }

            foreach ($mailData['to'] as $to){
                if($to){
                    $mail->setTo($to);
                    $mail->send();
                }
            }
            echo '<pre>';print_r($mail);echo '</pre>';
            
            return true;
        } catch (Exception $e) {
            return 'Error sending mail: ' . $e->getMessage();
        }
        return false;
    }
}

#[AllowDynamicProperties]
class OCMail {
    private string $adaptor;
    protected string $to = '';
    protected string $from = '';
    protected string $sender = '';
    protected string $reply_to = '';
    protected string $subject = '';
    protected string $text = '';
    protected string $html = '';
    protected array $attachments = [];
    protected string $library_key = '';

    /**
     * Constructor
     *
     * @param   string  $adaptor
     *
    */
    public function __construct(string $adaptor = 'mail') {
        $class = 'Ocs' . ucfirst($adaptor);
        $this->library_key = strtolower($class);
        
        // if (class_exists($class)) {
        //     $this->adaptor = $class;
        // } else {
        //     throw new \Exception('Error: Could not load mail adaptor ' . $adaptor . '!');
        // }
    }

    /**
     *
     *
     * @param   mixed   $to
     */
    public function setTo(string $to): void {
        $this->to = $to;
    }

    /**
     *
     *
     * @param   string  $from
     */
    public function setFrom($from): void {
        $this->from = $from;
    }

    /**
     *
     *
     * @param   string  $sender
     */
    public function setSender($sender): void {
        $this->sender = $sender;
    }

    /**
     *
     *
     * @param   string  $reply_to
     */
    public function setReplyTo($reply_to): void {
        $this->reply_to = $reply_to;
    }

    /**
     *
     *
     * @param   string  $subject
     */
    public function setSubject($subject): void {
        $this->subject = $subject;
    }

    /**
     *
     *
     * @param   string  $text
     */
    public function setText($text): void {
        $this->text = $text;
    }

    /**
     *
     *
     * @param   string  $html
     */
    public function setHtml($html): void {
        $this->html = $html;
    }

    /**
     *
     *
     * @param   string  $filename
     */
    public function addAttachment($filename): void {
        $this->attachments[] = $filename;
    }

    /**
     *
     *
     */
    public function send(): bool {
        if (!$this->to) {
            throw new \Exception('Error: E-Mail to required!');
        }

        if (!$this->from) {
            throw new \Exception('Error: E-Mail from required!');
        }

        if (!$this->sender) {
            throw new \Exception('Error: E-Mail sender required!');
        }

        if (!$this->subject) {
            throw new \Exception('Error: E-Mail subject required!');
        }

        if (!$this->text && !$this->html) {
            throw new \Exception('Error: E-Mail message required!');
        }

        $mail_data = [];

        foreach (get_object_vars($this) as $key => $value) $mail_data[$key] = $value;

        $CI =& get_instance();
        $CI->load->library($this->library_key, $mail_data);
        
        $mail = $CI->{$this->library_key};
        
        return $mail->send();
    }
}