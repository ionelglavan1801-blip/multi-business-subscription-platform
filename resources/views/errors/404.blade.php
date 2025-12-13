<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found - MultiApp</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #4f46e5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            text-align: center;
            color: white;
            max-width: 500px;
        }
        .error-code {
            font-size: 150px;
            font-weight: 800;
            line-height: 1;
            opacity: 0.9;
            text-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .error-title {
            font-size: 28px;
            font-weight: 700;
            margin-top: 20px;
        }
        .error-message {
            font-size: 18px;
            opacity: 0.9;
            margin-top: 12px;
            line-height: 1.6;
        }
        .actions {
            margin-top: 40px;
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: white;
            color: #4f46e5;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .btn-secondary {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.25);
        }
        .icon {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }
        .illustration {
            margin-bottom: 20px;
        }
        .illustration svg {
            width: 120px;
            height: 120px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="illustration">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="error-code">404</div>
        <h1 class="error-title">Page not found</h1>
        <p class="error-message">
            Oops! The page you're looking for doesn't exist or has been moved.
        </p>
        <div class="actions">
            <a href="/" class="btn btn-primary">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Go Home
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Go Back
            </a>
        </div>
    </div>
</body>
</html>
