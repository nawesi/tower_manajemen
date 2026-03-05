<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserControlController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'active'); // active | inactive

        $now = now();

        $base = User::query()->where('role', 'vendor');

        $activeQuery = (clone $base)
            ->where('account_status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('access_expires_at')
                  ->orWhere('access_expires_at', '>=', $now);
            })
            ->orderBy('vendor_name')
            ->orderBy('pic_name');

        $inactiveQuery = (clone $base)
            ->where(function ($q) use ($now) {
                $q->where('account_status', 'inactive')
                  ->orWhere(function ($q2) use ($now) {
                      $q2->whereNotNull('access_expires_at')
                         ->where('access_expires_at', '<', $now);
                  });
            })
            ->orderBy('vendor_name')
            ->orderBy('pic_name');

        $users = ($tab === 'inactive' ? $inactiveQuery : $activeQuery)
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'tab'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_name' => ['required', 'string', 'max:120'],
            'pic_name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:120', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'max:60'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:190'],
            'task_desc' => ['nullable', 'string', 'max:255'],
            'access_expires_at' => ['nullable', 'date'],
            'account_status' => ['required', 'in:active,inactive'],
        ]);

        User::create([
            'name' => $data['pic_name'], // optional
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => 'vendor',
            'email' => $data['email'],
            'vendor_name' => $data['vendor_name'],
            'pic_name' => $data['pic_name'],
            'phone' => $data['phone'] ?? null,
            'task_desc' => $data['task_desc'] ?? null,
            'access_expires_at' => $data['access_expires_at'] ?? null,
            'account_status' => $data['account_status'],
        ]);

        return redirect()->route('admin.users.index', ['tab' => 'active'])
            ->with('success', 'User vendor berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'vendor_name' => ['required', 'string', 'max:120'],
            'pic_name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:120', 'unique:users,username,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:190'],
            'task_desc' => ['nullable', 'string', 'max:255'],
            'access_expires_at' => ['nullable', 'date'],
            'account_status' => ['required', 'in:active,inactive'],
        ]);

        $user->update([
            'name' => $data['pic_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'vendor_name' => $data['vendor_name'],
            'pic_name' => $data['pic_name'],
            'phone' => $data['phone'] ?? null,
            'task_desc' => $data['task_desc'] ?? null,
            'access_expires_at' => $data['access_expires_at'] ?? null,
            'account_status' => $data['account_status'],
        ]);

        return back()->with('success', 'Data user berhasil diupdate.');
    }

    public function toggleStatus(Request $request, User $user)
    {
        $data = $request->validate([
            'account_status' => ['required', 'in:active,inactive'],
        ]);

        $user->update(['account_status' => $data['account_status']]);

        return back()->with('success', 'Status user berhasil diubah.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:60'],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password berhasil direset.');
    }

    public function export(Request $request)
    {
        $tab = $request->query('tab', 'all'); // active|inactive|all
        $now = now();

        $q = User::query()->where('role', 'vendor');

        if ($tab === 'active') {
            $q->where('account_status', 'active')
              ->where(function ($x) use ($now) {
                  $x->whereNull('access_expires_at')->orWhere('access_expires_at', '>=', $now);
              });
        } elseif ($tab === 'inactive') {
            $q->where(function ($x) use ($now) {
                $x->where('account_status', 'inactive')
                  ->orWhere(function ($y) use ($now) {
                      $y->whereNotNull('access_expires_at')->where('access_expires_at', '<', $now);
                  });
            });
        }

        $filename = 'users_vendor_' . $tab . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($q) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'No', 'Vendor', 'PIC', 'Username', 'Phone', 'Email', 'Deskripsi', 'Batas Akses', 'Status'
            ]);

            $i = 1;
            $q->orderBy('vendor_name')->orderBy('pic_name')->chunk(500, function ($rows) use (&$i, $out) {
                foreach ($rows as $u) {
                    fputcsv($out, [
                        $i++,
                        $u->vendor_name,
                        $u->pic_name,
                        $u->username,
                        $u->phone,
                        $u->email,
                        $u->task_desc,
                        optional($u->access_expires_at)->format('Y-m-d H:i:s'),
                        $u->account_status,
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}