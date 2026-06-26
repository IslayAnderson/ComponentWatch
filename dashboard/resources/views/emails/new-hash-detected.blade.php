<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #374151; padding: 32px; max-width: 600px;">
    <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">New HTML variant detected</h2>
    <p style="color: #6b7280; margin-bottom: 24px;">
        A new HTML structure was discovered for <strong>{{ $component->name }}</strong> on <strong>{{ $component->site->name }}</strong>.
    </p>

    <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 24px;">
        <tr>
            <td style="padding: 8px 0; color: #6b7280; width: 120px;">Component</td>
            <td style="padding: 8px 0; font-weight: 500;">{{ $component->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #6b7280;">Hash</td>
            <td style="padding: 8px 0; font-family: monospace;">{{ $hash }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #6b7280;">Page</td>
            <td style="padding: 8px 0; font-family: monospace; font-size: 12px;">{{ $pageUrl }}</td>
        </tr>
    </table>

    <a href="{{ url('/sites/' . $component->site_id . '/components/' . $component->id . '/analytics') }}"
       style="display: inline-block; background: #4f46e5; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 500;">
        View Analytics →
    </a>

    <p style="margin-top: 32px; font-size: 12px; color: #9ca3af;">
        You're receiving this because you own this component on ComponentWatch.
    </p>
</body>
</html>
