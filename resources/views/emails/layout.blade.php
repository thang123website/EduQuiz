<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
</head>
<body style="margin:0;padding:0;background:#f2f2f2;font-family: Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:20px 0;">
    <tr>
        <td align="center">
            <!-- CONTAINER -->
            <table width="650" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:6px;overflow:hidden;box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <!-- HEADER -->
                <tr>
                    <td style="padding:20px 25px;border-bottom:3px solid #405189;">
                        <table width="100%">
                            <tr>
                                <!-- LOGO -->
                                <td style="font-size:28px;font-weight:bold;">
                                    <span style="color:#405189;">Edu</span><span style="color:#0ab39c;">Quiz</span>
                                </td>
                                <!-- SOCIAL -->
                                <td align="right">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" width="24" style="margin-left:8px;" alt="Insta">
                                    <img src="https://cdn-icons-png.flaticon.com/512/733/733585.png" width="24" style="margin-left:8px;" alt="X">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" width="24" style="margin-left:8px;" alt="Youtube">
                                    <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="24" style="margin-left:8px;" alt="FB">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- CONTENT AREA -->
                <tr>
                    <td style="padding:0;">
                        @yield('content')
                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="padding:0 30px 25px;">
                        <table width="100%">
                            <tr>
                                <td style="font-size:13px;color:#888;text-align:center;">
                                    &copy; {{ date('Y') }} EduQuiz Platform. All rights reserved.<br>
                                    Đây là email tự động, vui lòng không trả lời.
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
