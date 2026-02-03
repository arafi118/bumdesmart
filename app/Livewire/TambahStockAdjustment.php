<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\StockMovement;

class TambahStockAdjustment extends Component
{
    public $title;
    public $businessId;

    public function mount()
    {
        $this->title = 'Tambah Stock Adjustment';
        $this->businessId = auth()->user()->business_id;
    }

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
    public function saveAdjustment($data)
    {
        if (!is_array($data) || empty($data['items'])) {
            $this->dispatch('alert', type: 'error', message: 'Data tidak valid');
            return;
        }

        DB::beginTransaction();

        try {
            if (empty($data['tanggal_penyesuaian'])) {
                throw new \Exception('Tanggal penyesuaian wajib diisi');
            }

            $adjustment = StockAdjustment::create([
                'business_id'         => $this->businessId,
                'user_id'             => auth()->id(),
                'no_penyesuaian'      => $data['no_penyesuaian'] ?: 'ADJ-' . now()->format('YmdHis'),
                'tanggal_penyesuaian' => $data['tanggal_penyesuaian'],
                'jenis_penyesuaian'   => $data['jenis_penyesuaian'] ?? 'correction',
                'status'              => $data['status'] ?? 'draft',
                'catatan'             => $data['catatan'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                if (
                    !isset(
                        $item['product_id'],
                        $item['stok_sistem'],
                        $item['stok_fisik'],
                        $item['selisih']
                    )
                ) {
                    continue;
                }

                $selisih = (int) $item['selisih'];
                if ($selisih === 0) continue;

                $jumlah = abs($selisih);
                $jenis  = $selisih > 0 ? 'in' : 'out';

                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id'          => $item['product_id'],
                    'jumlah'              => abs($selisih),
                    'jenis'               => $selisih > 0 ? 'in' : 'out',
                    'harga_satuan'        => $item['harga_satuan'],
                    'total_harga'         => abs($selisih) * $item['harga_satuan'],
                    'alasan'              => $item['alasan'] ?? null,
                    'catatan'             => $data['catatan'] ?? null,
                ]);

                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $product->stok_aktual += $selisih;
                $product->save();

                StockMovement::create([
                    'business_id'            => $this->businessId,
                    'product_id'             => $item['product_id'],
                    'tanggal_perubahan_stok' => $data['tanggal_penyesuaian'],
                    'jenis_perubahan'        => 'stock_adjustment',
                    'jumlah_perubahan'       => $selisih,
                    'reference_id'           => $adjustment->id,
                    'reference_type'         => 'stok_adjustment',
                    'catatan'                => $item['alasan'] ?? $data['catatan'] ?? null,
                ]);
            }

            DB::commit();

            $this->dispatch('alert', type: 'success', message: 'Stock adjustment berhasil disimpan');
            $this->dispatch('redirect', url: '/stock-adjustment/daftar', timeout: 1000);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.tambah-stock-adjustment')
            ->layout('layouts.app', ['title' => $this->title]);
    }
}
