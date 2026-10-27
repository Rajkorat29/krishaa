<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>New Customer Message</title>
  <style>
    body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:#F7F4FB;margin:0;padding:20px;color:#1B1530}
    .card{background:#FFFFFF;border-radius:18px;max-width:600px;margin:0 auto;padding:28px;border:1px solid #D9D1E8}
    .header{border-bottom:2px solid #1B1530;padding-bottom:16px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center}
    .badge{display:inline-block;padding:4px 10px;background:#2D3BD1;color:#FFF;border-radius:999px;font-size:12px;font-weight:700}
    .info-box{background:#F7F4FB;border-radius:12px;padding:14px;margin-bottom:16px;font-size:14px}
    .btn{display:inline-block;background:#1B1530;color:#FFFFFF;text-decoration:none;padding:10px 20px;border-radius:999px;font-weight:600;font-size:14px;margin-top:16px}
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h2 style="margin:0;font-size:22px;color:#1B1530">💎 daisy <small style="color:#2D3BD1;font-size:13px">INQUIRY</small></h2>
      <span class="badge">Contact Form</span>
    </div>

    <p style="font-size:16px;margin:0 0 16px">
      You have received a new customer message on <strong>Daisy</strong>:
    </p>

    <div class="info-box">
      <p style="margin:0 0 6px"><strong>From:</strong> {{ $contactMessage->name }}</p>
      <p style="margin:0 0 6px"><strong>Email:</strong> <a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></p>
      @if($contactMessage->phone)
        <p style="margin:0 0 6px"><strong>Phone:</strong> {{ $contactMessage->phone }}</p>
      @endif
      <p style="margin:0"><strong>Received At:</strong> {{ $contactMessage->created_at->format('d M Y, h:i A') }}</p>
    </div>

    <h3 style="margin:18px 0 8px;font-size:15px;text-transform:uppercase;color:#645C7E">Message</h3>
    <div class="info-box" style="background:#FFF;border:1px solid #D9D1E8;white-space:pre-wrap;font-size:15px;line-height:1.6">
{{ $contactMessage->message }}
    </div>

    <div style="text-align:center;margin-top:24px">
      <a href="{{ route('admin.inbox.index') }}" class="btn">View in Admin Inbox →</a>
    </div>
  </div>
</body>
</html>
