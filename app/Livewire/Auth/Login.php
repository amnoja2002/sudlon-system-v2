<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            // Redirect based on user role
            $user = Auth::user();
            
            // Check if user has a role assigned
            if (!$user->role) {
                $this->addError('email', 'Your account does not have a role assigned. Please contact an administrator.');
                Auth::logout();
                return;
            }

            // regenerate session to prevent fixation
            session()->regenerate();

            // prefer intended url, otherwise use role's dashboard
            $intended = session()->pull('url.intended');
            if ($intended) {
                return redirect()->intended($intended);
            }

            return redirect()->route($user->getDashboardRoute());
        }

        $this->addError('email', trans('auth.failed'));
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}