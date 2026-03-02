<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Devices</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{ background:#d9d9d9; }
    .wrap{ min-height:100vh; padding:32px 16px; }
    .frame{ max-width:1200px; margin:0 auto; }

    .stack-title{ font-size:11px; font-weight:700; letter-spacing:.6px; }
    .stack-box{
      border:1px solid #6b6b6b;
      min-height:90px;
      padding:10px;
      background:rgba(255,255,255,.35);
    }

    .action-link{
      font-size:11px;
      text-decoration:none;
      font-weight:700;
      color:#000;
      border-bottom:1px solid #000;
      line-height:1.1;
    }
    .action-link:hover{ opacity:.8; }

    .header-bar{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
      margin-bottom:14px;
    }
  </style>
</head>
<body>
<div class="wrap">
  <div class="frame">

    <div class="header-bar">
      <a class="btn btn-outline-dark btn-sm" href="{{ route('towers.index') }}">← Master Tower</a>
      <div  class="text-end small text-muted">Devices per Stack
          <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.welcome') }}">Admin Dashboard</a>
      </div>
    </div>

    <form method="GET" action="{{ route('devices.index') }}" class="mb-3" style="max-width:520px;margin:0 auto;">
      <select class="form-select" name="tower_id" onchange="this.form.submit()">
        @forelse($towers as $t)
          <option value="{{ $t->id }}" {{ optional($selectedTower)->id === $t->id ? 'selected' : '' }}>
            {{ $t->name }}
          </option>
        @empty
          <option value="">Belum ada tower</option>
        @endforelse
      </select>
    </form>

    @if(session('success'))
      <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    @if(!$selectedTower)
      <div class="alert alert-warning">Belum ada tower.</div>
    @else
      <div class="row g-4">
        @foreach(range(1,7) as $stackNo)
          <div class="col-12 col-lg-6">

            <div class="d-flex justify-content-between align-items-start mb-1">
              <div class="stack-title">STACK {{ $stackNo }}</div>

              <div class="d-flex gap-3">
                <a class="action-link"
                   href="#"
                   data-bs-toggle="modal"
                   data-bs-target="#editStackModal"
                   data-stack="{{ $stackNo }}"
                   data-tower="{{ $selectedTower->id }}">
                  EDIT
                </a>

                {{-- FIX: pakai $selectedTower, bukan $tower --}}
                <a class="action-link"
                   href="{{ route('towers.images', ['tower' => $selectedTower->id, 'stack' => $stackNo]) }}">
                  LIHAT GAMBAR
                </a>
              </div>
            </div>

            <div class="stack-box">
              @php
                $items = $stackMap[$stackNo] ?? collect();
              @endphp

              @if($items->isEmpty())
                <div class="text-muted small">Belum ada perangkat.</div>
              @else
                <ul class="mb-0 ps-3">
                  @foreach($items as $item)
                    <li class="small d-flex align-items-center justify-content-between">
                      <span>{{ $item->device_name }}</span>
                      <form method="POST" action="{{ route('stack-items.delete', $item->id) }}"
                            onsubmit="return confirm('Hapus perangkat ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-link text-danger p-0">hapus</button>
                      </form>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>

          </div>
        @endforeach
      </div>
    @endif

  </div>
</div>

<div class="modal fade" id="editStackModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('stack-items.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Tambah Perangkat</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="tower_id" id="tower_id">
          <input type="hidden" name="stack_no" id="stack_no">

          <div class="mb-2">
            <label class="form-label">Nama Perangkat</label>
            <input type="text" name="device_name" class="form-control" required placeholder="Contoh: Antena PB">
          </div>

          <div class="small text-muted">
            Tambah perangkat baru. Untuk menghapus perangkat gunakan tombol “hapus” pada list.
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Tutup</button>
          <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const modal = document.getElementById('editStackModal');
  modal?.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    document.getElementById('stack_no').value = btn.getAttribute('data-stack');
    document.getElementById('tower_id').value = btn.getAttribute('data-tower');
  });
</script>
</body>
</html>
