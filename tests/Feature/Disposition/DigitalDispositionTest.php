<?php

use App\Actions\Disposition\CreateDispositionAction;
use App\DTOs\Disposition\CreateDispositionData;
use App\Livewire\KepalaDinas\DispositionIndex as KadisDispositionIndex;
use App\Livewire\Petugas\DispositionIndex as KabagDispositionIndex;
use App\Livewire\Petugas\PelaksanaDispositionIndex;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Disposition;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

test('kepala dinas dapat memberikan disposisi tingkat 1 kepada kabag', function () {
    $kadis = User::factory()->kepalaDinas()->create();
    $kabag = User::factory()->petugasPenerima()->create(['name' => 'Budi Kabag']);
    $category = Category::factory()->create();
    $complaint = Complaint::factory()->create([
        'category_id' => $category->id,
        'status' => 'diteruskan',
    ]);

    Livewire::actingAs($kadis)
        ->test(KadisDispositionIndex::class)
        ->call('openDispositionModal', $complaint->id)
        ->set('form.assigned_to', $kabag->id)
        ->set('form.instruction', 'Koordinasikan dengan bidang penanganan bencana dan siapkan logistik')
        ->set('form.due_date', now()->addDays(3)->toDateString())
        ->call('submitDisposition')
        ->assertHasNoErrors();

    // Pastikan record disposisi terbentuk
    $this->assertDatabaseHas('dispositions', [
        'complaint_id' => $complaint->id,
        'assigned_by' => $kadis->id,
        'assigned_to' => $kabag->id,
        'instruction' => 'Koordinasikan dengan bidang penanganan bencana dan siapkan logistik',
        'status' => 'aktif',
    ]);

    // Status aduan berubah menjadi didisposisikan
    $complaint->refresh();
    expect($complaint->status)->toBe('didisposisikan');

    // Riwayat status tercatat di complaint_histories
    $this->assertDatabaseHas('complaint_histories', [
        'complaint_id' => $complaint->id,
        'user_id' => $kadis->id,
        'previous_status' => 'diteruskan',
        'new_status' => 'didisposisikan',
    ]);
});

test('kabag dapat memberikan disposisi lanjutan tingkat 2 kepada petugas pelaksana', function () {
    $kadis = User::factory()->kepalaDinas()->create();
    $kabag = User::factory()->petugasPenerima()->create();
    $petugas = User::factory()->petugasPelaksana()->create(['name' => 'Andi Pelaksana']);
    $category = Category::factory()->create();
    $complaint = Complaint::factory()->create([
        'category_id' => $category->id,
        'status' => 'didisposisikan',
    ]);

    // Disposisi tingkat 1 dari Kadis ke Kabag
    Disposition::factory()->create([
        'complaint_id' => $complaint->id,
        'assigned_by' => $kadis->id,
        'assigned_to' => $kabag->id,
        'instruction' => 'Tindak lanjuti segera',
    ]);

    Livewire::actingAs($kabag)
        ->test(KabagDispositionIndex::class)
        ->call('openForwardModal', $complaint->id)
        ->set('form.assigned_to', $petugas->id)
        ->set('form.instruction', 'Segera cek lokasi rumah pelapor dan kirimkan dokumentasi')
        ->set('form.due_date', now()->addDays(2)->toDateString())
        ->call('submitForwardDisposition')
        ->assertHasNoErrors();

    // Pastikan disposisi berjenjang kedua terbentuk
    $this->assertDatabaseHas('dispositions', [
        'complaint_id' => $complaint->id,
        'assigned_by' => $kabag->id,
        'assigned_to' => $petugas->id,
        'instruction' => 'Segera cek lokasi rumah pelapor dan kirimkan dokumentasi',
        'status' => 'aktif',
    ]);

    // Riwayat status tercatat untuk disposisi lanjutan
    $this->assertDatabaseHas('complaint_histories', [
        'complaint_id' => $complaint->id,
        'user_id' => $kabag->id,
        'new_status' => 'didisposisikan',
    ]);
});

test('validasi hierarki disposisi menolak tujuan yang tidak sesuai tingkatannya', function () {
    $action = app(CreateDispositionAction::class);

    $kadis = User::factory()->kepalaDinas()->create();
    $petugas = User::factory()->petugasPelaksana()->create();
    $complaint = Complaint::factory()->create();

    // Kadis tidak boleh langsung ke Pelaksana (harus ke Kabag terlebih dahulu)
    expect(function () use ($action, $complaint, $kadis, $petugas) {
        $action->handle(new CreateDispositionData(
            complaint: $complaint,
            assignedBy: $kadis,
            assignedToId: $petugas->id,
            instruction: 'Instruksi tidak sesuai hierarki',
        ));
    })->toThrow(ValidationException::class);
});

test('petugas pelaksana hanya dapat melihat aduan yang didisposisikan kepadanya', function () {
    $kabag = User::factory()->petugasPenerima()->create();
    $petugasA = User::factory()->petugasPelaksana()->create(['name' => 'Petugas A']);
    $petugasB = User::factory()->petugasPelaksana()->create(['name' => 'Petugas B']);

    $complaintA = Complaint::factory()->create(['title' => 'Aduan Khusus Petugas A']);
    $complaintB = Complaint::factory()->create(['title' => 'Aduan Khusus Petugas B']);

    Disposition::factory()->create([
        'complaint_id' => $complaintA->id,
        'assigned_by' => $kabag->id,
        'assigned_to' => $petugasA->id,
    ]);

    Disposition::factory()->create([
        'complaint_id' => $complaintB->id,
        'assigned_by' => $kabag->id,
        'assigned_to' => $petugasB->id,
    ]);

    // Petugas A hanya melihat Aduan A
    Livewire::actingAs($petugasA)
        ->test(PelaksanaDispositionIndex::class)
        ->assertSee('Aduan Khusus Petugas A')
        ->assertDontSee('Aduan Khusus Petugas B');

    // Petugas B hanya melihat Aduan B
    Livewire::actingAs($petugasB)
        ->test(PelaksanaDispositionIndex::class)
        ->assertSee('Aduan Khusus Petugas B')
        ->assertDontSee('Aduan Khusus Petugas A');
});

test('petugas pelaksana dapat menindaklanjuti disposisi dan mengubah status aduan', function () {
    $kabag = User::factory()->petugasPenerima()->create();
    $petugas = User::factory()->petugasPelaksana()->create();
    $complaint = Complaint::factory()->create(['status' => 'didisposisikan']);

    $disposition = Disposition::factory()->create([
        'complaint_id' => $complaint->id,
        'assigned_by' => $kabag->id,
        'assigned_to' => $petugas->id,
        'status' => 'aktif',
    ]);

    Livewire::actingAs($petugas)
        ->test(PelaksanaDispositionIndex::class)
        ->call('openActionModal', $disposition->id, 'tindaklanjuti')
        ->set('actionNote', 'Tim relawan tiba di lokasi desa pelapor')
        ->call('submitAction')
        ->assertHasNoErrors();

    $complaint->refresh();
    expect($complaint->status)->toBe('ditindaklanjuti');

    $this->assertDatabaseHas('complaint_histories', [
        'complaint_id' => $complaint->id,
        'user_id' => $petugas->id,
        'previous_status' => 'didisposisikan',
        'new_status' => 'ditindaklanjuti',
        'note' => 'Tim relawan tiba di lokasi desa pelapor',
    ]);
});

test('petugas pelaksana dapat menyelesaikan disposisi yang otomatis mengisi completed_at', function () {
    $kabag = User::factory()->petugasPenerima()->create();
    $petugas = User::factory()->petugasPelaksana()->create();
    $complaint = Complaint::factory()->create([
        'status' => 'ditindaklanjuti',
        'completed_at' => null,
    ]);

    $disposition = Disposition::factory()->create([
        'complaint_id' => $complaint->id,
        'assigned_by' => $kabag->id,
        'assigned_to' => $petugas->id,
        'status' => 'aktif',
    ]);

    Livewire::actingAs($petugas)
        ->test(PelaksanaDispositionIndex::class)
        ->call('openActionModal', $disposition->id, 'selesai')
        ->set('actionNote', 'Bantuan sosial tunai telah disalurkan dan tuntas diterima')
        ->call('submitAction')
        ->assertHasNoErrors();

    $disposition->refresh();
    $complaint->refresh();

    // Disposisi dan aduan menjadi selesai
    expect($disposition->status)->toBe('selesai')
        ->and($complaint->status)->toBe('selesai')
        ->and($complaint->completed_at)->not->toBeNull();

    $this->assertDatabaseHas('complaint_histories', [
        'complaint_id' => $complaint->id,
        'user_id' => $petugas->id,
        'previous_status' => 'ditindaklanjuti',
        'new_status' => 'selesai',
        'note' => 'Bantuan sosial tunai telah disalurkan dan tuntas diterima',
    ]);
});

test('pembatasan hak akses route disposisi berdasarkan role', function () {
    $kadis = User::factory()->kepalaDinas()->create();
    $kabag = User::factory()->petugasPenerima()->create();
    $petugas = User::factory()->petugasPelaksana()->create();

    // Kadis hanya boleh akses route disposisi kadis
    $this->actingAs($kadis)->get(route('kepala-dinas.dispositions.index'))->assertOk();
    $this->actingAs($kadis)->get(route('petugas.dispositions.index'))->assertForbidden();
    $this->actingAs($kadis)->get(route('pelaksana.dispositions.index'))->assertForbidden();

    // Kabag hanya boleh akses route disposisi kabag
    $this->actingAs($kabag)->get(route('petugas.dispositions.index'))->assertOk();
    $this->actingAs($kabag)->get(route('kepala-dinas.dispositions.index'))->assertForbidden();
    $this->actingAs($kabag)->get(route('pelaksana.dispositions.index'))->assertForbidden();

    // Pelaksana hanya boleh akses route disposisi pelaksana
    $this->actingAs($petugas)->get(route('pelaksana.dispositions.index'))->assertOk();
    $this->actingAs($petugas)->get(route('kepala-dinas.dispositions.index'))->assertForbidden();
    $this->actingAs($petugas)->get(route('petugas.dispositions.index'))->assertForbidden();
});
