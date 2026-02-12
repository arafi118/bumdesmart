@php
    use App\Utils\KeuanganUtil;
@endphp

@extends('layouts.pdf')

@section('content')
    <table style="width: 100%; border: 0;">
        @foreach ($akunLevel1s as $akunLevel1)
            <tr style="background-color: #b0b0b0;">
                <td colspan="3" style="text-align: center; border: 0; ">
                    {{ $akunLevel1->kode }}. {{ $akunLevel1->nama }}
                </td>
            </tr>

            @foreach ($akunLevel1->akunLevel2 as $akunLevel2)
                <tr style="background-color: #d0d0d0;">
                    <td style="border: 0;">
                        {{ $akunLevel2->kode }}.
                    </td>
                    <td colspan="2" style="border: 0;">
                        {{ $akunLevel2->nama }}
                    </td>
                </tr>

                @foreach ($akunLevel2->akunLevel3 as $index => $akunLevel3)
                    @php
                        $saldoAkun = 0;
                        foreach ($akunLevel3->accounts as $account) {
                            $saldo = KeuanganUtil::sumSaldo($account, $bulan);

                            $saldoAkun += $saldo;
                        }
                    @endphp

                    <tr style="background-color: {{ $index % 2 == 0 ? '#f0f0f0' : '#fefefe' }};">
                        <td style="border: 0; width: 10%;">
                            {{ $akunLevel3->kode }}.
                        </td>
                        <td style="border: 0;width: 60%;">
                            {{ $akunLevel3->nama }}
                        </td>
                        <td style="border: 0; text-align: right; width: 30%;">
                            {{ number_format($saldoAkun, 2) }}
                        </td>
                    </tr>
                @endforeach
            @endforeach

            <tr style="background-color: #b0b0b0;">
                <td colspan="3" style="text-align: center; border: 0; ">
                    {{ $akunLevel1->kode }}. {{ $akunLevel1->nama }}
                </td>
            </tr>
            <tr>
                <td style="height: 8px !important; border: 0; padding: 0;"></td>
            </tr>
        @endforeach
    </table>
@endsection
