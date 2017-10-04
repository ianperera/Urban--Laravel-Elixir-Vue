<!doctype html>
<html>
    <head>
        <style type="text/css">
            html, body {
                font-family: Arial, sans-serif;
                font-size: 13px;
            }
            p { margin: 0; padding: 0; }
            th {
                font-weight: normal;
                text-align: left;
            }
            .text-left {
                text-align: left;
            }
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
        </style>
    </head>
	<body>
        <table style="margin-bottom: 15px;">
            <tr>
                <td style="width: 125px; text-align: left;">
                    <img src="{{ url('images/logo-usc.png') }}">
                </td>
                <td style="text-align: left;">
                    <div style="font-size: 22px; margin-top: 25px;">
                        <strong>urbanshedconcepts.com</strong>
                        <br>
                        info@urbanshedconcepts.com
                        <br>
                        602.320.8350
                    </div>
                </td>
            </tr>
        </table>
        @yield('content')
	</body>
</html>
