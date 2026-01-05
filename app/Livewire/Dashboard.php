<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $title = 'Dashboard';

        return view('livewire.dashboard', [
            'title' => $title,
        ]);
    }
}
