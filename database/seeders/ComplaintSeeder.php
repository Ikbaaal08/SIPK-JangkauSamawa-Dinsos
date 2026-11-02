<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $bansos = Category::where('code', 'BANSOS')->first();
        $difabel = Category::where('code', 'DIFABEL')->first();
        $lansia = Category::where('code', 'LANSIA')->first();

        $petugas = User::where('role', 'petugas_penerima')->first();
        $kepala = User::where('role', 'kepala_dinas')->first();
        $pelaksana = User::where('role', 'petugas_pelaksana')->first();

        $complaints = [
            [
                'ticket_code' => 'JS-2026-0001',
                'reporter_name' => 'Ahmad Saputra',
                'reporter_phone' => '6281230000001',
                'reporter_address' => 'Desa Labuhan Badas',
                'category_id' => $bansos?->id,
                'title' => 'Belum menerima bantuan sosial',
                'description' => 'Saya belum menerima bantuan sosial yang seharusnya diterima bulan ini.',
                'village' => 'Labuhan Badas',
                'district' => 'Sumbawa',
                'status' => 'baru',
            ],
            [
                'ticket_code' => 'JS-2026-0002',
                'reporter_name' => 'Siti Aminah',
                'reporter_phone' => '6281230000002',
                'reporter_address' => 'Desa Pungka',
                'category_id' => $difabel?->id,
                'title' => 'Permohonan pendampingan penyandang disabilitas',
                'description' => 'Mohon pendampingan untuk anak saya yang mengalami disabilitas.',
                'village' => 'Pungka',
                'district' => 'Sumbawa Besar',
                'status' => 'diproses',
            ],
            [
                'ticket_code' => 'JS-2026-0003',
                'reporter_name' => 'Muhammad Yusuf',
                'reporter_phone' => '6281230000003',
                'reporter_address' => 'Desa Batu Alang',
                'category_id' => $lansia?->id,
                'title' => 'Pendataan lansia belum lengkap',
                'description' => 'Data lansia di desa kami belum terdata dengan lengkap.',
                'village' => 'Batu Alang',
                'district' => 'Sumbawa',
                'status' => 'didisposisikan',
            ],
        ];

        foreach ($complaints as $data) {
            $complaint = Complaint::updateOrCreate(
                ['ticket_code' => $data['ticket_code']],
                $data
            );

            $complaint->histories()->create([
                'user_id' => $petugas?->id,
                'previous_status' => null,
                'new_status' => $data['status'],
                'note' => 'Aduan awal dibuat melalui WhatsApp.',
            ]);
        }

        $complaint = Complaint::where('ticket_code', 'JS-2026-0003')->first();

        if ($complaint && $kepala && $pelaksana) {
            $complaint->dispositions()->create([
                'assigned_by' => $kepala->id,
                'assigned_to' => $pelaksana->id,
                'instruction' => 'Mohon petugas melakukan verifikasi dan pendataan ulang lansia di Desa Batu Alang.',
                'due_date' => now()->addDays(7)->toDateString(),
                'status' => 'aktif',
            ]);
        }
    }
}
