<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SendaSnap') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --background: 0 0% 100%;
            --foreground: 222.2 84% 4.9%;
            --card: 0 0% 100%;
            --card-foreground: 222.2 84% 4.9%;
            --popover: 0 0% 100%;
            --popover-foreground: 222.2 84% 4.9%;
            --primary: 221.2 83.2% 53.3%;
            --primary-foreground: 210 40% 98%;
            --secondary: 210 40% 96%;
            --secondary-foreground: 222.2 84% 4.9%;
            --muted: 210 40% 96%;
            --muted-foreground: 215.4 16.3% 46.9%;
            --accent: 210 40% 96%;
            --accent-foreground: 222.2 84% 4.9%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 210 40% 98%;
            --border: 214.3 31.8% 91.4%;
            --input: 214.3 31.8% 91.4%;
            --ring: 221.2 83.2% 53.3%;
            --radius: 0.5rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1000px;
            width: 100%;
            background: hsl(var(--card));
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            min-height: 600px;
        }

        .login-left {
            background: linear-gradient(135deg, hsl(var(--primary)) 0%, hsl(var(--primary) / 0.8) 100%);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: hsl(var(--primary-foreground));
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        .login-left-content {
            position: relative;
            z-index: 1;
        }

        .login-left h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
        }

        .login-left p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .features {
            list-style: none;
            text-align: left;
        }

        .features li {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 14px;
            opacity: 0.9;
        }

        .features i {
            width: 20px;
            text-align: center;
        }

        .login-right {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: hsl(var(--foreground));
            margin-bottom: 8px;
        }

        .login-header p {
            color: hsl(var(--muted-foreground));
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: hsl(var(--foreground));
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid hsl(var(--border));
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: hsl(var(--background));
            color: hsl(var(--foreground));
        }

        .form-group input:focus {
            outline: none;
            border-color: hsl(var(--ring));
            box-shadow: 0 0 0 3px hsl(var(--ring) / 0.1);
        }

        .form-group input::placeholder {
            color: hsl(var(--muted-foreground));
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: hsl(var(--muted-foreground));
        }

        .remember-me input {
            width: auto;
            margin: 0;
        }

        .forgot-password {
            color: hsl(var(--primary));
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 12px 16px;
            background: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn:hover {
            background: hsl(var(--primary) / 0.9);
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-radius: 50%;
            border-top-color: currentColor;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error-message i {
            font-size: 16px;
        }

        .demo-credentials {
            background: hsl(var(--muted));
            padding: 16px;
            border-radius: 8px;
            margin-top: 24px;
            font-size: 12px;
            color: hsl(var(--muted-foreground));
        }

        .demo-credentials h4 {
            font-weight: 600;
            margin-bottom: 8px;
            color: hsl(var(--foreground));
        }

        .demo-credentials ul {
            list-style: none;
            margin: 0;
        }

        .demo-credentials li {
            margin-bottom: 4px;
        }

        .demo-credentials strong {
            color: hsl(var(--foreground));
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 400px;
            }

            .login-left {
                padding: 40px 20px;
                min-height: 300px;
            }

            .login-right {
                padding: 40px 20px;
            }

            .login-left h1 {
                font-size: 24px;
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="login-container fade-in">
        <div class="login-left">
            <div class="login-left-content">
                <h1>
                    <span class="material-symbols-rounded">directions_car</span>
                    SendaSnap
                </h1>
                <p>Vehicle Management System</p>
                <ul class="features">
                    <li>
                        <span class="material-symbols-rounded">check</span>
                        Complete vehicle tracking
                    </li>
                    <li>
                        <span class="material-symbols-rounded">check</span>
                        Task management
                    </li>
                    <li>
                        <span class="material-symbols-rounded">check</span>
                        Team collaboration
                    </li>
                    <li>
                        <span class="material-symbols-rounded">check</span>
                        Real-time updates
                    </li>
                </ul>
            </div>
        </div>

        <div class="login-right">
            <div class="login-header">
                <h2>Welcome back</h2>
                <p>Sign in to your account to continue</p>
            </div>

            @if($errors->any())
                <div class="error-message">
                    <span class="material-symbols-rounded">error</span>
                    {{ $errors->first() }}
                </div>
            @endif

            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="Enter your email" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="form-actions">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        Remember me
                    </label>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="btn" id="loginBtn">
                    <span id="btnText">Sign in</span>
                    <div class="loading" id="btnLoading" style="display: none;"></div>
                </button>
            </form>

            <div class="demo-credentials">
                <h4>Demo Credentials</h4>
                <ul>
                    <li><strong>Admin:</strong> sulaiman@sendasnap.com / password</li>
                    <li><strong>Manager:</strong> acj.shiroyama@gmail.com / password</li>
                    <li><strong>Employee:</strong> acj.document@gmail.com / password</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // REST API login (Sanctum SPA compatible; falls back to token if returned)
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');

            const getCookie = (name) => {
                const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
                return match ? decodeURIComponent(match[1]) : null;
            };

            btn.disabled = true; btnText.style.display = 'none'; btnLoading.style.display = 'inline-block';
            try {
                // Token-based API login does NOT require XSRF token
                const res = await fetch('/api/v1/auth/login', {
                    method: 'POST',
                    // No cookies needed for token login
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ email, password })
                });
                const ct = res.headers.get('content-type') || '';
                const json = ct.includes('application/json') ? await res.json().catch(() => ({})) : {};
                if (!res.ok) {
                    // Try to parse plain text error if any
                    const txt = !ct ? (await res.text().catch(() => '')) : '';
                    throw new Error(json?.message || txt || 'Invalid credentials');
                }

                const payload = json?.data ?? json ?? {};
                const token = payload?.token ?? payload?.access_token ?? payload?.accessToken ?? null;

                if (!token) {
                    throw new Error('Login response missing access token.');
                }

                try { localStorage.setItem('api_token', token); } catch (_) {}

                // Bridge to create web session so protected pages work
                await fetch(@json(route('auth.token-login')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'include',
                    body: JSON.stringify({ token })
                });

                const dashUrl = @json(route('dashboard'));
                try { window.location.replace(dashUrl); } catch (_) { window.location.href = dashUrl; }
                setTimeout(() => { window.location.href = dashUrl; }, 100);
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Login failed', text: err.message || 'Please try again' });
                btn.disabled = false; btnText.style.display = 'inline'; btnLoading.style.display = 'none';
            }
        });

        // Auto-fill demo credentials on click
        document.querySelectorAll('.demo-credentials li').forEach(item => {
            item.style.cursor = 'pointer';
            item.addEventListener('click', function () {
                const text = this.textContent;
                const [email, password] = text.split(' / ');
                const cleanEmail = email.split(': ')[1];

                document.getElementById('email').value = cleanEmail;
                document.getElementById('password').value = password;

                // Show toast
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Demo credentials filled',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        });

        // Add hover effects to demo credentials
        document.querySelectorAll('.demo-credentials li').forEach(item => {
            item.addEventListener('mouseenter', function () {
                this.style.backgroundColor = 'rgba(0,0,0,0.05)';
                this.style.borderRadius = '4px';
                this.style.padding = '4px 8px';
                this.style.margin = '0 -8px 4px -8px';
            });

            item.addEventListener('mouseleave', function () {
                this.style.backgroundColor = 'transparent';
                this.style.padding = '0';
                this.style.margin = '0 0 4px 0';
            });
        });

        // Add some interactive animations
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function () {
                this.parentElement.style.transform = 'translateY(-2px)';
                this.parentElement.style.transition = 'transform 0.2s ease';
            });

            input.addEventListener('blur', function () {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Add floating animation to the left side
        const leftSide = document.querySelector('.login-left');
        let mouseX = 0;
        let mouseY = 0;

        document.addEventListener('mousemove', function (e) {
            mouseX = (e.clientX / window.innerWidth) * 100;
            mouseY = (e.clientY / window.innerHeight) * 100;

            leftSide.style.background = `linear-gradient(135deg,
                hsl(var(--primary)) 0%,
                hsl(var(--primary) / 0.8) 100%),
                radial-gradient(circle at ${mouseX}% ${mouseY}%,
                rgba(255,255,255,0.1) 0%,
                transparent 50%)`;
        });
    </script>
</body>

</html>
