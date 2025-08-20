<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi</title>
    <!--
        Email client compatibility is best with a combination of tables and inline styles.
        This <style> block is for modern clients, while the table structure ensures
        it renders correctly on older ones.
    -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f4f4;
            padding: 20px 0;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-width: 180px;
            height: auto;
        }
        .content {
            padding: 30px;
            text-align: center;
            line-height: 1.6;
        }
        .code-box {
            background-color: #f9f9f9;
            border: 2px dashed #c10000;
            border-radius: 8px;
            padding: 20px;
            margin: 25px auto;
            max-width: 300px;
        }
        .verification-code {
            font-size: 36px;
            font-weight: bold;
            color: #c10000;
            letter-spacing: 5px;
        }
        .call-to-action-btn {
            display: inline-block;
            background-color: #c10000;
            color: #ffffff !important;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            background-color: #c10000;
            color: #ffffff;
            text-align: center;
            padding: 15px;
            font-size: 12px;
        }
    </style>
</head>
<body>
<center class="email-wrapper">
    <table class="email-container" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
        <!-- Header Section with Logo -->

        <!-- Content Section -->
        <tr>
            <td class="content">
                <h1>Hai {{$name}}, Verifikasi Alamat Email Anda</h1>
                <p>Terima kasih telah mendaftar. Untuk menyelesaikan proses pembuatan akun, silakan gunakan kode verifikasi di bawah ini:</p>

                <!-- Verification Code Box -->
                <table class="code-box" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tr>
                        <td>
                            <p class="verification-code">
                                <!-- REPLACE_WITH_YOUR_VERIFICATION_CODE -->
                                {{$code}}
                            </p>
                        </td>
                    </tr>
                </table>

                <p>Kode ini berlaku selama 10 menit dan sampai {{$expired_at}}. Jika Anda tidak melakukan permintaan ini, mohon abaikan email ini.</p>
            </td>
        </tr>

        <!-- Footer Section -->
        <tr>
            <td class="footer">
                &copy; 2024 Abinggo Prima Group. Semua Hak Cipta Dilindungi.
            </td>
        </tr>
    </table>
</center>
</body>
</html>
