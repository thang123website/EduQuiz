<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mã xác nhận (OTP)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #405189;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .content {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }
        .otp-box {
            background-color: #f8f9fa;
            border: 2px dashed #405189;
            text-align: center;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #405189;
            letter-spacing: 5px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777777;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>EduQuiz</h2>
        </div>
        <div class="content">
            <p>Xin chào,</p>
            <p>Chúng tôi nhận được yêu cầu cấp lại mật khẩu hoặc xác thực tài khoản từ bạn. Dưới đây là mã xác nhận (OTP) của bạn:</p>
            
            <div class="otp-box">
                <div class="otp-code">{{ $code }}</div>
            </div>
            
            <p>Mã này có hiệu lực trong vòng <strong>15 phút</strong>. Vui lòng không chia sẻ mã này cho bất kỳ ai để đảm bảo an toàn cho tài khoản của bạn.</p>
            <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
            <br>
            <p>Trân trọng,<br>Đội ngũ EduQuiz</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} EduQuiz. All rights reserved.
        </div>
    </div>
</body>
</html>
