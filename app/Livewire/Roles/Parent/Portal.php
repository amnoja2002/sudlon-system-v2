<?php

namespace App\Livewire\Roles\Parent;

use Livewire\Component;

class Portal extends Component
{
    public function render()
    {
        return view('livewire.parent-portal')
            ->layout('layouts.app');
    }
}