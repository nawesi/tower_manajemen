<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gambar Tower</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background:#d9d9d9; }
    .frame { max-width:1100px; margin:0 auto; padding:24px 16px; }
    .box { background:#fff; border:1px solid #b5b5b5; border-radius:8px; }
    .img-box {
      aspect-ratio: 4 / 3;
      background:#f1f1f1;
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      border-radius:6px;
      border:1px solid #e6e6e6;
    }
    .img-box img { width:100%; height:100%; object-fit:contain; display:block; }
  </style>
</head>

<body>
<div class="frame">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Upload gagal:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <a class="btn btn-outline-dark btn-sm"
       href="{{ route('devices.index', ['tower_id' => $tower->id]) }}">
      ← Kembali
    </a>

    <div class="fw-semibold text-center">
      {{ $tower->name }}
      <span class="text-muted">&gt; STACK {{ (int) $stack }}</span>
    </div>

    <div style="width:72px;"></div>
  </div>

  <div class="row g-4">
    @foreach([1,2,3,4] as $side)
      @php
          $img = $images->firstWhere('side', $side);
      @endphp

      <div class="col-12 col-md-6">
        <div class="text-center fw-semibold mb-2">Tampak sisi {{ $side }}</div>

        <div class="box p-2">
          <div class="img-box mb-2">
             @if($img)
    <img src="{{ asset('storage/'.$img->image_path) }}" alt="Side {{ $side }}">
  @else
    <div class="text-muted small">Belum ada gambar untuk sisi {{ $side }}</div>
  @endif
          </div>

          <form method="POST"
                action="{{ route('towers.images.upload', ['tower' => $tower->id]) }}?stack={{ $stack }}"
                enctype="multipart/form-data"
                class="d-flex gap-2 align-items-center">
            @csrf
            <input type="hidden" name="stack" value="{{ $stack }}">
            <input type="hidden" name="side" value="{{ $side }}">

            <input type="file" name="image" class="form-control form-control-sm" required>
            <button class="btn btn-primary btn-sm">Upload</button>
          </form>

          <div class="small text-muted mt-1">
            Upload akan menggantikan gambar sisi {{ $side }} untuk STACK {{ $stack }}.
          </div>
        </div>
      </div>
    @endforeach
  </div>

</div>
</body>
</html>
