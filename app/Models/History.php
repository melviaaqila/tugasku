<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    /** @use HasFactory<\Database\Factories\HistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_tugas',
        'jenis_tugas',
        'divisi',
        'tanggal_dibuat',
        'tanggal_selesai',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
