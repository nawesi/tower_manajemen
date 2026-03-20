<p>Yth. {{ $req->user?->pic_name ?? $req->user?->name ?? 'Vendor' }},</p>

@php
  $status = strtoupper($req->status ?? '-');
  $activityLabel = match($req->activity ?? 'install_baru'){
    'dismantle' => 'Dismantle',
    'perbaikan' => 'Perbaikan',
    default => 'Install Baru'
  };
@endphp

<p>
  Pengajuan Anda sudah di <strong>{{ $status }}</strong>.
</p>

<ul>
  <li><strong>Tower:</strong> {{ $req->tower?->name ?? '-' }}</li>
  <li><strong>Kegiatan:</strong> {{ $activityLabel }}</li>
  <li><strong>Vendor/Dept:</strong> {{ $req->vendor_department ?? '-' }}</li>
  <li><strong>Perangkat:</strong> {{ $req->device_name ?? '-' }}</li>
  <li><strong>Stack:</strong> STACK {{ $req->stack_no ?? '-' }}</li>
  <li><strong>Tinggi:</strong> {{ $req->height_from_ground_m ?? '-' }} m</li>
  <li><strong>Tanggal Review:</strong> {{ optional($req->reviewed_at)->format('d M Y H:i') }}</li>
</ul>

@if($req->admin_comment)
  <p><strong>Catatan Admin:</strong><br>{!! nl2br(e($req->admin_comment)) !!}</p>
@endif

<p>Terima kasih.</p>
<p><em>IT Obi</em></p>