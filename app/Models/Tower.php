<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tower extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'height_m',
        'owner',
        'status',
        'tower_id',
        'stack_no',
        'side',
        'image_path',
    ];

    /**
     * =========================
     * RELATIONS
     * =========================
     */

    // Tower → banyak perangkat (stack items)
    public function stackItems(): HasMany
    {
        return $this->hasMany(StackItem::class);
    }

    // Tower → banyak gambar (tower_images)
    public function images(): HasMany
    {
        return $this->hasMany(TowerImage::class);
    }

    public function otbs()
    {
    return $this->hasMany(\App\Models\TowerOtb::class);
    }

    // Tower → banyak pengajuan instalasi
    public function installationRequests(): HasMany
    {
        return $this->hasMany(InstallationRequest::class);
    }

    /**
     * =========================
     * HELPERS
     * =========================
     */

    /**
     * Ambil 1 gambar berdasarkan stack & sisi
     * contoh: $tower->imageByStackSide(2, 3)
     *
     * @return TowerImage|null
     */
    public function imageByStackSide(?int $stackNo, int $side)
    {
        return $this->images()
            ->where('side', $side)
            ->when(
                is_null($stackNo),
                fn ($q) => $q->whereNull('stack_no'),
                fn ($q) => $q->where('stack_no', $stackNo)
            )
            ->first();
    }

    /**
     * Ambil semua gambar berdasarkan stack
     * contoh: $tower->imagesByStack(2)
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, TowerImage>
     */
    public function imagesByStack(?int $stackNo)
    {
        return $this->images()
            ->when(
                is_null($stackNo),
                fn ($q) => $q->whereNull('stack_no'),
                fn ($q) => $q->where('stack_no', $stackNo)
            )
            ->get();
    }
}
