<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('translate.Admin Access') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #1a1a2e; display: flex; align-items: center; justify-content: center;
            min-height: 100vh; color: #fff;
        }
        .card {
            background: #16213e; padding: 40px; border-radius: 12px; width: 360px;
            box-shadow: 0 8px 32px rgba(0,0,0,.3);
        }
        h1 { font-size: 1.25rem; margin-bottom: 8px; }
        p { color: #a0aec0; font-size: .875rem; margin-bottom: 24px; }
        input {
            width: 100%; padding: 12px 16px; border: 1px solid #2d3748; border-radius: 8px;
            background: #0f3460; color: #fff; font-size: 1rem; outline: none;
        }
        input:focus { border-color: #4a9eff; }
        button {
            width: 100%; padding: 12px; background: #4a9eff; color: #fff; border: none;
            border-radius: 8px; font-size: 1rem; cursor: pointer; margin-top: 16px;
        }
        button:hover { background: #3a8eef; }
        .error { color: #fc8181; font-size: .8rem; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ __('translate.Admin Access') }}</h1>
        <p>{{ __('translate.Enter the secret key to continue.') }}</p>
        @if ($error)
            <div class="error">{{ $error }}</div>
        @endif
        <form method="GET">
            <input type="password" name="admin_secret" placeholder="{{ __('translate.Secret key') }}" autofocus>
            <button type="submit">{{ __('translate.Continue') }}</button>
        </form>
    </div>
</body>
</html>
