<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin | Tower Management Application</title>

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
      min-height:30vh;
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
      width: 100%;
      max-width: 1200px;
      padding: clamp(16px, 3vw, 32px);
      position: relative; /* penting untuk logout absolute */
    }

    .title{
      text-align:center;
      font-weight:500;
      letter-spacing:.6px;
      margin-bottom: 10px;
    }

    .brand{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:.6rem;
      margin-bottom: 8px;
      opacity: .95;
    }
    .brand-text{
      font-weight: 800;
      letter-spacing: .8px;
      font-size: clamp(18px, 2.2vw, 34px);
      text-transform: uppercase;
      text-align:center;
      line-height:1.2;
    }

    /* Carousel landscape max 480px (responsif HP & laptop) */
    .carousel-box{
      width: min(94vw, 480px);
      margin: 0 auto;
    }

    .carousel-img{
      width:90%;
      height:auto;
      aspect-ratio: 9 / 16;
      object-fit: cover;
      display:block;
      border-radius:.50rem;
      max-height: min(68vh, 640px);
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon{
      filter: invert(1) grayscale(1);
    }

.btn-wrap{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  justify-content:center;
  align-items:center;
}

    .btn-gradient{
      min-width: min(90vw, 260px);
      border: 0;
      border-radius: .75rem;
      background: linear-gradient(135deg, #6366f1, #38bdf8);
      box-shadow: 0 14px 30px rgba(56,189,248,.18);
      color:#fff;
    }
    .btn-gradient:hover{ filter: brightness(1.05); color:#fff; }

    /* Logout pojok kanan */
    .logout-wrap{
      position:absolute;
      top:50px;
      right:16px;
      z-index:10;
    }
    .btn-orange{
      background:#ff7a00;
      border-color:#ff7a00;
      color:#fff;
      font-weight:700;
    }
    .btn-orange:hover{
      background:#e96f00;
      border-color:#e96f00;
      color:#fff;
    }
  </style>
</head>
      <div class="brand">
        <div class="brand-text">SELAMAT DATANG</div>
      </div>
<body>
  <div class="page-wrap">
    <div class="panel glass">

      {{-- Logout pojok kanan --}}
      <div class="logout-wrap">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-orange btn-sm">Logout</button>
        </form>
      </div>


      <h1 class="title h2 text-white mb-4">TOWER APPLICATION MANAGAMENT</h1>

      <div class="carousel-box mb-3">
        <div id="welcomeCarousel"
             class="carousel slide"
             data-bs-ride="carousel"
             data-bs-interval="10000"
             data-bs-pause="false">

          <div class="carousel-inner">
            <div class="carousel-item active">
              <img class="carousel-img" src="{{ asset('images/slide1.jpg') }}" alt="Slide 1">
            </div>
            <div class="carousel-item">
              <img class="carousel-img" src="{{ asset('images/slide2.jpg') }}" alt="Slide 2">
            </div>
            <div class="carousel-item">
              <img class="carousel-img" src="{{ asset('images/slide1.jpg') }}" alt="Slide 3">
            </div>
          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>

        </div>
      </div>

      <div class="btn-wrap" center>
        <a href="{{ route('devices.index') }}" class="btn btn-gradient py-2">
          Lihat list Perangkat
        </a>

        <a href="{{ route('admin.installations.index') }}" class="btn btn-gradient py-2">
          Riwayat Pengajuan
        </a>

          <a href="{{ route('admin.cables.index') }}" class="btn btn-gradient py-2">
         List Jalur Kabel
          </a>

          <a href="{{ route('admin.users.index') }}" class="btn btn-gradient py-2">
            User Kontrol Akses
          </a>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const el = document.querySelector('#welcomeCarousel');
    if (el) new bootstrap.Carousel(el, { interval: 10000, pause: false, ride: 'carousel', wrap: true });
  </script>
</body>
</html>
