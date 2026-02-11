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
                    <td>{{ $sale->no_invoice }}</td>
                    <td>{{ $sale->tanggal_transaksi }}</td>
                    <td>{{ $sale->customer->nama_pelanggan ?? 'Guest' }}</td>
                    <td>{{ ucfirst($sale->jenis_pembayaran ?? 'cash') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                    <td
                        style="text-align: center; color: {{ $sale->status === 'paid' || $sale->status === 'completed' ? 'green' : ($sale->status === 'cancelled' ? 'red' : 'orange') }}">
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
