<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Riwayat Pengajuan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0 fw-bold">Riwayat Pengajuan Pemasangan</h1>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('vendor.dashboard') }}">Dashboard</a>
      <a class="h10 mb-0 fw-bold" href="{{ route('vendor.request.create') }}">Ajukan Baru</a>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0 align-middle">
        <thead>
        <tr>
          <th>Tanggal</th>
          <th>Tower</th>
          <th>Perangkat</th>
          <th>Stack</th>
          <th>Status</th>
          <th>Catatan Admin</th>
          <th>Foto</th>
        </tr>
        </thead>
        <tbody>
        @forelse($requests as $r)
          <tr>
            <td class="text-nowrap">{{ $r->created_at->format('d M Y H:i') }}</td>
            <td>{{ $r->tower?->name }}</td>
            <td>{{ $r->device_name }}</td>
            <td>STACK {{ $r->stack_no }}</td>
            <td class="text-nowrap">
              @php
                $badge = match($r->status){
                  'approved' => 'success',
                  'rejected' => 'danger',
                  default => 'warning'
                };
              @endphp
              <span class="badge bg-{{ $badge }}">{{ strtoupper($r->status) }}</span>
            </td>
            <td style="min-width:260px;">
              <div class="small text-muted">
                {{ $r->admin_comment ?? '-' }}
              </div>
            </td>
            <td class="text-nowrap">
              @if($r->device_photo_path)
                <a class="btn btn-outline-primary btn-sm" target="_blank"
                   href="{{ asset('storage/'.$r->device_photo_path) }}">Lihat</a>
              @else
                <span class="text-muted small">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    {{ $requests->links() }}
  </div>

</div>
</body>
</html>
