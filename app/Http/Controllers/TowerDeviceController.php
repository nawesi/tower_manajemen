<?php

namespace App\Http\Controllers;

use App\Models\Tower;
use App\Models\StackItem;
use App\Models\TowerImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TowerDeviceController extends Controller
{
    public function index(Request $request)
    {
        $towers = Tower::orderBy('name')->get();

        $selectedTowerId = $request->query('tower_id') ?? $towers->first()?->id;

        $selectedTower = $selectedTowerId
            ? Tower::with(['stackItems' => fn ($q) => $q->orderBy('stack_no')->orderBy('device_name')])
                ->find($selectedTowerId)
            : null;

        // Map stack 1..7 untuk tampilan UI
        $stackMap = collect(range(1, 7))->mapWithKeys(function ($no) use ($selectedTower) {
            $items = $selectedTower
                ? $selectedTower->stackItems->where('stack_no', $no)->values()
                : collect();

            return [$no => $items];
        });

        return view('devices.index', compact('towers', 'selectedTower', 'stackMap'));
    }

    public function storeStackItem(Request $request)
    {
        $data = $request->validate([
            'tower_id' => ['required', 'exists:towers,id'],
            'stack_no' => ['required', 'integer', 'min:1', 'max:7'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        StackItem::create($data);

        return back()->with('success', 'Perangkat ditambahkan.');
    }

    public function deleteStackItem(StackItem $stackItem)
    {
        // hapus foto perangkat jika ada
        if ($stackItem->photo_path && Storage::disk('public')->exists($stackItem->photo_path)) {
            Storage::disk('public')->delete($stackItem->photo_path);
        }

        $stackItem->delete();

        return back()->with('success', 'Perangkat dihapus.');
    }

    public function images(Request $request, Tower $tower)
    {
        $stack = (int) $request->query('stack', 0);

        $images = $tower->images()
            ->where('stack_no', $stack)
            ->orderBy('side')
            ->get();

        return view('towers.images', compact('tower', 'images', 'stack'));
    }

    /**
     * API JSON untuk modal detail perangkat (klik nama perangkat).
     */
    public function showStackItem(StackItem $stackItem)
    {
        $stackItem->loadMissing('tower');

        return response()->json([
            'id' => $stackItem->id,
            'device_name' => $stackItem->device_name,
            'stack_no' => $stackItem->stack_no,
            'tower' => $stackItem->tower?->name,
            'photo_url' => $stackItem->photo_path ? asset('storage/' . $stackItem->photo_path) : null,
        ]);
    }

    /**
     * Upload 1 foto per perangkat (StackItem).
     */
    public function uploadStackItemPhoto(Request $request, StackItem $stackItem)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB
        ]);

        // hapus foto lama
        if ($stackItem->photo_path && Storage::disk('public')->exists($stackItem->photo_path)) {
            Storage::disk('public')->delete($stackItem->photo_path);
        }

        $path = $request->file('photo')->store('stack_items', 'public');

        $stackItem->update([
            'photo_path' => $path,
        ]);

        return back()->with('success', 'Foto perangkat berhasil diupload.');
    }

    public function uploadImage(Request $request, Tower $tower)
    {
        $stackNo = (int) ($request->query('stack') ?? $request->input('stack') ?? 0);

        $data = $request->validate([
            'side'  => ['required', 'integer', 'in:1,2,3,4'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $side = (int) $data['side'];

        $file = $request->file('image');
        $ext  = $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'tower_images',
            'tower_' . $tower->id . '_stack_' . $stackNo . '_side_' . $side . '_' . Str::random(8) . '.' . $ext,
            'public'
        );

        // cari record lama sesuai tower+stack+side, hapus file lama
        $record = TowerImage::where('tower_id', $tower->id)
            ->where('stack_no', $stackNo)
            ->where('side', $side)
            ->first();

        if ($record && $record->image_path && Storage::disk('public')->exists($record->image_path)) {
            Storage::disk('public')->delete($record->image_path);
        }

        TowerImage::updateOrCreate(
            [
                'tower_id' => $tower->id,
                'stack_no' => $stackNo,
                'side'     => $side,
            ],
            [
                'image_path' => $path,
            ]
        );

        return back()->with('success', 'Gambar berhasil diupload.');
    }
}