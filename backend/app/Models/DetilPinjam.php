<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetilPinjam extends Model
{
    protected $table = 'detail_pinjam';

    protected $fillable = [
        'peminjaman_id', 'alat_id', 'jumlah'
    ];

    protected function casts(): array {
        return [
            'jumlah' => 'integer',
        ];
    }

    protected function peminjaman(): BelongsTo {
        return $this->belongsTo(Peminjaman::class);
    }

    protected function alat(): BelongsTo {
        return $this->belongsTo(Alat::class);
    }
}
