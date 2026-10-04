<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - MediCare Appointment System</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --border-color: #334155;
            --primary: #0d9488;
            --primary-hover: #0f766e;
            --accent: #38bdf8;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --radius: 16px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background-image: radial-gradient(circle at 10% 20%, rgba(13, 148, 136, 0.15) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(56, 189, 248, 0.12) 0%, transparent 40%);
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 2.5rem;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.6);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 54px;
            height: 54px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #fff;
            margin-bottom: 1rem;
            box-shadow: 0 0 25px rgba(13, 148, 136, 0.4);
        }

        .brand-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .brand-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 6px;
            line-height: 1.4;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.25);
        }

        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(13, 148, 136, 0.35);
        }

        .btn-submit:hover { opacity: 0.95; transform: translateY(-1px); }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="brand-header">
            <div class="brand-icon">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1>Reset Password</h1>
            <p>Enter your registered account email address to reset your password.</p>
        </div>

        @if(session('success'))
            <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; font-size: 1.25rem; line-height: 1; cursor: pointer; padding: 0 0 0 10px;" title="Dismiss">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error" style="display: flex; align-items: center; justify-content: space-between;">
                <div><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
                <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; font-size: 1.25rem; line-height: 1; cursor: pointer; padding: 0 0 0 10px;" title="Dismiss">&times;</button>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-paper-plane"></i> Verify & Continue
            </button>
        </form>

        <div class="auth-footer">
            Remembered your password? <a href="{{ route('login') }}">Back to Sign In</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-error, div[style*="rgba(16, 185, 129"]');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 600);
                });
            }, 4000);
        });
    </script>
</body>
</html>
