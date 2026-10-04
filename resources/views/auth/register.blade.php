<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - MediCare</title>
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
            max-width: 500px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 2.5rem;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.6);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 1.5rem;
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
            margin-bottom: 0.75rem;
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
            margin-top: 4px;
        }

        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 1.5rem;
            background: rgba(15, 23, 42, 0.6);
            padding: 5px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .role-option {
            flex: 1;
            text-align: center;
        }

        .role-option input {
            display: none;
        }

        .role-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.7rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.2s ease;
        }

        .role-option input:checked + label {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 2px 10px rgba(13, 148, 136, 0.4);
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
        }

        .form-control:focus, .form-select:focus {
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
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1>Create Account</h1>
            <p>Register as a Patient or apply as a Specialist Doctor</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Role Selector: Patient vs Doctor -->
            <div class="role-selector">
                <div class="role-option">
                    <input type="radio" name="role" id="rolePatient" value="patient" {{ old('role', 'patient') === 'patient' ? 'checked' : '' }}>
                    <label for="rolePatient"><i class="fa-solid fa-user-check"></i> Patient</label>
                </div>
                <div class="role-option">
                    <input type="radio" name="role" id="roleDoctor" value="doctor" {{ old('role') === 'doctor' ? 'checked' : '' }}>
                    <label for="roleDoctor"><i class="fa-solid fa-user-doctor"></i> Doctor</label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" placeholder="Email Address" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" placeholder="Phone Number" value="{{ old('phone') }}">
            </div>

            <!-- Doctor Specific Field -->
            <div class="form-group" id="specializationGroup" style="display: none;">
                <label class="form-label">Specialization / Medical Field *</label>
                <input type="text" name="specialization" id="specializationInput" class="form-control" placeholder="Specialization / Medical Field" value="{{ old('specialization') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password *</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="btn-submit">
                Register Account
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rolePatient = document.getElementById('rolePatient');
            const roleDoctor = document.getElementById('roleDoctor');
            const specGroup = document.getElementById('specializationGroup');
            const specInput = document.getElementById('specializationInput');

            function toggleRoleFields() {
                if (roleDoctor.checked) {
                    specGroup.style.display = 'block';
                    specInput.required = true;
                } else {
                    specGroup.style.display = 'none';
                    specInput.required = false;
                }
            }

            rolePatient.addEventListener('change', toggleRoleFields);
            roleDoctor.addEventListener('change', toggleRoleFields);
            toggleRoleFields();
        });
    </script>
</body>
</html>
