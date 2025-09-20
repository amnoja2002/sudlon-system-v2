<?php

namespace App\Livewire;

use Livewire\Component;

class ParentPortal extends Component
{
    public function render()
    {
        return view('livewire.parent-portal')
            ->layout('layouts.app');
    }
}