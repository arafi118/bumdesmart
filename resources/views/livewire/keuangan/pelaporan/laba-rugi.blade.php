@extends('layouts.pdf')

@section('content')
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 50%; border: none; padding: 10px; background: #f8f8f8;">
                <strong>Total Penjualan (Revenue)</strong><br>
                <span style="font-size: 14pt; font-weight: bold;">Rp
                    {{ number_format($summary['total_revenue'], 0, ',', '.') }}</span>
            </td>
            <td style="width: 50%; border: none; padding: 10px; background: #f8f8f8;">
                <strong>Gross Profit Margin</strong><br>
                <span style="font-size: 14pt; font-weight: bold;">{{ number_format($summary['gross_margin'], 1) }}%</span>
            </td>
        </tr>
    </table>

    <h3>Rincian Laba Rugi</h3>
    <table>
        <tbody>
            <tr>
                <td style="padding: 8px;"><strong>Total Penjualan (Revenue)</strong></td>
                <td class="text-right" style="padding: 8px;">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px;">Harga Pokok Penjualan (HPP)</td>
                <td class="text-right" style="padding: 8px; color: red;">(Rp
                    {{ number_format($summary['total_hpp'], 0, ',', '.') }})</td>
            </tr>
            <tr style="background: #e8f5e9; font-weight: bold;">
                <td style="padding: 8px;"><strong>Laba Kotor (Gross Profit)</strong></td>
                <td class="text-right" style="padding: 8px;">Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px;">Gross Profit Margin</td>
                <td class="text-right" style="padding: 8px;">{{ number_format($summary['gross_margin'], 1) }}%</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 15px; font-size: 9pt; color: #777;">
        <em>* Biaya operasional (gaji, listrik, sewa) belum diperhitungkan. Laba bersih = Laba Kotor - Biaya
            Operasional.</em>
    </p>
@endsection
