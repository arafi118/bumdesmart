<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan {{ $sale->no_invoice }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 5mm;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .container {
            width: 100%;
            padding: 10px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .header-table td {
            border: none;
            padding: 1px;
            vertical-align: top;
        }

        .business-logo {
            max-width: 60px;
            max-height: 40px;
            margin-bottom: 2px;
        }

        .doc-title {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            line-height: 1;
        }

        .label-col {
            width: 100px;
        }

        .value-col {
            width: 10px;
            text-align: center;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.main-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
        }

        table.main-table td {
            padding: 4px 5px;
            border-bottom: 0.5px solid #eee;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }

        .footer-section {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
        }

        .signature-section {
            width: 60%;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-space {
            height: 40px;
        }

        .total-box {
            width: 35%;
            text-align: right;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        .notes {
            font-size: 9px;
            margin-top: 10px;
            border-top: 1px dotted #000;
            padding-top: 5px;
        }

        @media print {
            .d-print-none { display: none; }
            body { padding: 0; }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        <!-- Control Buttons -->
        <div class="d-print-none" style="margin-bottom: 20px; text-align: center; padding: 10px; background: #f0f0f0;">
            <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer; background: #28a745; color: white; border: none; border-radius: 4px; font-weight: bold;">🖨️ CETAK</button>
            <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 4px; font-weight: bold; margin-left: 10px;">❌ TUTUP</button>
        </div>

        @php
            $logoUrl = $owner && $owner->logo ? asset('storage/' . $owner->logo) : null;
            $totalQty = $sale->saleDetails->sum('jumlah');
            $tanggal = date('d-m-Y', strtotime($sale->tanggal_transaksi));
            $tempo = $sale->jumlah_utang > 0 ? date('d-m-Y', strtotime($sale->tanggal_transaksi . ' + 30 days')) : '-';
        @endphp

        <!-- Header -->
        <table class="header-table">
            <tr>
                <!-- Left Side: Business Info -->
                <td style="width: 50%;">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="business-logo">
                    @else
                        <div style="font-size: 18px; font-weight: bold;">{{ $business->nama_usaha ?? '' }}</div>
                    @endif
                    <div class="doc-title">SURAT JALAN</div>
                    <div style="margin-top: 5px;">
                        <table>
                            <tr>
                                <td class="label-col">Phone</td>
                                <td class="value-col">:</td>
                                <td>{{ $business->no_telp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">No. Transaksi</td>
                                <td class="value-col">:</td>
                                <td>{{ $sale->no_invoice }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">Admin/Marketing</td>
                                <td class="value-col">:</td>
                                <td>{{ strtoupper($sale->user->nama_lengkap ?? 'Admin') }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
                <!-- Right Side: Customer Info -->
                <td style="width: 50%;">
                    <div style="margin-top: 25px;">
                        <table>
                            <tr>
                                <td class="label-col">Kepada</td>
                                <td class="value-col">:</td>
                                <td class="fw-bold">{{ $sale->customer->nama_pelanggan ?? 'Umum' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">Alamat</td>
                                <td class="value-col">:</td>
                                <td>{{ $sale->customer->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="height: 10px;"></td>
                            </tr>
                            <tr>
                                <td class="label-col">Tanggal</td>
                                <td class="value-col">:</td>
                                <td>{{ $tanggal }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">Tempo</td>
                                <td class="value-col">:</td>
                                <td>{{ $tempo }}</td>
                            </tr>
                            <tr>
                                <td class="label-col">Kode Toko</td>
                                <td class="value-col">:</td>
                                <td>{{ $sale->customer->kode_pelanggan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 150px;">Barcode</th>
                    <th>Nama Barang</th>
                    <th style="width: 120px;" class="text-center">Isi Per Dus</th>
                    <th style="width: 80px;" class="text-right">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->saleDetails as $item)
                    <tr>
                        <td>{{ $item->product->barcode ?? $item->product->sku ?? '-' }}</td>
                        <td>{{ $item->product->nama_produk ?? 'Produk' }}</td>
                        <td class="text-center">@1 {{ $item->product->unit->nama_satuan ?? 'PCS' }}</td>
                        <td class="text-right">{{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->product->unit->nama_satuan ?? 'PCS' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="fw-bold" style="text-align: right; margin-right: 5px;">
            Total Qty {{ number_format($totalQty, 0, ',', '.') }}
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 10px; font-weight: bold; text-transform: uppercase;">
            HARGA SUDAH TERMASUK PPN 11%
        </div>
        <div style="text-align: center; font-size: 9px; margin-top: 5px;">
            Komplain barang dan harga, diterima paling lambat 7 (tujuh) hari dari tanggal terima. Selebihnya dianggap setuju.
        </div>

        <div class="footer-section">
            <div class="signature-section">
                <div class="signature-box">
                    <p>Diterima Oleh,</p>
                    <div class="signature-space"></div>
                    <p>( ................................ )</p>
                </div>
                <div class="signature-box">
                    <p>Hormat Kami,</p>
                    <div class="signature-space"></div>
                    <p>( {{ strtoupper($sale->user->nama_lengkap ?? 'Admin') }} )</p>
                </div>
            </div>
        </div>

        <div class="notes">
            Printed on: {{ date('d/m/Y H:i:s') }}
        </div>
    </div>
</body>

</html>
