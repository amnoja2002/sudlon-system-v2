<?php

namespace App\Livewire\Roles\Teacher;

use Livewire\Component;

class Portal extends Component
{
    public function render()
    {
        return view('livewire.teacher-portal')
            ->layout('layouts.app');
    }
}