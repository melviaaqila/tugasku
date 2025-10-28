<?php

namespace Database\Seeders;

use App\Models\RoutineTask;
use Illuminate\Database\Seeder;

class RoutineTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            ['divisi_id' => 1, 'nama_tugas' => 'Pemeriksaan status server utama (uptime, CPU, RAM)'],
            ['divisi_id' => 1, 'nama_tugas' => 'Monitoring anomali trafik jaringan'],
            ['divisi_id' => 1, 'nama_tugas' => 'Pengecekan log aplikasi dan sistem operasi'],
            ['divisi_id' => 1, 'nama_tugas' => 'Memastikan semua service production berjalan normal'],
            ['divisi_id' => 1, 'nama_tugas' => 'Backup database harian'],
            ['divisi_id' => 1, 'nama_tugas' => 'Verifikasi integritas file backup mingguan'],
            ['divisi_id' => 1, 'nama_tugas' => 'Update antivirus dan definisi malware'],
            ['divisi_id' => 1, 'nama_tugas' => 'Scanning kerentanan (vulnerability scanning) mingguan'],
            ['divisi_id' => 1, 'nama_tugas' => 'Review dan arsip log keamanan bulanan'],
            ['divisi_id' => 1, 'nama_tugas' => 'Patching sistem operasi dan software bulanan'],
            ['divisi_id' => 1, 'nama_tugas' => 'Manajemen user account (penambahan/penghapusan)'],
            ['divisi_id' => 1, 'nama_tugas' => 'Pengecekan dan perapihan kabel di ruang server'],
            ['divisi_id' => 1, 'nama_tugas' => 'Laporan performa server bulanan'],
            ['divisi_id' => 1, 'nama_tugas' => 'Pengecekan kapasitas penyimpanan (storage)'],
            ['divisi_id' => 1, 'nama_tugas' => 'Rotasi kunci enkripsi (encryption keys) per kuartal'],
        ];

        foreach ($tasks as $task) {
            RoutineTask::create($task);
        }
    }
}