<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Form Izin Pemasangan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{ background:#d9d9d9; }
    .frame{ max-width:900px; margin:0 auto; padding:28px 16px; }
    .box{ background:#fff; border:1px solid #bdbdbd; }
    .label{ font-size:12px; font-weight:700; }
    .help{ font-size:11px; color:#555; }
    .upload-area{
      border:1px dashed #999; background:#f7f7f7;
      min-height:220px; display:flex; align-items:center; justify-content:center;
    }
    .preview-img{ max-width:100%; max-height:260px; object-fit:contain; }
  </style>
</head>
<body>
  <div class="frame">

    <div class="d-flex justify-content-between align-items-center mb-3">
<a class="btn btn-outline-dark btn-sm" href="{{ route('vendor.dashboard') }}">← Kembali</a>
      <h1 class="h5 mb-0 fw-bold">FORM IZIN PEMASANGAN</h1>
      <div style="width:90px;"></div>
    </div>

    <form class="box p-4" method="POST" action="{{ route('vendor.request.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="mb-3" style="max-width:420px;margin:0 auto;">
        <select class="form-select" name="tower_id" required>
          <option value="" disabled selected>Pilih Tower</option>
          @foreach($towers as $t)
            <option value="{{ $t->id }}">{{ $t->name }}</option>
          @endforeach
        </select>
        @error('tower_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <div class="label mb-1">VENDOR / DEPARTEMEN PEMASANG</div>
        <input class="form-control" name="vendor_department" required>
        @error('vendor_department') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <div class="label mb-1">NAMA PERANGKAT YANG DI PASANG</div>
        <input class="form-control" name="device_name" required>
        @error('device_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="row g-3 align-items-end mb-3">
        <div class="col-12 col-md-4">
          <div class="label mb-1">KETINGGIAN PERANGKAT YANG AKAN DI PASANG</div>
          <select class="form-select" name="stack_no" required>
            @foreach(range(1,7) as $no)
              <option value="{{ $no }}">STACK {{ $no }}</option>
            @endforeach
          </select>
          @error('stack_no') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 col-md-4">
          <div class="label mb-1">TINGGI DARI LANDASAN TOWER (meter)</div>
          <input class="form-control" name="height_from_ground_m" type="number" step="0.01" min="0" placeholder="contoh: 12.5">
          @error('height_from_ground_m') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mb-2">
        <div class="label mb-1">PHOTO PERANGKAT TAMPAK DEPAN</div>
      </div>

      <div class="upload-area mb-2" id="previewBox">
        <div class="text-muted small">Preview akan muncul di sini</div>
      </div>

      <div class="mb-4">
        <input class="form-control" type="file" name="device_photo" accept="image/*" id="device_photo">
        <div class="help mt-1">Upload photo (JPG/PNG). Maks 5MB.</div>
        @error('device_photo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="text-center">
        <button class="btn btn-primary px-5">SUBMIT</button>
      </div>
    </form>

  </div>

  <script>
    const input = document.getElementById('device_photo');
    const previewBox = document.getElementById('previewBox');

    input?.addEventListener('change', () => {
      const file = input.files?.[0];
      if (!file) return;

      const url = URL.createObjectURL(file);
      previewBox.innerHTML = `<img class="preview-img" src="${url}" alt="preview">`;
    });
  </script>
</body>
</html>
