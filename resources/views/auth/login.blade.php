<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>

  <!-- Bootstrap 5.3.x -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --bg1:#0b1220;
      --bg2:#0f1b33;
    }
    body{
      min-height:100vh;
      background:
        radial-gradient(1200px 600px at 15% 10%, rgba(99,102,241,.20), transparent 60%),
        radial-gradient(900px 500px at 85% 25%, rgba(56,189,248,.18), transparent 55%),
        linear-gradient(180deg, var(--bg1), var(--bg2));
    }
    .card-glass{
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.14);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 1.25rem;
      box-shadow: 0 20px 60px rgba(0,0,0,.35);
    }
    .form-control, .form-check-input{
      background: rgba(255,255,255,.08);
      border-color: rgba(255,255,255,.18);
      color: #fff;
    }
    .form-control:focus{
      background: rgba(255,255,255,.10);
      color:#fff;
      border-color: rgba(99,102,241,.65);
      box-shadow: 0 0 0 .25rem rgba(99,102,241,.20);
    }
    .form-control::placeholder{ color: rgba(255,255,255,.55); }
    .text-muted-2{ color: rgba(255,255,255,.65) !important; }
    .brand-dot{
      width:.75rem;height:.75rem;border-radius:999px;
      background: linear-gradient(135deg, #6366f1, #38bdf8);
      box-shadow: 0 0 24px rgba(99,102,241,.55);
      display:inline-block;
      margin-right:.5rem;
      transform: translateY(-1px);
    }
    .btn-primary{
      background: linear-gradient(135deg, #6366f1, #38bdf8);
      border: none;
      box-shadow: 0 14px 30px rgba(56,189,248,.20);
    }
    .btn-primary:hover{ filter: brightness(1.05); }
    a.link-light-2{ color: rgba(255,255,255,.78); }
    a.link-light-2:hover{ color:#fff; }
  </style>
</head>

<body class="text-white">
  <main class="container">
    <div class="row min-vh-100 align-items-center justify-content-center py-5">
      <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
        <div class="card card-glass">
          <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-center mb-3">
            </div>

            <h1 class="h4 fw-bold mb-1" style="color:#fff;">Welcome back</h1>
            <p class="text-muted-2 mb-4">Silakan login untuk melanjutkan.</p>

                <form class="needs-validation" action="{{ route('login.post') }}" method="POST" novalidate> @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input
                    type="text"
                    class="form-control form-control-lg"
                    id="username"
                    name="username"
                    placeholder="Username"
                    required
                    />
                    <div class="invalid-feedback">Masukkan Username yang valid.</div>
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group input-group-lg">
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        minlength="6"
                    />
                    <button class="btn btn-outline-light" type="button" id="togglePassword">Show</button>
                    <div class="invalid-feedback">Password minimal 6 karakter.</div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4 mt-3">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label text-muted-2" for="remember">Remember me</label>
                    </div>
                    <a href="/forgot-password" class="link-light-2 text-decoration-none small">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                    Sign in
                </button>
                </form>

          </div>
        </div>

        <p class="text-center text-muted-2 small mt-4 mb-0">
          © <span id="year"></span> Information & Technology
        </p>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Year
    document.getElementById('year').textContent = new Date().getFullYear();

    // Bootstrap validation
    (() => {
      const forms = document.querySelectorAll('.needs-validation');
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    })();

    // Toggle password
    const btn = document.getElementById('togglePassword');
    const pw = document.getElementById('password');
    btn.addEventListener('click', () => {
      const isPw = pw.getAttribute('type') === 'password';
      pw.setAttribute('type', isPw ? 'text' : 'password');
      btn.textContent = isPw ? 'Hide' : 'Show';
    });
  </script>
</body>
</html>
