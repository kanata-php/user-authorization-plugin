<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .table-wrapper {
            width: 100%;
            margin: 0 auto;
        }

        .table-wrapper .table-content {
            width: 100%;
            margin: 0 auto;
            border: 1px solid #000;
        }

        .table-content .header {
            padding-top: 15px;
            text-align: center;
        }

        .table-content .header img {
            width: 80px;
        }

        .table-content .body {
            padding: 15px;
        }

        .table-content .footer {
            padding: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="table-wrapper">
        <tr>
            <td></td>
            <td width="450">
                <table class="table-content">
                    <tr>
                        <td class="header">
                            <img src="https://avatars.githubusercontent.com/u/98372005?s=200&v=4"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="body">
                            <?=$this->section('content')?>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">@kanataphp.com</td>
                    </tr>
                </table>
            </td>
            <td></td>
        </tr>
    </table>
</body>
</html>