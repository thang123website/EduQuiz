@extends('emails.layout')

@section('title', $title)

@section('content')
    <!-- TITLE -->
    <table width="100%">
        <tr>
            <td align="center" style="padding:25px 20px 10px;">
                <h2 style="margin:0;font-size:22px;color:#333;">
                    {{ $title }}
                </h2>
            </td>
        </tr>
    </table>

    <!-- BODY CONTENT -->
    <table width="100%">
        <tr>
            <td style="padding:10px 30px 20px;color:#444;font-size:15px;line-height:1.6;">
                <p>Xin chào <strong>{{ $name }}</strong>,</p>

                @if(!empty($image))
                    <div style="margin: 20px 0; text-align: center;">
                        <img src="{{ $image }}" style="max-width: 100%; border-radius: 8px;" alt="Banner">
                    </div>
                @endif

                <div style="color: #555;">
                    {!! nl2br(e($body)) !!}
                </div>

                @if(!empty($url))
                    <p style="margin-top: 30px; text-align: center;">
                        <a href="{{ $url }}" style="display: inline-block; padding: 12px 30px; background-color: #0ab39c; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                            Truy cập để xem chi tiết
                        </a>
                    </p>
                @endif
            </td>
        </tr>
    </table>

    <!-- SUPPORT BOX -->
    <table width="100%" style="padding:0 30px 25px;">
        <tr>
            <td>
                <table width="100%" style="border:1px dashed #405189;padding:20px;background-color: #f8f9ff;">
                    <tr>
                        <td style="font-size:14px;color:#444;line-height:1.6;">
                            <strong style="color: #405189;">EduQuiz luôn sẵn sàng hỗ trợ bạn</strong><br>
                            Email: support@eduquiz.com<br>
                            Hotline: 0123 456 789<br>
                            Giờ làm việc: 08:00 - 21:00 (Mỗi ngày)
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- FOOTER TEXT & SIGN -->
    <table width="100%" style="padding:0 30px 30px;">
        <tr>
            <td style="font-size:14px;color:#444;">
                Một lần nữa, <strong>EduQuiz</strong> xin cảm ơn bạn đã sử dụng hệ thống.
            </td>
        </tr>
        <tr>
            <td align="right" style="padding-top:15px;font-weight:bold;color:#405189;font-size: 16px;">
                EDUQUIZ TEAM
            </td>
        </tr>
    </table>
@endsection
