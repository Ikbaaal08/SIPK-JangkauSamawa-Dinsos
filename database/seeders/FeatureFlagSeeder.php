<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'key' => 'registration',
                'label' => 'Registrasi Mandiri',
                'description' => 'Mengaktifkan atau menonaktifkan halaman dan route pendaftaran mandiri.',
                'is_enabled' => false,
            ],
            [
                'key' => 'email_verification',
                'label' => 'Verifikasi Email',
                'description' => 'Mengaktifkan atau menonaktifkan kebutuhan verifikasi email setelah login.',
                'is_enabled' => false,
            ],
            [
                'key' => 'whatsapp_gateway',
                'label' => 'WhatsApp Gateway',
                'description' => 'Mengaktifkan pengiriman pesan notifikasi otomatis via WhatsApp Fonnte.',
                'is_enabled' => false,
            ],
            [
                'key' => 'whatsapp_verification',
                'label' => 'Verifikasi WhatsApp OTP',
                'description' => 'Mengaktifkan alur verifikasi nomor WhatsApp menggunakan kode OTP 6 digit.',
                'is_enabled' => false,
            ],
            [
                'key' => 'public_tracking',
                'label' => 'Pelacakan Aduan Publik',
                'description' => 'Mengizinkan warga melacak progres penanganan aduan menggunakan kode tiket.',
                'is_enabled' => false,
            ],
            [
                'key' => 'maintenance_mode',
                'label' => 'Mode Pemeliharaan',
                'description' => 'Mengaktifkan mode pemeliharaan sistem untuk pembatasan akses sementara.',
                'is_enabled' => false,
            ],
        ];

        foreach ($features as $feature) {
            FeatureFlag::updateOrCreate(
                ['key' => $feature['key']],
                $feature
            );
        }
    }
}
