<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    public $businessId;

    public $todaySales = 0;
    public $monthSales = 0;
    public $todayProfit = 0;
    public $monthProfit = 0;
    public $totalTransactions = 0;

    public $lowStockProducts = [];
    public $topSellingProducts = [];
    public $recentTransactions = [];

    public function mount()
    {
        $this->businessId = auth()->user()->business_id;
        $this->loadData();
    }

    public function loadData()
    {
        $now = now();

        // Sales Metrics
        $this->todaySales = Sale::where('business_id', $this->businessId)
            ->whereDate('created_at', $now->today())
            ->sum('total');

        $this->monthSales = Sale::where('business_id', $this->businessId)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('total');

        $this->totalTransactions = Sale::where('business_id', $this->businessId)
            ->whereDate('created_at', $now->today())
            ->count();

        // Profit Metrics
        $this->todayProfit = SaleDetail::whereHas('sale', function ($q) use ($now) {
            $q->where('business_id', $this->businessId)
                ->whereDate('created_at', $now->today());
        })->sum('profit');

        $this->monthProfit = SaleDetail::whereHas('sale', function ($q) use ($now) {
            $q->where('business_id', $this->businessId)
                ->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year);
        })->sum('profit');

        // Low Stock Products
        $this->lowStockProducts = Product::where('business_id', $this->businessId)
            ->where('is_active', true)
            ->whereColumn('stok_aktual', '<=', 'stok_minimal')
            ->orderBy('stok_aktual', 'asc')
            ->take(5)
            ->get();

        // Top Selling Products (This Month)
        $this->topSellingProducts = SaleDetail::whereHas('sale', function ($q) use ($now) {
            $q->where('business_id', $this->businessId)
                ->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year);
        })
            ->select('product_id', DB::raw('sum(jumlah) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with(['product'])
            ->get();

        // Recent Transactions
        $this->recentTransactions = Sale::where('business_id', $this->businessId)
            ->with('customer')
            ->latest()
            ->take(6)
            ->get();
    }

    #[Layout('layouts.app')]
    #[Title('Dashboard')]
    public function render()
    {
        return view('livewire.dashboard');
    }
}
