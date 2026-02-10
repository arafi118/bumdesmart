@extends('layouts.pdf')

@section('content')
    <div style="margin-bottom: 20px;">
        <strong>Rangkuman:</strong><br>
        <table style="width: 100%; border: 1px solid #ddd; margin-top: 10px;">
            <tr>
                <th style="background: #f4f4f4;">Total Penjualan</th>
                <th style="background: #f4f4f4;">Jumlah Transaksi</th>
                <th style="background: #f4f4f4;">Rata-rata</th>
            </tr>
            <tr>
                <td style="text-align: right;">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ $summary['total_transactions'] }}</td>
                <td style="text-align: right;">Rp {{ number_format($summary['avg_transaction'], 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <h3>Rincian Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Invoice</th>
                <th>Waktu</th>
                <th>Pelanggan</th>
                <th>Pembayaran</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $index => $sale)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $sale->invoice_number ?? ($sale->code ?? '#' . $sale->id) }}</td>
                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->customer->name ?? 'Guest' }}</td>
                    <td>{{ ucfirst($sale->payment_method ?? 'cash') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                    <td
                        style="text-align: center; color: {{ $sale->status === 'paid' ? 'green' : ($sale->status === 'cancelled' ? 'red' : 'orange') }}">
                        {{ ucfirst($sale->status ?? 'paid') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right;">Total</th>
                <th style="text-align: right;">Rp {{ number_format($sales->sum('total_amount'), 0, ',', '.') }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
@endsection
