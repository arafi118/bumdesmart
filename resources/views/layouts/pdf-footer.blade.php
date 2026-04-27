<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 8pt;
            margin: 0;
            padding: 0;
            color: #999;
        }
        .footer {
            width: 100%;
            text-align: right;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
    </style>
    <script>
        function subst() {
            var vars = {};
            var query_strings_from_url = document.location.search.substring(1).split('&');
            for (var query_string in query_strings_from_url) {
                var cas = query_strings_from_url[query_string].split('=', 2);
                vars[cas[0]] = decodeURIComponent(cas[1]);
            }
            var ids = ['page', 'frompage', 'topage', 'webpage', 'section', 'subsection', 'datepicker', 'isodate', 'time', 'title', 'doctitle', 'sitepage', 'sitepages'];
            for (var id in ids) {
                var elmt = document.getElementById(ids[id]);
                if (elmt) {
                    elmt.textContent = vars[ids[id]];
                }
            }
        }
    </script>
</head>
<body onload="subst()">
    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i') }} | Hal. <span id="page"></span> dari <span id="topage"></span>
    </div>
</body>
</html>
