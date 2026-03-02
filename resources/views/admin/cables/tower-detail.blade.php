<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Tower - {{ $tower->name }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .port-row {
      display:flex;
      align-items:center;
      gap:12px;
      padding:10px 0;
      border-bottom:1px solid #f0f0f0;
    }
    .dot { width:28px; height:10px; border-radius:3px; display:inline-block; }
    .dot.ready { background:#0d6efd; }   /* STANDBY */
    .dot.used { background:#198754; }    /* ACTIVE */
    .dot.broken { background:#dc3545; }  /* RUSAK */

    .port-label { width:80px; font-weight:600; }
    .status-badge { min-width:110px; }
    .note-input { max-width:520px; }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h5 mb-1 fw-bold">Detail {{ $tower->name }}</h1>
      <div class="text-muted small">Menampilkan OTB & port (status + catatan link per port)</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.cables.index') }}">Kembali</a>

      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generateModal">
        Generate OTB & Port
      </button>

      <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.welcome') }}">Admin Dashboard</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Terjadi error:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Modal Generate -->
  <div class="modal fade" id="generateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" method="POST" action="{{ route('admin.cables.tower.generate', $tower->id) }}">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Generate OTB & Port</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Jumlah OTB</label>
            <input type="number" name="otb_count" class="form-control" min="1" max="20" value="4" required>
            <div class="form-text">Contoh: 4 OTB untuk 1 tower.</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Port per OTB</label>
            <input type="number" name="ports_per_otb" class="form-control" min="1" max="96" value="12" required>
            <div class="form-text">Default 12 port/OTB.</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Mode</label>
            <select name="mode" class="form-select">
              <option value="skip" selected>Skip (tidak hapus data lama, hanya tambah yang kurang)</option>
              <option value="reset">Reset (hapus semua OTB & Port lalu generate ulang)</option>
            </select>
          </div>

          <div class="alert alert-warning mb-0">
            <strong>Catatan:</strong> Mode <em>Reset</em> akan menghapus status & catatan port yang sudah diubah sebelumnya.
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Generate</button>
        </div>
      </form>
    </div>
  </div>

  @foreach($otbs as $otb)
    <div class="card mb-3">
      <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
        <span>{{ $otb->name }}</span>
        <span class="small text-muted">Total port: {{ $otb->total_ports ?? $otb->ports->count() }}</span>
      </div>

      <div class="card-body">
        @php $ports = $otb->ports->sortBy('port_no')->values(); @endphp

        @foreach($ports as $p)
          <form class="port-row" method="POST" action="{{ route('admin.cables.port.update', $p->id) }}">
            @csrf
            @method('PATCH')

            <div class="port-label">PORT {{ $p->port_no }}</div>

            {{-- indikator warna status (tetap ada) --}}
            <span class="dot {{ $p->status }}"></span>

            {{-- dropdown status --}}
            <select name="status" class="form-select form-select-sm status-badge" title="Status Port">
              <option value="used"   {{ $p->status === 'used' ? 'selected' : '' }}>ACTIVE</option>
              <option value="ready"  {{ $p->status === 'ready' ? 'selected' : '' }}>STANDBY</option>
              <option value="broken" {{ $p->status === 'broken' ? 'selected' : '' }}>RUSAK</option>
            </select>

            {{-- textbox catatan/link --}}
            <input type="text"
                   name="note"
                   value="{{ old('note', $p->note) }}"
                   class="form-control form-control-sm note-input"
                   placeholder="Contoh: Link arah tower LBA 082, jarak 5,675 km, redaman -14">

            <div class="ms-auto d-flex gap-2 align-items-center">
              @if($p->photo_path)
                <a class="btn btn-outline-primary btn-sm" target="_blank"
                   href="{{ asset('storage/'.$p->photo_path) }}">Lihat gambar</a>
              @endif

              <button class="btn btn-success btn-sm">Simpan</button>
            </div>
          </form>
        @endforeach

        <div class="mt-3 small">
          <span class="me-3"><span class="dot used"></span> ACTIVE</span>
          <span class="me-3"><span class="dot ready"></span> STANDBY</span>
          <span class="me-3"><span class="dot broken"></span> RUSAK</span>
        </div>
      </div>
    </div>
  @endforeach

</div>
</body>
</html>