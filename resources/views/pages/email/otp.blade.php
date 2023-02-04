<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,500,700,300' rel='stylesheet' type='text/css'>
    <style type="text/css">
        /*Global Styles*/
        body {
            background: transparent;
            margin: 0;
            padding: 0;
            min-width: 100%!important;
        }
        a {
            color: #333333;
            text-decoration: none;
        }
        img {
            height: auto;
        }
        .content {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        /*Media Queries*/
        @media only screen and (max-width: 721px) {
            .columns {
                width: 100% !important;
            }
            .columncontainer {
                display: block !important;
                width: 100% !important;
            }
            .listitem,
            .role {
                font-size: 9px;
            }
            .role img {
                display: none
            }
            .role a {
                display: block
            }
            .role .divid {
                display: none
            }
            .footer {
                font-size: 9.5px;
            }
            .name {
                font-size: 16px;
            }
        }
        @media only screen and (min-width: 721px) {
            .content {
                width: 720px !important;
            }
            .role {
                font-size: 13px;
            }
            .footer {
                font-size: 13px;
            }
        }
    </style>
</head>
<body style="font-family: 'Roboto', sans-serif;font-size:13px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <!--[if (gte mso 9)|(IE)]>
                  <table width="540" align="center" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td>
                <![endif]-->
                <!--Content Wrapper-->
                <table class="content" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="padding:0 10px 30px 10px">
                            <p style="line-height:20px;">
                                Hai,<br><br>Kami menerima permintaan akses menu khusus di <span style="font-weight: bold;color: #404042;">ScanEat</span>. Berikut adalah informasi anda.
                            </p>
                            <div style="padding:15px;line-height:25px">
                                <table style="width: 100%">
                                    <tr>
                                        <td style="width: 40%">Waktu</td><td style="width: 60%">: {{ date('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%">Nama</td><td style="width: 60%">: {{ $customer['name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%">Email</td><td style="width: 60%">: {{ $customer['email'] }}</td>
                                    </tr>
                                  <tr>
                                        <td style="width: 40%">Website</td><td style="width: 60%">: {{ $customer['url'] }}</td>
                                    </tr>
                                     <tr>
                                        <td style="width: 40%">IP Address</td><td style="width: 60%">: {{ $customer['ip'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%">Browser</td><td style="width: 60%">: {{ $customer['browser'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div style="margin-top:10px">
                                <p style="line-height:20px;">
                                    Untuk melindungi data Catalog anda, kami membatasi akses tersebut.<br>
                                    Berikut adalah kode OTP yang diperlukan : <b>{{ $customer['otp'] }}</b><br>
                                    Kode OTP berlaku hanya 1 jam.
                                </p>
                            </div>
                          <div style="margin-top:10px">
                                <p style="line-height:20px;">
                                    Demi keamanan akun anda, mohon untuk tidak memberitahukan kode OTP kepada siapapun.
                                </p>
                            </div>
                        </td>
                    </tr>
                    <!--Signature-->
                    <tr>
                        <td style="padding: 0px 0px 30px 0px;border-top: 1px solid #e8e8e8; border-bottom: 1px solid #e8e8e8;">
                            <table border="0" cellpadding="0" cellspacing="0" width="720" class="columns">
                                <tr valign="top">
                                    <td width="10%" class="columncontainer" style="display:block; width:120px; margin-right: 30px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td height="150" style="display: block; width: 120px; height: 120px; text-align: center; font-family: sans-serif; line-height: 120px; font-size: 52px; color: #ffffff; margin: 30px 0px 0px 10px;">
                                                    <a href="http://scaneat.id">
                                                        <img src="https://scaneat.id/assets/img/logo.png" width="100">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="90%" class="columncontainer">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td valign="top" style="padding: 30px 30px 0px 10px;">
                                                    <table border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td class="name">
                                                                <span style="font-size: 16px;font-weight: bold;color: #404042;">ScanEat</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="role" style="padding: 10px 0 0 0;">
                                                                <a href="tel:"><span style="position: relative;top: -5px;left: 1%;">085157130040</span></a>
                                                                <a href="#" style="margin:0 11px;"><span class="divid" style="position: relative;top: -5px;">|</span>
                                                                    </a>
                                                                <a href="mailto:admin@scaneat.com"><span style="position: relative;top: -5px;">admin@scaneat.id</span></a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!--Legal-->
                    <tr>
                        <td style="padding: 10px 30px 30px 10px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="footer" style="padding: 20px 0 0 0;line-height:25px">
                                        Disclaimer: This message contains confidential information and is intended only for the addressee. If you are not addressee
                                        you should not disseminate, distribute or copy this e-mail. Please notify the sender
                                        if you receive this e-mail by mistake and delete this e-mail from your system. Thank
                                        you.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
