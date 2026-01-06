<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public $title;

    public function render()
    {
        $this->title = 'Dashboard';

        return view('livewire.dashboard')->layout('layouts.app', ['title' => $this->title]);
    }
}
