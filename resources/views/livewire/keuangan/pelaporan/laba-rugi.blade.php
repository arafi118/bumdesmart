@extends('layouts.pdf')

@section('content')
    <table style="width: 100%; border: 0;">
        @foreach ($labaRugi as $index => $lr)
            <tr style="background-color: #b0b0b0;">
                <td colspan="3" style="text-align: center; font-weight: bold; text-transform: uppercase; border: 0;">
                    {{ $lr['nama'] }}
                </td>
            </tr>

            @foreach ($lr['kode'] as $index2 => $kode)
                <tr style="background-color: {{ $index2 % 2 == 0 ? '#f0f0f0' : '#fefefe' }};">
                    <td style="width: 10%; text-align: center; border: 0;">{{ $kode['kode'] }}</td>
                    <td style="width: 75%; border: 0;">{{ $kode['nama'] }}</td>
                    <td style="width: 15%; text-align: right; border: 0;">{{ number_format($kode['saldo_bulan_ini'], 2) }}
                    </td>
                </tr>
            @endforeach

            @if ($index > 0)
                <tr style="background-color: #d0d0d0;">
                    <td colspan="2" style="font-weight: bold; border: 0;">Jumlah {{ $lr['nama'] }}</td>
                    <td style="width: 15%; text-align: right; font-weight: bold; border: 0;">
                        {{ number_format($lr['jumlah'], 2) }}</td>
                </tr>
            @endif

            @if ($index == 4)
                <tr style="background-color: #b0b0b0;">
                    <td colspan="2" style="font-weight: bold; border: 0;">Laba Rugi Sebelum Pajak</td>
                    <td style="width: 15%; text-align: right; font-weight: bold; border: 0;">
                        {{ number_format($lr['total'], 2) }}</td>
                </tr>
            @endif

            @if ($index == 5)
                <tr style="background-color: #b0b0b0;">
                    <td colspan="2" style="font-weight: bold; border: 0;">Laba Rugi</td>
                    <td style="width: 15%; text-align: right; font-weight: bold; border: 0;">
                        {{ number_format($lr['total'], 2) }}</td>
                </tr>
            @endif

            <tr>
                <td colspan="3" style="height: 2px; border: 0;"></td>
            </tr>
        @endforeach
    </table>
@endsection
