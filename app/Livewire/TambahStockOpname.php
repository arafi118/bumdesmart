<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TambahStockOpname extends Component
{
    public $title;

    public $businessId;

    public $nomorOpname;

    public $status = 'draft';

    public $tanggalOpname;

    public $tanggalApproved;

    public $approvedBy;

    public $catatan;

    public $products = [];

    public function mount()
    {
        $this->title = 'Tambah Stock Opname';
        $this->businessId = auth()->user()->business_id;
    }

    /**
     * Load products untuk tampilan tabel
     */
    public function loadProducts($page = 1, $search = '')
    {
        $query = Product::where('business_id', $this->businessId);

        if ($search) {
            $query->where('nama_produk', 'like', "%{$search}%");
        }

        $data = $query
            ->orderBy('nama_produk')
            ->skip(($page - 1) * 10)
            ->take(11)
            ->get();

        return [
            'data' => $data->take(10)->values(),
            'has_more' => $data->count() > 10,
        ];
    }

    /**
     * Menyimpan Stock Opname
     * 
     * @param array $data -> dikirim dari Alpine.js via @this.call()
     */
    public function saveOpname($data)
    {
        if (!$data || !is_array($data)) {
            $this->dispatch('alert', type: 'error', message: 'Data tidak valid');
            return;
        }

        DB::beginTransaction();

        try {
            $tanggalOpname = $data['tanggal'] ?? null;
            if (!$tanggalOpname) throw new \Exception('Tanggal opname wajib diisi');

            $status = $data['status'] ?? 'draft';
            if (!in_array($status, ['draft', 'completed', 'approved', 'rejected', 'canceled', 'closed'])) {
                throw new \Exception('Status opname tidak valid');
            }

            if (empty($data['items']) || !is_array($data['items'])) {
                throw new \Exception('Item stock opname tidak boleh kosong');
            }

            $tanggalApproved = $status === 'approved' ? ($data['tanggal_approved'] ?? now()->format('Y-m-d')) : null;
            $approvedBy      = $status === 'approved' ? ($data['approved_by'] ?? auth()->user()->name) : null;

            $opname = StockOpname::create([
                'business_id'      => $this->businessId,
                'user_id'          => auth()->id(),
                'no_opname'        => $data['no_opname'] ?: 'SO-' . now()->format('YmdHis'),
                'tanggal_opname'   => $tanggalOpname,
                'status'           => $status,
                'catatan'          => $data['catatan'] ?? null,
                'approved_by'      => $approvedBy,
                'tanggal_approved' => $tanggalApproved,
            ]);

            foreach ($data['items'] as $item) {
                if (!isset($item['product_id'])) continue;

                // Simpan detail opname
                StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,
                    'product_id'      => $item['product_id'],
                    'stok_sistem'     => $item['stok_sistem'],
                    'stok_fisik'      => $item['stok_fisik'],
                    'selisih'         => $item['selisih'],
                    'jenis_selisih'   => $item['jenis_selisih'],
                    'alasan'          => $item['alasan'] ?? null,
                    'harga_satuan'    => $item['harga_satuan'] ?? 0,
                    'total_harga'     => ($item['stok_fisik']) * ($item['harga_satuan'] ?? 0),
                    'catatan'         => $data['catatan'] ?? null,
                ]);

                // Update stok aktual produk
                Product::where('id', $item['product_id'])
                    ->update(['stok_aktual' => $item['stok_fisik']]);

                // Buat stock movement per produk
                StockMovement::create([
                    'business_id'            => $this->businessId,
                    'product_id'             => $item['product_id'],
                    'tanggal_perubahan_stok' => $tanggalOpname,
                    'jenis_perubahan'        => 'stock_opname',
                    'jumlah_perubahan'       => $item['selisih'],
                    'reference_id'           => $opname->id,
                    'reference_type'         => 'stock_opname',
                    'catatan'                => $item['alasan'] ?? $data['catatan'] ?? null,
                ]);
            }

            DB::commit();

            $this->dispatch('alert', type: 'success', message: 'Stock opname berhasil disimpan');
            $this->dispatch('redirect', url: '/stock-opname/daftar', timeout: 1000);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.tambah-stock-opname')
            ->layout('layouts.app', ['title' => $this->title]);
    }
}
