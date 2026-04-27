<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        .kop {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .kop-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }
        .kop-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .kop-logo {
            width: 80px;
            text-align: center;
        }
        .kop-logo img {
            max-width: 70px;
            max-height: 70px;
        }
        .kop-text {
            padding-left: 15px;
            text-align: left;
        }
        .kop-nama {
            font-size: 18pt;
            font-weight: bold;
            color: #000;
            margin: 0;
            line-height: 1.2;
        }
        .kop-alamat {
            font-size: 10pt;
            color: #333;
            margin: 4px 0 0 0;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="kop">
        <table class="kop-table">
            <tr>
                @if ($base64Logo)
                    <td class="kop-logo">
                        <img src="{{ $base64Logo }}" alt="Logo">
                    </td>
                @endif
                <td class="kop-text">
                    <p class="kop-nama">{{ $namaUsaha }}</p>
                    <p class="kop-alamat">
                        {{ $alamatUsaha }}
                        @if ($telpUsaha)
                            <br>Telp: {{ $telpUsaha }}
                        @endif
                        @if ($emailUsaha)
                            | Email: {{ $emailUsaha }}
                        @endif
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
