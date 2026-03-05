<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - User Kontrol</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .table thead th { white-space: nowrap; }
    .nowrap { white-space: nowrap; }
    .small-muted { font-size:.85rem; color:#6c757d; }
    .modal .form-label { font-weight:600; }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h5 mb-0 fw-bold">Admin - User Kontrol</h1>
      <div class="small-muted">Kelola akun vendor (aktif / nonaktif), batas akses, dan export data.</div>
    </div>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.welcome') }}">Admin Dashboard</a>
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

  <div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
    <a class="btn btn-sm {{ $tab==='active' ? 'btn-dark' : 'btn-outline-dark' }}"
       href="{{ route('admin.users.index', ['tab'=>'active']) }}">User Aktif</a>

    <a class="btn btn-sm {{ $tab==='inactive' ? 'btn-secondary' : 'btn-outline-secondary' }}"
       href="{{ route('admin.users.index', ['tab'=>'inactive']) }}">User Non Aktif</a>

    <div class="ms-auto d-flex gap-2">
      <a class="btn btn-outline-primary btn-sm"
         href="{{ route('admin.users.export', ['tab' => $tab]) }}">
        Export CSV
      </a>

      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
        + Tambah User
      </button>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Vendor</th>
            <th>Nama PIC</th>
            <th>Username</th>
            <th>No. HP</th>
            <th>Email</th>
            <th>Deskripsi Tugas</th>
            <th>Batas Akses</th>
            <th>Status</th>
            <th style="width:320px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse($users as $i => $u)
          @php
            $no = ($users->currentPage()-1)*$users->perPage() + $i + 1;
            $expired = $u->access_expires_at ? \Carbon\Carbon::parse($u->access_expires_at)->isPast() : false;
          @endphp
          <tr>
            <td class="nowrap">{{ $no }}</td>
            <td>{{ $u->vendor_name }}</td>
            <td>{{ $u->pic_name }}</td>
            <td class="nowrap"><span class="badge text-bg-light">{{ $u->username }}</span></td>
            <td class="nowrap">{{ $u->phone ?? '-' }}</td>
            <td class="nowrap">{{ $u->email }}</td>
            <td style="min-width:220px;">{{ $u->task_desc ?? '-' }}</td>
            <td class="nowrap">
              @if($u->access_expires_at)
                {{ \Carbon\Carbon::parse($u->access_expires_at)->format('d M Y H:i') }}
                @if($expired)
                  <div class="small text-danger">Expired</div>
                @endif
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td class="nowrap">
              @php
                $badge = $u->account_status === 'active' && !$expired ? 'success' : 'secondary';
              @endphp
              <span class="badge bg-{{ $badge }}">
                {{ strtoupper($u->account_status) }}
              </span>
            </td>

            <td>
              <div class="d-flex flex-wrap gap-2">
                <!-- Edit -->
                <button class="btn btn-outline-dark btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#editUserModal"
                        data-id="{{ $u->id }}"
                        data-vendor_name="{{ e($u->vendor_name) }}"
                        data-pic_name="{{ e($u->pic_name) }}"
                        data-username="{{ e($u->username) }}"
                        data-phone="{{ e($u->phone) }}"
                        data-email="{{ e($u->email) }}"
                        data-task_desc="{{ e($u->task_desc) }}"
                        data-access_expires_at="{{ $u->access_expires_at ? \Carbon\Carbon::parse($u->access_expires_at)->format('Y-m-d\TH:i') : '' }}"
                        data-account_status="{{ e($u->account_status) }}">
                  Edit
                </button>

                <!-- Reset Password -->
                <button class="btn btn-outline-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#resetPasswordModal"
                        data-id="{{ $u->id }}"
                        data-username="{{ e($u->username) }}">
                  Reset Password
                </button>

                <!-- Toggle Status -->
                @if($u->account_status === 'active' && !$expired)
                  <form method="POST" action="{{ route('admin.users.toggleStatus', $u->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="account_status" value="inactive">
                    <button class="btn btn-outline-danger btn-sm">Nonaktifkan</button>
                  </form>
                @else
                  <form method="POST" action="{{ route('admin.users.toggleStatus', $u->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="account_status" value="active">
                    <button class="btn btn-outline-success btn-sm">Aktifkan</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="text-center text-muted py-4">Tidak ada data.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">
    {{ $users->links() }}
  </div>

</div>

<!-- ===================== MODAL: CREATE ===================== -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah User Vendor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Vendor</label>
            <input type="text" name="vendor_name" class="form-control" required value="{{ old('vendor_name') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Nama PIC</label>
            <input type="text" name="pic_name" class="form-control" required value="{{ old('pic_name') }}">
            <div class="form-text">Nama PIC juga bisa dijadikan dasar username.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Username (Login)</label>
            <input type="text" name="username" class="form-control" required value="{{ old('username') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">No. HP (WhatsApp)</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Email (Notifikasi Approval)</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
          </div>

          <div class="col-md-8">
            <label class="form-label">Deskripsi Tugas</label>
            <input type="text" name="task_desc" class="form-control" value="{{ old('task_desc') }}" placeholder="Contoh: Pemasangan FO backbone area Obi">
          </div>
          <div class="col-md-4">
            <label class="form-label">Status Akun</label>
            <select name="account_status" class="form-select" required>
              <option value="active" selected>Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Batas Akses (opsional)</label>
            <input type="datetime-local" name="access_expires_at" class="form-control" value="{{ old('access_expires_at') }}">
            <div class="form-text">Jika lewat tanggal ini, otomatis dianggap Non Aktif.</div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- ===================== MODAL: EDIT ===================== -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" id="editUserForm" action="#">
      @csrf
      @method('PATCH')

      <div class="modal-header">
        <h5 class="modal-title">Edit User Vendor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="edit_id">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Vendor</label>
            <input type="text" name="vendor_name" id="edit_vendor_name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nama PIC</label>
            <input type="text" name="pic_name" id="edit_pic_name" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Username (Login)</label>
            <input type="text" name="username" id="edit_username" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">No. HP (WhatsApp)</label>
            <input type="text" name="phone" id="edit_phone" class="form-control">
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="edit_email" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status Akun</label>
            <select name="account_status" id="edit_account_status" class="form-select" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="col-md-8">
            <label class="form-label">Deskripsi Tugas</label>
            <input type="text" name="task_desc" id="edit_task_desc" class="form-control">
          </div>

          <div class="col-md-4">
            <label class="form-label">Batas Akses</label>
            <input type="datetime-local" name="access_expires_at" id="edit_access_expires_at" class="form-control">
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
        <button class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- ===================== MODAL: RESET PASSWORD ===================== -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="resetPasswordForm" action="#">
      @csrf
      @method('PATCH')

      <div class="modal-header">
        <h5 class="modal-title">Reset Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="small text-muted mb-2">Reset password untuk user:</div>
        <div class="fw-semibold mb-3" id="reset_username">-</div>

        <label class="form-label">Password Baru</label>
        <input type="password" name="password" class="form-control" required>
        <div class="form-text">Minimal 6 karakter.</div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary">Reset</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // EDIT MODAL fill
  const editModal = document.getElementById('editUserModal');
  editModal.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;

    const id = btn.getAttribute('data-id');
    const vendor_name = btn.getAttribute('data-vendor_name');
    const pic_name = btn.getAttribute('data-pic_name');
    const username = btn.getAttribute('data-username');
    const phone = btn.getAttribute('data-phone');
    const email = btn.getAttribute('data-email');
    const task_desc = btn.getAttribute('data-task_desc');
    const access_expires_at = btn.getAttribute('data-access_expires_at');
    const account_status = btn.getAttribute('data-account_status');

    document.getElementById('edit_vendor_name').value = vendor_name || '';
    document.getElementById('edit_pic_name').value = pic_name || '';
    document.getElementById('edit_username').value = username || '';
    document.getElementById('edit_phone').value = phone || '';
    document.getElementById('edit_email').value = email || '';
    document.getElementById('edit_task_desc').value = task_desc || '';
    document.getElementById('edit_access_expires_at').value = access_expires_at || '';
    document.getElementById('edit_account_status').value = account_status || 'active';

    document.getElementById('editUserForm').action = "{{ url('/admin/users') }}/" + id;
  });

  // RESET PASSWORD MODAL fill
  const resetModal = document.getElementById('resetPasswordModal');
  resetModal.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const id = btn.getAttribute('data-id');
    const username = btn.getAttribute('data-username');

    document.getElementById('reset_username').innerText = username || '-';
    document.getElementById('resetPasswordForm').action = "{{ url('/admin/users') }}/" + id + "/reset-password";
  });
</script>

</body>
</html>