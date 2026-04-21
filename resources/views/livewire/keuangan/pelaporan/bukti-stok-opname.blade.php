@extends('layouts.pdf')

@section('content')
    <div style="margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px;">
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; width: 60%; vertical-align: top;">
                    <h2 style="margin: 0; color: #1a5632;">BUKTI STOCK OPNAME</h2>
                    <p style="margin: 5px 0; font-size: 11pt;">No: <strong>{{ $opname->no_opname }}</strong></p>
                </td>
                <td style="border: none; width: 40%; text-align: right; vertical-align: top;">
                    <p style="margin: 0; font-size: 10pt;">Tanggal: {{ \Carbon\Carbon::parse($opname->tanggal_opname)->format('d F Y') }}</p>
                    <p style="margin: 5px 0; font-size: 10pt;">Status: <span style="text-transform: uppercase; font-weight: bold; color: {{ $opname->status == 'approved' ? 'green' : 'orange' }}">{{ $opname->status }}</span></p>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 20px;">
        <table style="border: none; width: 100%; font-size: 10pt;">
            <tr>
                <td style="border: none; width: 15%;">Petugas</td>
                <td style="border: none; width: 35%;">: {{ $opname->user->nama_lengkap ?? '-' }}</td>
                <td style="border: none; width: 15%;">Disetujui Oleh</td>
                <td style="border: none; width: 35%;">: {{ $opname->approvedBy->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border: none;">Catatan</td>
                <td style="border: none;" colspan="3">: {{ $opname->catatan ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Produk</th>
                <th style="width: 12%;" class="text-center">Stok Sistem</th>
                <th style="width: 12%;" class="text-center">Stok Fisik</th>
                <th style="width: 10%;" class="text-center">Selisih</th>
                <th style="width: 26%;">Alasan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($opname->details as $i => $detail)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $detail->product->nama_produk ?? '-' }}</strong><br>
                        <small style="color: #666;">{{ $detail->product->kode_produk ?? '' }}</small>
                    </td>
                    <td class="text-center">{{ $detail->stok_sistem }}</td>
                    <td class="text-center">{{ $detail->stok_fisik }}</td>
                    <td class="text-center"
                        style="color: {{ $detail->selisih < 0 ? '#d9534f' : ($detail->selisih > 0 ? '#5cb85c' : '#333') }}; font-weight: bold;">
                        {{ $detail->selisih > 0 ? '+' : '' }}{{ $detail->selisih }}
                    </td>
                    <td>{{ $detail->alasan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <table style="border: none; width: 100%;">
            <tr>
                <td style="border: none; width: 33%; text-align: center;">
                    <p>Petugas</p>
                    <br><br><br>
                    <p>( ____________________ )</p>
                </td>
                <td style="border: none; width: 33%; text-align: center;">
                </td>
                <td style="border: none; width: 33%; text-align: center;">
                    <p>Pimpinan / Approval</p>
                    <br><br><br>
                    <p>( ____________________ )</p>
                </td>
            </tr>
        </table>
    </div>

    <div style="position: fixed; bottom: 0; width: 100%; font-size: 8pt; color: #777; text-align: center; border-top: 1px solid #ddd; padding-top: 5px;">
        Bukti Stock Opname - Dicetak pada {{ date('d/m/Y H:i:s') }}
    </div>
@endsection
