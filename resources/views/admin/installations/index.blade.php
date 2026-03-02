<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Review Pengajuan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0 fw-bold">Admin - Review Pengajuan Pemasangan</h1>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.welcome') }}">Admin Dashboard</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  

  <div class="d-flex gap-2 mb-3">
    <a class="btn btn-sm {{ $status===null ? 'btn-dark' : 'btn-outline-dark' }}"
       href="{{ route('admin.installations.index') }}">Semua</a>

    <a class="btn btn-sm {{ $status==='pending' ? 'btn-warning' : 'btn-outline-warning' }}"
       href="{{ route('admin.installations.index', ['status'=>'pending']) }}">Pending</a>

    <a class="btn btn-sm {{ $status==='approved' ? 'btn-success' : 'btn-outline-success' }}"
       href="{{ route('admin.installations.index', ['status'=>'approved']) }}">Approved</a>

    <a class="btn btn-sm {{ $status==='rejected' ? 'btn-danger' : 'btn-outline-danger' }}"
       href="{{ route('admin.installations.index', ['status'=>'rejected']) }}">Rejected</a>
    <a class="btn btn-primary btn-sm ms-auto"
   href="{{ route('admin.installations.export', array_filter(['status' => $status])) }}">
  Export CSV
</a>

  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table mb-0 align-middle">
        <thead>
        <tr>
          <th>Tanggal</th>
          <th>Tower</th>
          <th>Vendor/Dept</th>
          <th>Perangkat</th>
          <th>Stack</th>
          <th>Foto</th>
          <th>Status</th>
          <th style="width:360px;">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse($requests as $r)
          <tr>
            <td class="text-nowrap">{{ $r->created_at->format('d M Y H:i') }}</td>
            <td>{{ $r->tower?->name }}</td>
            <td>{{ $r->vendor_department }}</td>
            <td>{{ $r->device_name }}</td>
            <td>STACK {{ $r->stack_no }}</td>
            <td class="text-nowrap">
              @if($r->device_photo_path)
                <a class="btn btn-outline-primary btn-sm" target="_blank"
                   href="{{ asset('storage/'.$r->device_photo_path) }}">Lihat</a>
              @else
                <span class="text-muted small">-</span>
              @endif
            </td>
            <td class="text-nowrap">
              @php
                $badge = match($r->status){
                  'approved' => 'success',
                  'rejected' => 'danger',
                  default => 'warning'
                };
              @endphp
              <span class="badge bg-{{ $badge }}">{{ strtoupper($r->status) }}</span>
              @if($r->reviewed_at)
                <div class="small text-muted">{{ $r->reviewed_at->format('d M Y H:i') }}</div>
              @endif
            </td>

            <td>
              <form class="d-flex flex-column gap-2"
                    method="POST"
                    action="{{ route('admin.installations.update', $r->id) }}">
                @csrf
                @method('PATCH')

                <textarea class="form-control form-control-sm"
                          name="admin_comment"
                          rows="2"
                          placeholder="Komentar admin (opsional)">{{ old('admin_comment', $r->admin_comment) }}</textarea>

                <div class="d-flex gap-2">
                  <button name="status" value="approved" class="btn btn-success btn-sm w-50">Approve</button>
                  <button name="status" value="rejected" class="btn btn-danger btn-sm w-50">Reject</button>
                </div>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td>
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
