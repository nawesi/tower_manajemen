<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstallationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\InstallationStatusMail;

class InstallationReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status'); // pending/approved/rejected/null

        $q = InstallationRequest::with(['tower', 'user'])->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $q->where('status', $status);
        }

        $requests = $q->paginate(12)->withQueryString();

        return view('admin.installations.index', compact('requests', 'status'));
    }

    public function update(Request $request, InstallationRequest $installation)
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'admin_comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldStatus = $installation->status;

        $installation->update([
            'status' => $data['status'],
            'admin_comment' => $data['admin_comment'] ?? null,
            'reviewed_at' => now(),
        ]);

        $emailInfo = null;

        // Kirim email hanya jika status berubah
        if ($oldStatus !== $installation->status) {
            $installation->loadMissing(['user', 'tower']);

            $recipient = $installation->user?->email;

            if (!empty($recipient)) {
                try {
                    Mail::to($recipient)->send(new InstallationStatusMail($installation));

                    Log::info('Email sent', [
                        'to' => $recipient,
                        'installation_id' => $installation->id,
                        'status' => $installation->status,
                    ]);

                    $emailInfo = "Email terkirim ke {$recipient}.";
                } catch (\Throwable $e) {
                    Log::error('Email send failed', [
                        'to' => $recipient,
                        'installation_id' => $installation->id,
                        'status' => $installation->status,
                        'error' => $e->getMessage(),
                    ]);

                    $emailInfo = "Email gagal dikirim ke {$recipient} (cek log).";
                }
            } else {
                $emailInfo = "Email vendor tidak ditemukan, jadi notifikasi tidak dikirim.";
            }
        } else {
            $emailInfo = "Status tidak berubah, email tidak dikirim.";
        }

        return back()->with('success', 'Status pengajuan berhasil diupdate. ' . $emailInfo);
    }

    public function export(Request $request)
    {
        $status = $request->query('status'); // pending/approved/rejected/null

        $query = InstallationRequest::with('tower')->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        } else {
            $status = null;
        }

        $filename = 'pengajuan_pemasangan_' . ($status ?? 'semua') . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Tanggal Pengajuan',
                'Tower',
                'Vendor/Dept',
                'Perangkat',
                'Stack',
                'Status',
                'Tanggal Review',
                'Komentar Admin',
                'Link Foto',
            ]);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    $photoUrl = $r->device_photo_path ? asset('storage/' . $r->device_photo_path) : '';

                    fputcsv($out, [
                        optional($r->created_at)->format('Y-m-d H:i:s'),
                        $r->tower?->name,
                        $r->vendor_department,
                        $r->device_name,
                        'STACK ' . $r->stack_no,
                        $r->status,
                        optional($r->reviewed_at)->format('Y-m-d H:i:s'),
                        $r->admin_comment,
                        $photoUrl,
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}