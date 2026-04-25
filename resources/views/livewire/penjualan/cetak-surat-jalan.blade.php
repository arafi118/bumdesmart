<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan {{ $sale->no_invoice }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .business-info {
            width: 60%;
        }

        .business-info h2 {
            margin: 0;
            font-size: 20px;
        }

        .business-info p {
            margin: 2px 0;
            font-size: 12px;
        }

        .document-title {
            width: 35%;
            text-align: right;
        }

        .document-title h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .info-box {
            width: 45%;
        }

        .info-box h4 {
            margin: 0 0 5px 0;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        .info-box p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-space {
            height: 80px;
        }

        .text-center {
            text-align: center;
        }

        @media print {
            body {
                padding: 0;
            }

            .d-print-none {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        <div class="d-print-none" style="margin-bottom: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px;">Cetak Surat Jalan</button>
            <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 4px;">Tutup</button>
        </div>

        @php
            $logoUrl = $owner && $owner->logo ? asset('storage/' . $owner->logo) : null;
        @endphp

        <div class="header">
            <div class="business-info">
                <div style="display: flex; align-items: center;">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" style="max-width: 80px; max-height: 80px; margin-right: 15px;">
                    @endif
                    <div>
                        <h2>{{ $business->nama_usaha ?? $owner->nama_usaha ?? '' }}</h2>
                        <p>{{ $business->alamat ?? 'Alamat Toko Belum Diatur' }}</p>
                        <p>Telp: {{ $business->no_telp ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="document-title">
                <h1>SURAT JALAN</h1>
                <p>No: {{ $sale->no_invoice }}</p>
                <p>Tanggal: {{ date('d/m/Y', strtotime($sale->tanggal_transaksi)) }}</p>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h4>Kepada Yth:</h4>
                <p><strong>{{ $sale->customer->nama_pelanggan ?? 'Umum' }}</strong></p>
                <p>{{ $sale->customer->alamat ?? '-' }}</p>
                <p>{{ $sale->customer->no_hp ?? '-' }}</p>
            </div>
            <div class="info-box">
                <h4>Keterangan:</h4>
                <p>{{ $sale->keterangan ?: 'Delivery of items' }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">No</th>
                    <th>Nama Barang</th>
                    <th style="width: 100px;" class="text-center">Jumlah</th>
                    <th style="width: 100px;">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->saleDetails as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->product->nama_produk ?? 'Produk' }}</td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td>{{ $item->product->unit->nama_satuan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>Barang-barang tersebut di atas telah diterima dalam keadaan baik dan cukup.</p>

        <div class="signature-section">
            <div class="signature-box">
                <p>Penerima,</p>
                <div class="signature-space"></div>
                <p>( ................................ )</p>
            </div>
            <div class="signature-box">
                <p>Hormat Kami,</p>
                <div class="signature-space"></div>
                <p>( {{ $sale->user->nama_lengkap ?? 'Admin' }} )</p>
            </div>
        </div>

        <div style="margin-top: 30px; font-size: 10px; color: #666; text-align: center;">
            <p>Printed on: {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>

</html>
