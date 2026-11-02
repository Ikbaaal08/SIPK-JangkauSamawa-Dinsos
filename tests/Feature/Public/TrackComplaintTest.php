<?php

use App\Livewire\Public\TrackComplaint;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Models\User;
use App\Services\FeatureService;
use Livewire\Livewire;

beforeEach(function () {
    // Pastikan flag public_tracking aktif secara default pada test suite ini
    app(FeatureService::class)->enable('public_tracking');
});

test('public_tracking OFF memblokir halaman lacak dan mengarahkan ke login', function () {
    app(FeatureService::class)->disable('public_tracking');

    $response = $this->get(route('public.tracking.index'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status', 'Fitur pelacakan aduan sedang dinonaktifkan.');
});

test('public_tracking ON mengizinkan halaman lacak', function () {
    app(FeatureService::class)->enable('public_tracking');

    $response = $this->get(route('public.tracking.index'));

    $response->assertOk();
    $response->assertSee('Lacak Aduan Jangkau Samawa');
    $response->assertSee('Masukkan kode tiket aduan Anda untuk melihat status penanganan.');
});

test('halaman lacak dapat diakses tanpa login', function () {
    $this->assertGuest();

    $response = $this->get('/lacak');

    $response->assertOk();
    $response->assertSee('Lacak Aduan Jangkau Samawa');
    $response->assertSee('Kode tiket dikirim melalui WhatsApp');
    $response->assertSee('Lacak Aduan');
});

test('kode tiket kosong menampilkan validasi kode tiket wajib diisi', function () {
    Livewire::test(TrackComplaint::class)
        ->set('form.ticket_code', '')
        ->call('track')
        ->assertHasErrors(['form.ticket_code' => 'required'])
        ->assertSee('Kode tiket wajib diisi.');
});

test('kode tiket tidak ditemukan menampilkan pesan error', function () {
    Livewire::test(TrackComplaint::class)
        ->set('form.ticket_code', 'TKT-99999999')
        ->call('track')
        ->assertHasNoErrors()
        ->assertSee('Aduan dengan kode tiket tersebut tidak ditemukan.');
});

test('kode tiket valid menampilkan status dan riwayat', function () {
    $category = Category::factory()->create([
        'name' => 'Bantuan Sosial',
        'code' => 'BANSOS',
    ]);

    $complaint = Complaint::factory()->create([
        'ticket_code' => 'JS-2026-TEST1',
        'category_id' => $category->id,
        'title' => 'Bantuan sembako lansia tertunda',
        'status' => 'diproses',
        'reporter_phone' => '6281234567890',
    ]);

    $officer = User::factory()->create(['name' => 'Petugas Rahasia Dinsos']);

    ComplaintHistory::factory()->create([
        'complaint_id' => $complaint->id,
        'user_id' => $officer->id,
        'previous_status' => 'baru',
        'new_status' => 'diproses',
        'note' => 'Catatan verifikasi internal rahasia petugas',
    ]);

    Livewire::test(TrackComplaint::class)
        ->set('form.ticket_code', 'JS-2026-TEST1')
        ->call('track')
        ->assertHasNoErrors()
        ->assertSee('JS-2026-TEST1')
        ->assertSee('Bantuan sembako lansia tertunda')
        ->assertSee('Sedang Diproses')
        ->assertSee('Bantuan Sosial')
        ->assertSee('Bidang Perlindungan dan Jaminan Sosial (Linjamsos)')
        ->assertSee('Riwayat Perubahan Status');
});

test('data pribadi sensitif tidak ditampilkan pada halaman pelacakan', function () {
    $sensitiveAddress = 'Jalan Melati No. 99 RT 05 RW 02 Sumbawa Besar';
    $sensitivePhone = '6281234567890';
    $sensitiveOfficerName = 'Bambang Tri Atmojo Staf Dinsos';
    $sensitiveInternalNote = 'CATATAN_INTERNAL_RAHASIA_JANGAN_BOCOR_98765';

    $complaint = Complaint::factory()->create([
        'ticket_code' => 'JS-2026-PRIVACY',
        'reporter_name' => 'Warga Pelapor',
        'reporter_phone' => $sensitivePhone,
        'reporter_address' => $sensitiveAddress,
        'title' => 'Permohonan bantuan kursi roda',
        'status' => 'didisposisikan',
    ]);

    $officer = User::factory()->create(['name' => $sensitiveOfficerName]);

    ComplaintHistory::factory()->create([
        'complaint_id' => $complaint->id,
        'user_id' => $officer->id,
        'previous_status' => 'diteruskan',
        'new_status' => 'didisposisikan',
        'note' => $sensitiveInternalNote,
    ]);

    $component = Livewire::test(TrackComplaint::class)
        ->set('form.ticket_code', 'JS-2026-PRIVACY')
        ->call('track');

    // Pastikan nomor disamarkan (misalnya 62812****890)
    $component->assertSee('62812****890');

    // Pastikan data sensitif berikut TIDAK muncul sama sekali:
    $component->assertDontSee($sensitivePhone);
    $component->assertDontSee($sensitiveAddress);
    $component->assertDontSee($sensitiveOfficerName);
    $component->assertDontSee($sensitiveInternalNote);
});
