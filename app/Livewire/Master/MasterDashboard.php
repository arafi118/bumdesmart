<?php

namespace App\Livewire\Master;

use App\Models\Owner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class MasterDashboard extends Component
{
    public $totalOwners = 0;
    public $totalTenants = 0;

    public function mount()
    {
        $this->totalOwners = Owner::count();
        $this->totalTenants = Owner::count();
    }

    #[Layout('layouts.app')]
    #[Title('Master Dashboard')]
    public function render()
    {
        return view('livewire.master.master-dashboard');
    }
}
