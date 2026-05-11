<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Assigned</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 20px; }
        .card { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .header { background: #4f46e5; padding: 24px 32px; color: #fff; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 700; }
        .header p { margin: 6px 0 0; font-size: 13px; opacity: .85; }
        .body { padding: 28px 32px; }
        .label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; margin-bottom: 4px; }
        .value { font-size: 15px; color: #111827; margin-bottom: 20px; }
        .severity-badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .sev-low { background: #f1f5f9; color: #475569; }
        .sev-medium { background: #fef3c7; color: #92400e; }
        .sev-high { background: #ffedd5; color: #9a3412; }
        .sev-critical { background: #fee2e2; color: #991b1b; }
        .footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
        .btn { display: inline-block; margin-top: 8px; padding: 10px 24px; background: #4f46e5; color: #fff; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Alert Assigned to You</h1>
            <p>BCC Church Management System</p>
        </div>
        <div class="body">
            <p style="margin:0 0 20px;color:#374151;">Hi {{ $leader->full_name }}, an alert has been assigned to you. Please review and take action.</p>

            <div class="label">Alert Title</div>
            <div class="value">{{ $alert->title }}</div>

            <div class="label">Message</div>
            <div class="value" style="color:#4b5563;font-size:14px;">{{ $alert->message }}</div>

            <div class="label">Severity</div>
            <div class="value">
                <span class="severity-badge sev-{{ $alert->severity }}">{{ ucfirst($alert->severity) }}</span>
            </div>

            @if($alert->due_at)
            <div class="label">Due Date</div>
            <div class="value">{{ $alert->due_at->format('d M Y, H:i') }}</div>
            @endif

            <a class="btn" href="{{ config('app.url') }}/alerts">View Alert in System →</a>
        </div>
        <div class="footer">
            This is an automated message from BCC CMS. Please do not reply to this email.
        </div>
    </div>
</body>
</html>
