<?php

use App\Actions\Dashboard\GetDashboardStatisticsAction;
use App\DTOs\Dashboard\GetDashboardStatisticsDto;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Disposition;
use App\Models\User;

beforeEach(function () {
    $this->category = Category::create([
        'name' => 'Bantuan Sosial',
        'code' => 'BANSOS',
        'description' => 'Bantuan sosial tunai dan sembako',
        'is_active' => true,
    ]);
});

test('get dashboard statistics action calculates correct metrics for admin', function () {
    $admin = User::factory()->admin()->create();

    Complaint::create([
        'ticket_code' => 'JS-2026-0001',
        'reporter_name' => 'Warga 1',
        'reporter_phone' => '628123456001',
        'category_id' => $this->category->id,
        'title' => 'Aduan 1',
        'description' => 'Deskripsi aduan 1',
        'status' => 'baru',
    ]);

    Complaint::create([
        'ticket_code' => 'JS-2026-0002',
        'reporter_name' => 'Warga 2',
        'reporter_phone' => '628123456002',
        'category_id' => $this->category->id,
        'title' => 'Aduan 2',
        'description' => 'Deskripsi aduan 2',
        'status' => 'diproses',
    ]);

    Complaint::create([
        'ticket_code' => 'JS-2026-0003',
        'reporter_name' => 'Warga 3',
        'reporter_phone' => '628123456003',
        'category_id' => $this->category->id,
        'title' => 'Aduan 3',
        'description' => 'Deskripsi aduan 3',
        'status' => 'selesai',
    ]);

    Complaint::create([
        'ticket_code' => 'JS-2026-0004',
        'reporter_name' => 'Warga 4',
        'reporter_phone' => '628123456004',
        'category_id' => $this->category->id,
        'title' => 'Aduan 4',
        'description' => 'Deskripsi aduan 4',
        'status' => 'ditolak',
    ]);

    $action = app(GetDashboardStatisticsAction::class);
    $dto = $action->handle(new GetDashboardStatisticsDto($admin));

    expect($dto->totalAduan)->toBe(4)
        ->and($dto->aduanBaru)->toBe(1)
        ->and($dto->aduanDiproses)->toBe(1)
        ->and($dto->aduanSelesai)->toBe(1)
        ->and($dto->extra['aduanDitolak'])->toBe(1);
});

test('get dashboard statistics action calculates correct metrics for petugas pelaksana', function () {
    $kepala = User::factory()->kepalaDinas()->create();
    $pelaksana = User::factory()->petugasPelaksana()->create();
    $otherPelaksana = User::factory()->petugasPelaksana()->create();

    $complaint1 = Complaint::create([
        'ticket_code' => 'JS-2026-0010',
        'reporter_name' => 'Warga A',
        'reporter_phone' => '628123456010',
        'category_id' => $this->category->id,
        'title' => 'Tugas Pelaksana 1',
        'description' => 'Deskripsi tugas 1',
        'status' => 'didisposisikan',
    ]);

    Disposition::create([
        'complaint_id' => $complaint1->id,
        'assigned_by' => $kepala->id,
        'assigned_to' => $pelaksana->id,
        'instruction' => 'Cek lokasi desa A',
        'status' => 'aktif',
    ]);

    $complaint2 = Complaint::create([
        'ticket_code' => 'JS-2026-0011',
        'reporter_name' => 'Warga B',
        'reporter_phone' => '628123456011',
        'category_id' => $this->category->id,
        'title' => 'Tugas Pelaksana 2',
        'description' => 'Deskripsi tugas 2',
        'status' => 'ditindaklanjuti',
    ]);

    Disposition::create([
        'complaint_id' => $complaint2->id,
        'assigned_by' => $kepala->id,
        'assigned_to' => $pelaksana->id,
        'instruction' => 'Salurkan bantuan',
        'status' => 'aktif',
    ]);

    // Disposition for other pelaksana (should NOT count towards $pelaksana)
    $complaintOther = Complaint::create([
        'ticket_code' => 'JS-2026-0012',
        'reporter_name' => 'Warga C',
        'reporter_phone' => '628123456012',
        'category_id' => $this->category->id,
        'title' => 'Tugas Orang Lain',
        'description' => 'Bukan tugas pelaksana ini',
        'status' => 'didisposisikan',
    ]);

    Disposition::create([
        'complaint_id' => $complaintOther->id,
        'assigned_by' => $kepala->id,
        'assigned_to' => $otherPelaksana->id,
        'instruction' => 'Tugas pelaksana lain',
        'status' => 'aktif',
    ]);

    $action = app(GetDashboardStatisticsAction::class);
    $dto = $action->handle(new GetDashboardStatisticsDto($pelaksana));

    expect($dto->totalAduan)->toBe(2)
        ->and($dto->aduanBaru)->toBe(1)
        ->and($dto->aduanDiproses)->toBe(1)
        ->and($dto->aduanSelesai)->toBe(0)
        ->and($dto->extra['tugasAktif'])->toBe(2);
});

test('admin dashboard renders four statistic cards', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Total Aduan')
        ->assertSee('Aduan Baru')
        ->assertSee('Aduan Diproses')
        ->assertSee('Aduan Selesai');
});

test('petugas penerima dashboard renders four statistic cards', function () {
    $penerima = User::factory()->petugasPenerima()->create();

    $this->actingAs($penerima)
        ->get(route('petugas.dashboard'))
        ->assertOk()
        ->assertSee('Total Aduan')
        ->assertSee('Aduan Baru')
        ->assertSee('Aduan Diproses')
        ->assertSee('Aduan Selesai');
});

test('kepala dinas dashboard renders four statistic cards', function () {
    $kepala = User::factory()->kepalaDinas()->create();

    $this->actingAs($kepala)
        ->get(route('kepala-dinas.dashboard'))
        ->assertOk()
        ->assertSee('Total Aduan')
        ->assertSee('Perlu Disposisi')
        ->assertSee('Disposisi Berjalan')
        ->assertSee('Aduan Selesai');
});

test('petugas pelaksana dashboard renders four statistic cards', function () {
    $pelaksana = User::factory()->petugasPelaksana()->create();

    $this->actingAs($pelaksana)
        ->get(route('pelaksana.dashboard'))
        ->assertOk()
        ->assertSee('Total Tugas Aduan')
        ->assertSee('Tugas Baru')
        ->assertSee('Ditindaklanjuti')
        ->assertSee('Tugas Selesai');
});
