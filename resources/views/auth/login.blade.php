<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SPMB (Sistem Penerimaan Murid Baru)</title>
    @include('partials.favicon')
    @include('partials.theme-vars')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            font-size: 28px;
            color: #333;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .login-logo {
            display: block;
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin: 0 auto 18px;
            border-radius: 12px;
        }
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        .form-group input {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: var(--theme-primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--theme-primary) 14%, transparent);
        }
        .btn-login {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 20px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px color-mix(in srgb, var(--theme-primary) 40%, transparent);
        }
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }
        .alert-error {
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            @php
                $schoolName = $settings['school_name'] ?? 'SPMB (Sistem Penerimaan Murid Baru)';
                $logo = !empty($settings['school_logo']) ? asset('storage/' . $settings['school_logo']) : null;
            @endphp
            @if($logo)
                <img src="{{ $logo }}" alt="Logo {{ $schoolName }}" class="login-logo">
            @endif
            <h1>{{ $schoolName }}</h1>
            <p>Sistem Penerimaan Murid Baru</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="admin@spmb.local"
                    required
                >
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Masukkan password"
                    required
                >
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-login">Login</button>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #999;">
            <p><strong>TEST LOGIN</strong></p>
            <p>Admin: admin@spmb.local / admin123</p>
            <p>Panitia: panitia@spmb.local / panitia123</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

