<?php

namespace App\Http\Controllers;

use App\Models\Tower;
use App\Models\StackItem;
use Illuminate\Http\Request;
use App\Models\TowerImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TowerDeviceController extends Controller
{
    public function index(Request $request)
    {
        $towers = Tower::orderBy('name')->get();

        $selectedTowerId = $request->query('tower_id') ?? $towers->first()?->id;

        $selectedTower = $selectedTowerId
            ? Tower::with(['stackItems' => fn($q) => $q->orderBy('stack_no')->orderBy('device_name')])
                ->find($selectedTowerId)
            : null;

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
        'tower_'.$tower->id.'_stack_'.$stackNo.'_side_'.$side.'_'.\Illuminate\Support\Str::random(8).'.'.$ext,
        'public'
    );

    // cari record lama sesuai tower+stack+side
    $record = \App\Models\TowerImage::where('tower_id', $tower->id)
        ->where('stack_no', $stackNo)
        ->where('side', $side)
        ->first();

    if ($record && $record->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($record->image_path)) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($record->image_path);
    }

    \App\Models\TowerImage::updateOrCreate(
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
