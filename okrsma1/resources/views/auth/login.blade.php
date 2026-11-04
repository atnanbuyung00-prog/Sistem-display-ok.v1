<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login - RS Mutiara Aini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #1b7a5a;
            --bs-primary-rgb: 27, 122, 90;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #eaf6f1, #ffffff);
            display: grid;
            place-items: center
        }

        .login-card {
            max-width: 420px;
            width: 100%;
            border: 0;
            box-shadow: 0 20px 60px rgba(27, 122, 90, .12);
            border-radius: 20px
        }

        .text-primary {
            color: #1b7a5a !important
        }

        .btn-primary {
            background-color: #1b7a5a !important;
            border-color: #1b7a5a !important
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #145a43 !important;
            border-color: #145a43 !important
        }

        .form-control:focus {
            border-color: #1b7a5a;
            box-shadow: 0 0 0 .25rem rgba(27, 122, 90, .2)
        }
    </style>
</head>

<body>
    <div class="card login-card p-4">
        <div class="text-center"><img src="{{ asset('images/logo-rsma.png') }}" style="width:250px">
            <h4 class="mt-3 fw-bold text-primary">Login Administrator</h4>
            <p class="text-muted">Sistem Jadwal Operasi</p>
        </div>@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif<form method="POST" action="{{ route('login.submit') }}">@csrf<div class="mb-3"><label class="form-label">Username</label><input class="form-control form-control-lg" name="username" value="{{ old('username') }}" autofocus></div>
            <div class="mb-3"><label class="form-label">Password</label>
                <div class="input-group"><input id="loginPassword" type="password" class="form-control form-control-lg" name="password" autocomplete="current-password"><button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Tampilkan password">👁</button></div>
            </div>
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" value="1" checked>
                    <label class="form-check-label text-muted small" for="rememberMe">Ingat saya (tetap login)</label>
                </div>
            </div>
            <button class="btn btn-primary btn-lg w-100">Masuk</button>
        </form>
    </div>
    <script>
        document.getElementById("togglePassword").addEventListener("click", function() {
            const p = document.getElementById("loginPassword");
            p.type = p.type === "password" ? "text" : "password";
            this.setAttribute("aria-label", p.type === "password" ? "Tampilkan password" : "Sembunyikan password");
        });
    </script>
</body>

</html>