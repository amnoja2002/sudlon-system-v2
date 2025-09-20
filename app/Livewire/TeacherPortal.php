<?php

namespace App\Livewire;

use Livewire\Component;

class TeacherPortal extends Component
{
    public function render()
    {
        return view('livewire.teacher-portal')
            ->layout('layouts.app');
    }
}