<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User | Tower Management Application</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{ --bg1:#0b1220; --bg2:#0f1b33; }
    body{
      min-height:100vh;
      background:
        radial-gradient(1200px 600px at 15% 10%, rgba(99,102,241,.20), transparent 60%),
        radial-gradient(900px 500px at 85% 25%, rgba(56,189,248,.18), transparent 55%),
        linear-gradient(180deg, var(--bg1), var(--bg2));
      color:#fff;
      margin:0;
    }

    .page-wrap{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: clamp(16px, 3vw, 48px);
    }

    .glass{
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.14);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 1.25rem;
      box-shadow: 0 20px 60px rgba(0,0,0,.35);
    }

    .panel{
      position: relative;                 /* kunci */
      width: min(100%, 1100px);
      padding: clamp(16px, 3vw, 32px);
    }

    .logout-wrap{
      position: absolute;
      top: 16px;
      right: 16px;
      z-index: 10;
    }

    .btn-orange{
      background:#ff7a00;
      border-color:#ff7a00;
      color:#fff;
    }
    .btn-orange:hover{
      background:#e96f00;
      border-color:#e96f00;
      color:#fff;
    }

    .title{
      text-align:center;
      font-weight:700;
      letter-spacing:.6px;
      margin-bottom: clamp(12px, 2vw, 20px);
    }

    .carousel-box{ width: min(92vw, 520px); margin: 0 auto; }

    .carousel-img{
      width:90%;
      height:auto;
      aspect-ratio: 9 / 16;
      object-fit: cover;
      display:block;
      border-radius:.50rem;
      max-height: min(68vh, 640px);
    }

    .btn-gradient{
      border:0; border-radius:.5rem;
      background: linear-gradient(135deg, #6366f1, #38bdf8);
      box-shadow: 0 14px 30px rgba(56,189,248,.18);
      color:#fff;
    }
  </style>
</head>

<body>
  <div class="page-wrap">
    <div class="panel glass">

      <!-- Logout pojok kanan -->
      <div class="logout-wrap">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-orange btn-sm">Logout</button>
        </form>
      </div>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <h1 class="title h4">SELAMAT DATANG</h1>

      <div class="carousel-box">
        <div id="welcomeCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000" data-bs-pause="false">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img class="carousel-img" src="{{ asset('images/slide1.jpg') }}" alt="Slide 1">
            </div>
            <div class="carousel-item">
              <img class="carousel-img" src="{{ asset('images/slide2.jpg') }}" alt="Slide 2">
            </div>
          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
            <span class="visually-hidden">Prev</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>

      <div class="d-flex justify-content-center gap-2 flex-wrap mt-4">
        <a href="{{ route('vendor.request.create') }}" class="btn btn-gradient py-2 px-4">
          AJUKAN PEMASANGAN
        </a>
        <a href="{{ route('vendor.requests.history') }}" class="btn btn-outline-light py-2 px-4">
          Riwayat Pemasangan
        </a>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
