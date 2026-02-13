<?php

namespace App\Livewire\Keuangan;

use App\Models\Transaction_type;
use App\Models\Account;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;

class JurnalUmum extends Component
{
    public $title = 'Jurnal Umum';
    public $business_id;

    public $jenis_transaksi = [];
    public $rekeningList = [];

    public $tanggal_transaksi;
    public $selectedJenis = null;
    public $selectedSumber = null;
    public $selectedTujuan = null;

    public $items = [];

    public $keterangan;
    public $total = 0;
    public $saldo = 0;

    public $tahun;
    public $bulan;
    public $tanggal;

    public $jurnalUmum = null;

    public function mount()
    {
        $this->business_id = auth()->user()->business_id;

        $this->jenis_transaksi = Transaction_type::all();
        $this->rekeningList = Account::where('business_id', $this->business_id)->get();
        $this->tanggal_transaksi = date('Y-m-d');
        $this->tahun = date('Y');
        $this->bulan = date('m');
        $this->tanggal = date('d');

        $akun = Account::where('business_id', auth()->user()->business_id)->get();
        $this->jurnalUmum = [
            'akun' => $akun,
            'jenis_transaksi' => $this->jenis_transaksi,
        ];
    }

    public function saveJurnalUmum($data)
    {
        if (!is_array($data)) {
            $this->dispatch('alert', type: 'error', message: 'Data tidak valid');
            return;
        }

        DB::beginTransaction();

        try {

            if (empty($data['tanggal_pembayaran'])) {
                throw new \Exception('Tanggal Transaksi wajib diisi');
            }

            $sumber = $data['sumber_dana'] ?? '';
            $simpan = $data['disimpan_ke'] ?? '';

            $noPembayaran = 'PAY-' . date('Ymd') . '-' .
                str_pad(Payment::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            //PENGHAPUSAN / PENJUALAN ASET
            if (
                str_starts_with($sumber, '1.2.01.01') ||
                str_starts_with($sumber, '1.2.02')
            ) {
                if (str_starts_with($simpan, '5.3.02.01') && $data['jenis_transaksi'] == 2) {
                    Payment::create([
                        'business_id' => auth()->user()->business_id,
                        'user_id' => auth()->id(),
                        'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                        'jenis_transaksi' => $data['jenis_transaksi'],
                        'rekening_debit' => $simpan,
                        'rekening_kredit' => $sumber,
                        'total_harga' => $data['nominal'] ?? 0,
                        'catatan' => 'Penghapusan / Penjualan Inventaris',
                        'no_pembayaran' => $noPembayaran,
                    ]);
                }
                //PEMBELIAN INVENTARIS
            } elseif (
                str_starts_with($simpan, '1.2.01') ||
                str_starts_with($simpan, '1.2.03')
            ) {
                Payment::create([
                    'business_id' => auth()->user()->business_id,
                    'user_id' => auth()->id(),
                    'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                    'jenis_transaksi' => $data['jenis_transaksi'],
                    'rekening_debit' => $simpan,
                    'rekening_kredit' => $sumber,
                    'total_harga' => $data['nominal'] ?? 0,
                    'catatan' => 'Pembelian Inventaris',
                    'no_pembayaran' => $noPembayaran,
                ]);
            } else {
                Payment::create([
                    'business_id' => auth()->user()->business_id,
                    'user_id' => auth()->id(),
                    'tanggal_pembayaran' => $data['tanggal_pembayaran'],
                    'jenis_transaksi' => $data['jenis_transaksi'],
                    'rekening_debit' => $sumber,
                    'rekening_kredit' => $simpan,
                    'total_harga' => $data['nominal'] ?? 0,
                    'catatan' => $data['keterangan'] ?? null,
                    'no_pembayaran' => $noPembayaran,
                ]);
            }

            DB::commit();

            $this->dispatch('alert', type: 'success', message: 'Transaksi berhasil disimpan');
            $this->dispatch('redirect', url: '/keuangan/jurnal-umum', timeout: 1000);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.keuangan.jurnal-umum')
            ->layout('layouts.app', ['title' => $this->title]);
    }
}
