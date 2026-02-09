<?php

namespace App\Livewire;

use App\Models\StockOpname as StockOpnameModel;
use App\Utils\TableUtil;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class StockOpname extends Component
{
    use WithPagination;

    public $title;

    public $search = '';

    public $businessId;

    // Properties for detail modal
    public $selectedOpname;

    public $opnameDetails = [];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->title = 'Daftar Stock Opname';
        $this->businessId = auth()->user()->business_id;

        $query = StockOpnameModel::where('business_id', $this->businessId)
            ->where(function ($q) {
                $q->where('no_opname', 'like', '%'.$this->search.'%')
                    ->orWhere('catatan', 'like', '%'.$this->search.'%')
                    ->orWhere('status', 'like', '%'.$this->search.'%');
            })
            ->with(['user', 'approvedBy'])
            ->orderBy('created_at', 'desc');

        $headers = [
            TableUtil::setTableHeader('id', '#', false, false),
            TableUtil::setTableHeader('no_opname', 'No. Opname', true, true),
            TableUtil::setTableHeader('tanggal_opname', 'Tanggal', true, true),
            TableUtil::setTableHeader('status', 'Status', true, true),
            TableUtil::setTableHeader('user.nama_lengkap', 'Petugas', true, true),
            TableUtil::setTableHeader('catatan', 'Catatan', true, true),
            TableUtil::setTableHeader('aksi', 'Aksi', false, false),
        ];

        $opnames = TableUtil::paginate($this, $query, $headers, 10);

        return view('livewire.stock-opname', [
            'opnames' => $opnames,
            'headers' => $headers,
        ])->layout('layouts.app', ['title' => $this->title]);
    }

    public function showDetail($id)
    {
        $this->selectedOpname = StockOpnameModel::with(['details.product', 'user', 'approvedBy'])->findOrFail($id);
        $this->opnameDetails = $this->selectedOpname->details;
        $this->dispatch('show-modal', modalId: 'detailOpnameModal');
    }

    #[On('approve-confirmed')]
    public function approve($id)
    {
        $opname = StockOpnameModel::with('details')->findOrFail($id);

        if ($opname->status !== 'draft') {
            $this->dispatch('alert', type: 'error', message: 'Hanya status draft yang dapat disetujui');
            return;
        }

        DB::beginTransaction();

        try {
            // Update Opname Status
            $opname->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'tanggal_approved' => now(),
            ]);

            // Process Stock Adjustments
            foreach ($opname->details as $detail) {
                // Update Product Stock
                $product = \App\Models\Product::find($detail->product_id);
                if ($product) {
                    $product->update(['stok_aktual' => $detail->stok_fisik]);
                }

                // Create Movement if there is a difference
                if ($detail->selisih != 0) {
                    \App\Models\StockMovement::create([
                        'business_id' => $this->businessId,
                        'product_id' => $detail->product_id,
                        'tanggal_perubahan_stok' => $opname->tanggal_opname,
                        'jenis_perubahan' => 'stock opname',
                        'jumlah_perubahan' => $detail->selisih,
                        'reference_id' => $opname->id,
                        'reference_type' => 'stock_opname',
                        'catatan' => $detail->alasan ?? 'Stock Opname Approved',
                    ]);
                }
            }

            DB::commit();
            $this->dispatch('alert', type: 'success', message: 'Stock Opname berhasil disetujui dan stok telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', type: 'error', message: 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $opname = StockOpnameModel::findOrFail($id);

        if ($opname->status !== 'draft') {
            $this->dispatch('alert', type: 'error', message: 'Hanya stock opname status draft yang dapat dihapus');

            return;
        }

        DB::transaction(function () use ($opname) {
            // Delete details
            $opname->details()->delete();
            // Delete opname
            $opname->delete();
        });

        $this->dispatch('alert', type: 'success', message: 'Stock opname berhasil dihapus');
    }
}
