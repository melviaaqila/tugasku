<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutineTask extends Model
{
    /** @use HasFactory<\Database\Factories\RoutineTaskFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'nama_tugas',
        'divisi_id',
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }
}