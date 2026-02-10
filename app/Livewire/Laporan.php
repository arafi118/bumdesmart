<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan')]
class Laporan extends Component
{
    // Filter Properties
    public $tahun;

    public $bulan;

    public $periode;

    public $jenis_laporan;

    public function mount()
    {
        $this->tahun = date('Y');
        $this->bulan = date('m');
        $this->periode = '-';

        $this->jenis_laporan = '';
    }

    public function render()
    {
        return view('livewire.laporan');
    }
}
