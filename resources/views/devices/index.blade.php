<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Devices</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      background:#d9d9d9;
    }

    .wrap{
      min-height:100vh;
      padding:32px 16px;
    }

    .frame{
      max-width:1200px;
      margin:0 auto;
    }

    /* Header baru */
    .header-bar{
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:16px;
      margin-bottom:20px;
      padding:16px 18px;
      background:#ffffff;
      border:1px solid #dcdcdc;
      border-radius:16px;
      box-shadow:0 4px 14px rgba(0,0,0,.06);
    }

    .header-side{
      min-width:180px;
    }

    .header-center{
      flex:1;
      text-align:center;
    }

    .header-title{
      margin:0;
      font-size:20px;
      font-weight:700;
      color:#212529;
      line-height:1.2;
    }

    .header-subtitle{
      margin-top:4px;
      font-size:13px;
      color:#6c757d;
    }

    .nav-btn{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:9px 14px;
      border-radius:10px;
      font-weight:600;
      text-decoration:none;
      transition:all .2s ease;
    }

    .nav-btn:hover{
      transform:translateY(-1px);
    }

    .nav-btn-back{
      background:#f8f9fa;
      border:1px solid #ced4da;
      color:#212529;
    }

    .nav-btn-back:hover{
      background:#eef1f4;
      color:#212529;
    }

    .nav-btn-dashboard{
      background:#212529;
      border:1px solid #212529;
      color:#fff;
    }

    .nav-btn-dashboard:hover{
      background:#111418;
      color:#fff;
    }

    .stack-title{
      font-size:11px;
      font-weight:700;
      letter-spacing:.6px;
    }

    .stack-box{
      border:1px solid #6b6b6b;
      min-height:90px;
      padding:10px;
      background:rgba(255,255,255,.35);
      border-radius:10px;
    }

    .action-link{
      font-size:11px;
      text-decoration:none;
      font-weight:700;
      color:#000;
      border-bottom:1px solid #000;
      line-height:1.1;
    }

    .action-link:hover{
      opacity:.8;
    }

    .device-link{
      background:none;
      border:0;
      padding:0;
      text-align:left;
      color:#0d6efd;
      text-decoration:underline;
      cursor:pointer;
      font-size:0.95rem;
    }

    .device-link:hover{
      opacity:.85;
    }

    @media (max-width: 768px){
      .header-bar{
        flex-direction:column;
        align-items:stretch;
        text-align:center;
      }

      .header-side{
        min-width:auto;
        width:100%;
      }

      .header-center{
        order:-1;
      }

      .header-side .nav-btn{
        width:100%;
        justify-content:center;
      }
    }
  </style>
</head>
<body>
<div class="wrap">
  <div class="frame">

    <div class="header-bar">
      <div class="header-side">
        <a class="nav-btn nav-btn-back" href="{{ route('towers.index') }}">
          <span>←</span>
          <span>Master Tower</span>
        </a>
      </div>

      <div class="header-center">
        <h1 class="header-title">Devices per Stack</h1>
        <div class="header-subtitle">Monitoring & Overview</div>
      </div>

      <div class="header-side text-md-end">
        <a class="nav-btn nav-btn-dashboard" href="{{ route('admin.welcome') }}">
          <span>Admin Dashboard</span>
          <span>→</span>
        </a>
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

    @if($errors->any())
      <div class="alert alert-danger py-2">
        <div class="fw-semibold mb-1">Terjadi error:</div>
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
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
                    <li class="small d-flex align-items-center justify-content-between gap-2">
                      <button type="button"
                              class="device-link stack-item-link"
                              data-id="{{ $item->id }}">
                        {{ $item->device_name }}
                      </button>

                      <div class="d-flex align-items-center gap-2">
                        <form method="POST"
                              action="{{ route('stack-items.photo.upload', $item->id) }}"
                              enctype="multipart/form-data">
                          @csrf
                          <label class="btn btn-outline-primary btn-sm mb-0">
                            Upload Foto
                            <input type="file" name="photo" accept="image/*" hidden onchange="this.form.submit()">
                          </label>
                        </form>

                        <form method="POST" action="{{ route('stack-items.delete', $item->id) }}"
                              onsubmit="return confirm('Hapus perangkat ini?')">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-link text-danger p-0">hapus</button>
                        </form>
                      </div>
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

<!-- Modal Tambah Perangkat -->
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

<!-- Modal Detail Perangkat (Foto) -->
<div class="modal fade" id="stackItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="stackItemTitle">Detail Perangkat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="small text-muted mb-2" id="stackItemMeta">-</div>

        <div id="stackItemImgWrap" class="mb-3" style="display:none;">
          <img id="stackItemImg" src="" class="img-fluid rounded border" alt="Foto perangkat">
        </div>

        <div id="stackItemNoImg" class="alert alert-warning mb-0" style="display:none;">
          Foto perangkat belum tersedia. Silakan upload foto untuk perangkat ini.
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const editStackModal = document.getElementById('editStackModal');
  editStackModal?.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    document.getElementById('stack_no').value = btn.getAttribute('data-stack');
    document.getElementById('tower_id').value = btn.getAttribute('data-tower');
  });

  document.querySelectorAll('.stack-item-link').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;

      try {
        const res = await fetch("{{ url('/admin/devices/stack-items') }}/" + id);
        if (!res.ok) throw new Error('HTTP ' + res.status);

        const data = await res.json();

        document.getElementById('stackItemTitle').innerText = data.device_name || 'Detail Perangkat';
        document.getElementById('stackItemMeta').innerText =
          `Tower: ${data.tower ?? '-'} | Stack: ${data.stack_no ?? '-'}`;

        const imgWrap = document.getElementById('stackItemImgWrap');
        const img = document.getElementById('stackItemImg');
        const noImg = document.getElementById('stackItemNoImg');

        if (data.photo_url) {
          img.src = data.photo_url;
          imgWrap.style.display = 'block';
          noImg.style.display = 'none';
        } else {
          imgWrap.style.display = 'none';
          noImg.style.display = 'block';
        }

        new bootstrap.Modal(document.getElementById('stackItemModal')).show();
      } catch (e) {
        console.error(e);
        alert('Gagal mengambil detail perangkat.');
      }
    });
  });
</script>
</body>
</html>