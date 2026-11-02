<?php

use App\Livewire\Admin\Category\CategoryFormModal;
use App\Livewire\Admin\Category\CategoryIndex;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Livewire\Livewire;

test('super_admin dapat melihat daftar kategori', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $category = Category::factory()->create([
        'name' => 'Bantuan Sosial Pangan',
        'code' => 'BSP',
    ]);

    $response = $this->actingAs($superAdmin)->get(route('admin.categories.index'));

    $response->assertOk();

    Livewire::actingAs($superAdmin)
        ->test(CategoryIndex::class)
        ->assertSee('Manajemen Kategori')
        ->assertSee('Bantuan Sosial Pangan')
        ->assertSee('BSP');
});

test('admin dapat melihat daftar kategori', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create([
        'name' => 'Program Keluarga Harapan',
        'code' => 'PKH',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.categories.index'));

    $response->assertOk();

    Livewire::actingAs($admin)
        ->test(CategoryIndex::class)
        ->assertSee('Manajemen Kategori')
        ->assertSee('Program Keluarga Harapan')
        ->assertSee('PKH');
});

test('super_admin dapat menambah kategori', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    Livewire::actingAs($superAdmin)
        ->test(CategoryFormModal::class)
        ->call('create')
        ->set('form.name', 'Asistensi Rehabilitasi Sosial')
        ->set('form.code', 'atensi')
        ->set('form.description', 'Layanan rehabilitasi sosial bagi kelompok rentan')
        ->set('form.is_active', true)
        ->call('save')
        ->assertDispatched('category-saved')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('categories', [
        'name' => 'Asistensi Rehabilitasi Sosial',
        'code' => 'ATENSI', // Otomatis kapital
        'is_active' => true,
    ]);
});

test('admin dapat menambah kategori', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(CategoryFormModal::class)
        ->call('create')
        ->set('form.name', 'Bantuan Sosial Tunai')
        ->set('form.code', 'bst')
        ->set('form.description', 'Bantuan langsung tunai dampak inflasi')
        ->set('form.is_active', true)
        ->call('save')
        ->assertDispatched('category-saved')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('categories', [
        'name' => 'Bantuan Sosial Tunai',
        'code' => 'BST', // Otomatis kapital
        'is_active' => true,
    ]);
});

test('super_admin dapat mengedit kategori', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $category = Category::factory()->create([
        'name' => 'Nama Lama',
        'code' => 'LAMA',
        'description' => 'Deskripsi lama',
        'is_active' => false,
    ]);

    Livewire::actingAs($superAdmin)
        ->test(CategoryFormModal::class)
        ->call('edit', $category->id)
        ->assertSet('form.name', 'Nama Lama')
        ->assertSet('form.code', 'LAMA')
        ->set('form.name', 'Nama Baru Super Admin')
        ->set('form.code', 'baru')
        ->set('form.is_active', true)
        ->call('save')
        ->assertDispatched('category-saved')
        ->assertHasNoErrors();

    $category->refresh();
    expect($category->name)->toBe('Nama Baru Super Admin')
        ->and($category->code)->toBe('BARU')
        ->and($category->is_active)->toBeTrue();
});

test('admin dapat mengedit kategori', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create([
        'name' => 'Kategori Edit Admin',
        'code' => 'KEA',
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test(CategoryFormModal::class)
        ->call('edit', $category->id)
        ->set('form.name', 'Kategori Berhasil Diedit')
        ->set('form.code', 'kbe')
        ->call('save')
        ->assertDispatched('category-saved')
        ->assertHasNoErrors();

    $category->refresh();
    expect($category->name)->toBe('Kategori Berhasil Diedit')
        ->and($category->code)->toBe('KBE');
});

test('super_admin dapat menghapus kategori', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $category = Category::factory()->create([
        'name' => 'Kategori Dihapus Super Admin',
        'code' => 'DEL1',
    ]);

    Livewire::actingAs($superAdmin)
        ->test(CategoryIndex::class)
        ->call('confirmDelete', $category->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('deletingCategoryId', $category->id)
        ->call('deleteCategory');

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});

test('admin dapat menghapus kategori', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create([
        'name' => 'Kategori Dihapus Admin',
        'code' => 'DEL2',
    ]);

    Livewire::actingAs($admin)
        ->test(CategoryIndex::class)
        ->call('delete', $category->id);

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});

test('kadis, kabag, dan petugas tidak dapat mengakses halaman kategori', function () {
    // Testing shorthand role names
    $kadis = User::factory()->kadis()->create();
    $kabag = User::factory()->kabag()->create();
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($kadis)->get(route('admin.categories.index'))->assertForbidden();
    $this->actingAs($kabag)->get(route('admin.categories.index'))->assertForbidden();
    $this->actingAs($petugas)->get(route('admin.categories.index'))->assertForbidden();

    // Testing legacy role names
    $kepalaDinas = User::factory()->kepalaDinas()->create();
    $petugasPenerima = User::factory()->petugasPenerima()->create();
    $petugasPelaksana = User::factory()->petugasPelaksana()->create();

    $this->actingAs($kepalaDinas)->get(route('admin.categories.index'))->assertForbidden();
    $this->actingAs($petugasPenerima)->get(route('admin.categories.index'))->assertForbidden();
    $this->actingAs($petugasPelaksana)->get(route('admin.categories.index'))->assertForbidden();
});

test('validasi kode kategori duplikat gagal', function () {
    $admin = User::factory()->admin()->create();
    Category::factory()->create(['code' => 'EXISTING']);

    Livewire::actingAs($admin)
        ->test(CategoryFormModal::class)
        ->call('create')
        ->set('form.name', 'Kategori Duplikat')
        ->set('form.code', 'existing')
        ->call('save')
        ->assertHasErrors(['form.code']);
});

test('kategori yang masih digunakan aduan tidak dapat dihapus', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create(['name' => 'Kategori Beraduan']);

    // Hubungkan aduan ke kategori ini
    Complaint::factory()->create([
        'category_id' => $category->id,
    ]);

    Livewire::actingAs($admin)
        ->test(CategoryIndex::class)
        ->call('delete', $category->id)
        ->assertSee('Kategori tidak dapat dihapus karena masih memiliki aduan terkait.');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
    ]);
});

test('pencarian dan filter status berfungsi pada daftar kategori', function () {
    $admin = User::factory()->admin()->create();
    Category::factory()->create(['name' => 'Disabilitas Fisik', 'code' => 'DIS-F', 'is_active' => true]);
    Category::factory()->create(['name' => 'Lanjut Usia Terlantar', 'code' => 'LUT', 'is_active' => false]);

    Livewire::actingAs($admin)
        ->test(CategoryIndex::class)
        ->set('search', 'Disabilitas')
        ->assertSee('Disabilitas Fisik')
        ->assertDontSee('Lanjut Usia Terlantar')
        ->set('search', '')
        ->set('statusFilter', 'inactive')
        ->assertSee('Lanjut Usia Terlantar')
        ->assertDontSee('Disabilitas Fisik');
});

test('sidebar menampilkan menu kategori hanya untuk admin dan menyembunyikannya untuk role lain', function () {
    $admin = User::factory()->admin()->create();
    $petugas = User::factory()->petugas()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee(route('admin.categories.index'));

    $this->actingAs($petugas)
        ->get(route('pelaksana.dashboard'))
        ->assertOk()
        ->assertDontSee(route('admin.categories.index'));
});
