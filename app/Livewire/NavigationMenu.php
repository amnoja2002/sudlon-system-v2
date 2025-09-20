<?php

namespace App\Livewire;

use Livewire\Component;

class NavigationMenu extends Component
{
    public $isMobileMenuOpen = false;

    public function toggleMobileMenu()
    {
        $this->isMobileMenuOpen = !$this->isMobileMenuOpen;
    }

    public function render()
    {
        return view('livewire.navigation-menu');
    }
}