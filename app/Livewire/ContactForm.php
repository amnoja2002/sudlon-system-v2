<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use App\Mail\ContactMessage;

class ContactForm extends Component
{
    public $name = '';
    public $email = '';
    public $message = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'message' => 'required|min:10',
    ];

    public function submit()
    {
        $this->validate();

        Mail::to(config('mail.from.address'))
            ->send(new ContactMessage(
                name: $this->name,
                email: $this->email,
                message: $this->message
            ));

        session()->flash('message', "Message sent! We'll be in touch shortly.");

        // Reset form
        $this->reset(['name', 'email', 'message']);
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}