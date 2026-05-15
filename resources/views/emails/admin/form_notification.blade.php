@extends('emails.layout')

@section('title', 'Yêu cầu mới: ' . strtoupper($submission->type))

@section('content')
    <!-- TITLE -->
    <table width="100%">
        <tr>
            <td align="center" style="padding:25px 20px 10px;">
                <h2 style="margin:0;font-size:22px;color:#405189;">
                    Có yêu cầu mới từ hệ thống
                </h2>
            </td>
        </tr>
    </table>

    <!-- BODY CONTENT -->
    <table width="100%">
        <tr>
            <td style="padding:10px 30px 20px;color:#444;font-size:15px;line-height:1.6;">
                <p>Xin chào <strong>Ban quản trị</strong>,</p>
                <p>Hệ thống vừa ghi nhận một yêu cầu mới từ người dùng với thông tin chi tiết như sau:</p>

                <!-- FORM DATA TABLE -->
                <div style="background-color: #f8f9ff; padding: 20px; border-radius: 8px; margin: 25px 0; border: 1px dashed #405189;">
                    <h3 style="margin-top: 0; border-bottom: 2px solid #e1e5f2; padding-bottom: 12px; color: #405189; font-size: 18px;">
                        Chi tiết Form: <span style="color: #0ab39c;">{{ strtoupper($submission->type) }}</span>
                    </h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tbody>
                            @foreach($submission->data as $key => $value)
                            <tr>
                                <td style="padding: 10px 0; border-bottom: 1px solid #e1e5f2; width: 35%; font-weight: bold; text-transform: capitalize; color: #555;">{{ str_replace('_', ' ', $key) }}</td>
                                <td style="padding: 10px 0; border-bottom: 1px solid #e1e5f2; color: #333;">
                                    @if(is_array($value))
                                        {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="background-color: #f3f6f9; padding: 15px; border-radius: 6px; font-size: 14px;">
                    <strong>ID Hệ thống:</strong> #{{ $submission->id }}<br>
                    <strong>Thời gian gửi:</strong> {{ display_datetime($submission->created_at) }}<br>
                    <strong>IP Gửi:</strong> {{ $submission->ip_address }}
                </div>

                <p style="margin-top: 30px; text-align: center;">
                    <a href="{{ route('admin.forms.show', $submission->id) }}" style="display: inline-block; padding: 12px 30px; background-color: #0ab39c; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; box-shadow: 0 4px 6px rgba(10, 179, 156, 0.2);">
                        XEM CHI TIẾT TRÊN ADMIN
                    </a>
                </p>
            </td>
        </tr>
    </table>

    <!-- FOOTER TEXT & SIGN -->
    <table width="100%" style="padding:0 30px 30px;">
        <tr>
            <td align="right" style="padding-top:15px;font-weight:bold;color:#405189;font-size: 16px;">
                EDUQUIZ TEAM
            </td>
        </tr>
    </table>
@endsection
