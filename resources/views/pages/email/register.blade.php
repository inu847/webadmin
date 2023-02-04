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
                            <p style="line-height:25px;">
                                Terima kasih telah melakukan pendaftaran di <span style="font-weight: bold;color: #404042;">YoScan</span>. Berikut adalah informasi pendaftaran anda.
                            </p>
                            <div style="padding:15px;line-height:25px">
                                <table style="width: 100%">
                                    <tr>
                                        <td style="width: 40%">Nomor Invoice</td><td style="width: 60%"><b>: {{ $register['invoice'] }}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%">Status</td><td style="width: 60%"><b>: {{ $status }}</b></td>
                                    </tr>
                                    @if(!empty($notes))
                                    <tr>
                                        <td style="width: 40%">Keterangan</td>
                                        <td style="width: 60%"><b>: {{ $notes }}</b></td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="width: 40%">Tanggal Pendaftaran</td><td style="width: 60%">: {{ Date::myDate($register['created_at']) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%">Nama</td><td style="width: 60%">: {{ $customer['name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%">Phone</td><td style="width: 60%">: {{ $customer['phone'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div style="margin-top:30px;background:#f7f7fe;border:1px dashed #DDD">
                                <div style="padding:15px;line-height:25px">
                                    <table style="width: 100%">
                                        <tr>
                                            <td style="width: 40%">Paket Pendaftaran</td>
                                            <td style="width: 60%">: {{ $register['package_name'] }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 40%">Harga</td>
                                            <td style="width: 60%">: Rp. {{ ($register['price']==0)?'Gratis':number_format($register['price']) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 40%">Masa Berlaku</td>
                                            <td style="width: 60%">: {{ $register['duration'] }}</td>
                                        </tr>
                                        @if(!empty($expired))
                                        <tr>
                                            <td style="width: 40%">Berlaku Sampai</td>
                                            <td style="width: 60%">: {{ Date::myDate($expired) }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            @if($status == 'Approved')
                            <div style="margin-top:30px;">
                                <p style="line-height:25px;">
                                    Informasi Login :
                                </p>
                                <div style="background:#f7f7fe;border:1px dashed #DDD">
                                    <div style="padding:15px;line-height:25px">
                                        <table style="width: 100%">
                                            <tr>
                                                <td style="width: 40%">Email</td>
                                                <td style="width: 60%">: {{ $register['email'] }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 40%">Password</td>
                                                <td style="width: 60%">: {{ $register['temp_password'] }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <p style="line-height:25px;">
                                    Anda dapat mulai mengelola Menu Digital dengan mengunjungi halaman berikut <a href="https://admin.yoscan.id"><b><u>admin.yoscan.id</u></b></a>.
                                </p>
                            </div>
                            @endif

                            @if($register['confirmation']=='Y')
                            <div style="margin-top:30px">
                                <p style="line-height:25px;">
                                    Tujuan Pembayaran : 
                                </p>
                                <div style="border:1px solid #DDD">
                                    <div style="padding:15px;line-height:25px">
                                        {!! $account_to !!}
                                    </div>
                                </div>
                            </div>
                            @endif
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
                                                    <a href="http://yoscan.id">
                                                        <img src="http://yoscan.id/assets/img/yoscan-square.png" width="100">
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
                                                                <span style="font-size: 16px;font-weight: bold;color: #404042;">YoScan</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="role" style="padding: 10px 0 0 0;">
                                                                <a href="tel:"><span style="position: relative;top: -5px;left: 1%;">(021) 765 0987</span></a>
                                                                <a href="#" style="margin:0 11px;"><span class="divid" style="position: relative;top: -5px;">|</span>
                                                                    </a>
                                                                <a href="mailto:hello@yoscan.com"><span style="position: relative;top: -5px;">hello@yoscan.com</span></a>
                                                                <a href="#" style="margin:0 11px;"><span class="divid" style="position: relative;top: -5px;">|</span>
                                                                    </a>
                                                                <a href="http://yoscan.id" target="_blank">yoscan.id</a>
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
