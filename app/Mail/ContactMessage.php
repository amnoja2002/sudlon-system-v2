<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $messageBody;

    public function __construct(string $name, string $email, string $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->messageBody = $message;
    }

    public function build(): self
    {
        return $this->subject('New Contact Message from ' . $this->name)
                    ->replyTo($this->email, $this->name)
                    ->view('emails.contact-message');
    }
}


