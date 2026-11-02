<?php

use App\Actions\Dashboard\GetAdminDashboardDataAction;
use App\Actions\Dashboard\GetKabagDashboardDataAction;
use App\Actions\Dashboard\GetKadisDashboardDataAction;
use App\Actions\Dashboard\GetPetugasDashboardDataAction;
use App\Actions\Dashboard\GetSuperAdminDashboardDataAction;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Disposition;
use App\Models\User;

beforeEach(function () {
    $this->catLinjamsos = Category::create([
        'name' => 'Bantuan Sosial',
        'code' => 'BANSOS',
        'bidang' => 'perlindungan_jaminan_sosial',
        'is_active' => true,
    ]);

    $this->catRehsos = Category::create([
        'name' => 'Penyandang Disabilitas',
        'code' => 'DIFABEL',
        'bidang' => 'rehabilitasi_sosial',
        'is_active' => true,
    ]);

    $this->catDayasos = Category::create([
        'name' => 'Kemiskinan dan Ketahanan',
        'code' => 'KEMSOS',
        'bidang' => 'pengembangan_kesejahteraan',
        'is_active' => true,
    ]);
});

test('1. super admin melihat statistik seluruh aduan', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);

    Complaint::factory()->create(['category_id' => $this->catLinjamsos->id, 'status' => 'baru']);
    Complaint::factory()->create(['category_id' => $this->catRehsos->id, 'status' => 'diproses']);
    Complaint::factory()->create(['category_id' => $this->catDayasos->id, 'status' => 'selesai']);

    $action = app(GetSuperAdminDashboardDataAction::class);
    $data = $action->handle($superAdmin);

    expect($data['statusCounts']['total'])->toBe(3)
        ->and($data['statusCounts']['baru'])->toBe(1)
        ->and($data['statusCounts']['diproses'])->toBe(1)
        ->and($data['statusCounts']['selesai'])->toBe(1);

    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Super Admin')
        ->assertSee('Total Aduan');
});

test('2. admin melihat statistik seluruh aduan', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Complaint::factory()->create(['category_id' => $this->catLinjamsos->id, 'status' => 'baru']);
    Complaint::factory()->create(['category_id' => $this->catRehsos->id, 'status' => 'selesai']);

    $action = app(GetAdminDashboardDataAction::class);
    $data = $action->handle($admin);

    expect($data['statusCounts']['total'])->toBe(2);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Administrator')
        ->assertSee('Total Aduan');
});

test('3. kadis melihat statistik seluruh aduan', function () {
    $kadis = User::factory()->create(['role' => 'kadis']);

    Complaint::factory()->create(['category_id' => $this->catLinjamsos->id, 'status' => 'diteruskan']);
    Complaint::factory()->create(['category_id' => $this->catRehsos->id, 'status' => 'didisposisikan']);

    $action = app(GetKadisDashboardDataAction::class);
    $data = $action->handle($kadis);

    expect($data['statusCounts']['total'])->toBe(2)
        ->and($data['statusCounts']['diteruskan'])->toBe(1)
        ->and($data['statusCounts']['didisposisikan'])->toBe(1);

    $this->actingAs($kadis)
        ->get(route('kepala-dinas.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Kepala Dinas')
        ->assertSee('Total Aduan');
});

test('4. kabag hanya melihat statistik aduan pada bidangnya', function () {
    $kabagLinjamsos = User::factory()->create([
        'role' => 'kabag',
        'bidang' => 'perlindungan_jaminan_sosial',
    ]);

    // 2 aduan pada bidang Linjamsos
    Complaint::factory()->create(['category_id' => $this->catLinjamsos->id, 'status' => 'diteruskan']);
    Complaint::factory()->create(['category_id' => $this->catLinjamsos->id, 'status' => 'selesai']);

    // 1 aduan pada bidang Rehsos (bidang lain)
    Complaint::factory()->create(['category_id' => $this->catRehsos->id, 'status' => 'diteruskan']);

    $action = app(GetKabagDashboardDataAction::class);
    $data = $action->handle($kabagLinjamsos);

    expect($data['statusCounts']['total'])->toBe(2)
        ->and($data['statusCounts']['diteruskan'])->toBe(1)
        ->and($data['statusCounts']['selesai'])->toBe(1);
});

test('5. petugas hanya melihat statistik aduan yang ditugaskan kepadanya', function () {
    $kadis = User::factory()->create(['role' => 'kadis']);
    $petugas1 = User::factory()->create(['role' => 'petugas']);
    $petugas2 = User::factory()->create(['role' => 'petugas']);

    $complaint1 = Complaint::factory()->create([
        'category_id' => $this->catLinjamsos->id,
        'status' => 'ditindaklanjuti',
    ]);
    Disposition::create([
        'complaint_id' => $complaint1->id,
        'assigned_by' => $kadis->id,
        'assigned_to' => $petugas1->id,
        'instruction' => 'Segera salurkan',
        'status' => 'aktif',
    ]);

    $complaint2 = Complaint::factory()->create([
        'category_id' => $this->catRehsos->id,
        'status' => 'ditindaklanjuti',
    ]);
    Disposition::create([
        'complaint_id' => $complaint2->id,
        'assigned_by' => $kadis->id,
        'assigned_to' => $petugas2->id,
        'instruction' => 'Bukan tugas petugas 1',
        'status' => 'aktif',
    ]);

    $action = app(GetPetugasDashboardDataAction::class);
    $data = $action->handle($petugas1);

    expect($data['statusCounts']['total'])->toBe(1)
        ->and($data['statusCounts']['ditindaklanjuti'])->toBe(1);
});

test('6. kabag tidak melihat aduan dari bidang lain pada dashboard', function () {
    $kabagLinjamsos = User::factory()->create([
        'role' => 'kabag',
        'bidang' => 'perlindungan_jaminan_sosial',
    ]);

    $myComplaint = Complaint::factory()->create([
        'ticket_code' => 'JS-MY-BIDANG-01',
        'category_id' => $this->catLinjamsos->id,
        'status' => 'diteruskan',
    ]);

    $otherComplaint = Complaint::factory()->create([
        'ticket_code' => 'JS-OTHER-BIDANG-02',
        'category_id' => $this->catRehsos->id,
        'status' => 'diteruskan',
    ]);

    $this->actingAs($kabagLinjamsos)
        ->get(route('petugas.dashboard'))
        ->assertOk()
        ->assertSee('JS-MY-BIDANG-01')
        ->assertDontSee('JS-OTHER-BIDANG-02');
});

test('7. petugas tidak melihat aduan milik petugas lain pada dashboard', function () {
    $kadis = User::factory()->create(['role' => 'kadis']);
    $petugas1 = User::factory()->create(['role' => 'petugas']);
    $petugas2 = User::factory()->create(['role' => 'petugas']);

    $complaint1 = Complaint::factory()->create([
        'ticket_code' => 'JS-PETUGAS-1-TKT',
        'category_id' => $this->catLinjamsos->id,
        'status' => 'didisposisikan',
    ]);
    Disposition::create([
        'complaint_id' => $complaint1->id,
        'assigned_by' => $kadis->id,
        'assigned_to' => $petugas1->id,
        'instruction' => 'Tugas 1',
        'status' => 'aktif',
    ]);

    $complaint2 = Complaint::factory()->create([
        'ticket_code' => 'JS-PETUGAS-2-TKT',
        'category_id' => $this->catLinjamsos->id,
        'status' => 'didisposisikan',
    ]);
    Disposition::create([
        'complaint_id' => $complaint2->id,
        'assigned_by' => $kadis->id,
        'assigned_to' => $petugas2->id,
        'instruction' => 'Tugas 2',
        'status' => 'aktif',
    ]);

    $this->actingAs($petugas1)
        ->get(route('pelaksana.dashboard'))
        ->assertOk()
        ->assertSee('JS-PETUGAS-1-TKT')
        ->assertDontSee('JS-PETUGAS-2-TKT');
});

test('8. aduan yang melewati batas waktu ditandai sebagai terlambat', function () {
    $kadis = User::factory()->create(['role' => 'kadis']);
    $petugas = User::factory()->create(['role' => 'petugas']);

    $complaint = Complaint::factory()->create([
        'ticket_code' => 'JS-OVERDUE-01',
        'category_id' => $this->catLinjamsos->id,
        'status' => 'didisposisikan',
    ]);

    Disposition::create([
        'complaint_id' => $complaint->id,
        'assigned_by' => $kadis->id,
        'assigned_to' => $petugas->id,
        'instruction' => 'Lakukan sekarang',
        'due_date' => now()->subDays(3)->toDateString(), // sudah lewat
        'status' => 'aktif',
    ]);

    expect($complaint->fresh()->is_overdue)->toBeTrue();

    $this->actingAs($petugas)
        ->get(route('pelaksana.dashboard'))
        ->assertOk()
        ->assertSee('Terlambat');
});

test('9. aduan selesai dan ditolak tidak dianggap terlambat', function () {
    $kadis = User::factory()->create(['role' => 'kadis']);
    $petugas = User::factory()->create(['role' => 'petugas']);

    $complaintSelesai = Complaint::factory()->create([
        'category_id' => $this->catLinjamsos->id,
        'status' => 'selesai',
        'completed_at' => now(),
    ]);

    Disposition::create([
        'complaint_id' => $complaintSelesai->id,
        'assigned_by' => $kadis->id,
        'assigned_to' => $petugas->id,
        'instruction' => 'Lakukan',
        'due_date' => now()->subDays(5)->toDateString(),
        'status' => 'selesai',
    ]);

    $complaintDitolak = Complaint::factory()->create([
        'category_id' => $this->catLinjamsos->id,
        'status' => 'ditolak',
        'completed_at' => now(),
    ]);

    Disposition::create([
        'complaint_id' => $complaintDitolak->id,
        'assigned_by' => $kadis->id,
        'assigned_to' => $petugas->id,
        'instruction' => 'Lakukan',
        'due_date' => now()->subDays(5)->toDateString(),
        'status' => 'selesai',
    ]);

    expect($complaintSelesai->fresh()->is_overdue)->toBeFalse()
        ->and($complaintDitolak->fresh()->is_overdue)->toBeFalse();
});

test('10. dashboard menampilkan aduan terbaru sesuai hak akses', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);
    $kabag = User::factory()->create(['role' => 'kabag', 'bidang' => 'perlindungan_jaminan_sosial']);

    $c1 = Complaint::factory()->create([
        'ticket_code' => 'JS-REC-01',
        'category_id' => $this->catLinjamsos->id,
    ]);
    $c2 = Complaint::factory()->create([
        'ticket_code' => 'JS-REC-02',
        'category_id' => $this->catRehsos->id,
    ]);

    // Super admin melihat kedua aduan terbaru
    $this->actingAs($superAdmin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('JS-REC-01')
        ->assertSee('JS-REC-02');

    // Kabag Linjamsos hanya melihat aduan di bidangnya
    $this->actingAs($kabag)
        ->get(route('petugas.dashboard'))
        ->assertOk()
        ->assertSee('JS-REC-01')
        ->assertDontSee('JS-REC-02');
});

