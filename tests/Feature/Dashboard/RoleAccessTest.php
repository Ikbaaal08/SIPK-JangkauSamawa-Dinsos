<?php

use App\Models\User;

test('guests are redirected to login when attempting to access role dashboards', function () {
    $routes = [
        route('admin.dashboard'),
        route('petugas.dashboard'),
        route('kepala-dinas.dashboard'),
        route('pelaksana.dashboard'),
        route('dashboard'),
    ];

    foreach ($routes as $url) {
        $response = $this->get($url);
        $response->assertRedirect(route('login'));
    }
});

test('admin can access admin dashboard but cannot access other role dashboards', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Administrator');

    $this->actingAs($admin)
        ->get(route('petugas.dashboard'))
        ->assertForbidden();

    $this->actingAs($admin)
        ->get(route('kepala-dinas.dashboard'))
        ->assertForbidden();

    $this->actingAs($admin)
        ->get(route('pelaksana.dashboard'))
        ->assertForbidden();
});

test('petugas penerima can access penerima dashboard but cannot access admin dashboard', function () {
    $penerima = User::factory()->petugasPenerima()->create();

    $this->actingAs($penerima)
        ->get(route('petugas.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Petugas Penerima');

    $this->actingAs($penerima)
        ->get(route('admin.dashboard'))
        ->assertForbidden();

    $this->actingAs($penerima)
        ->get(route('kepala-dinas.dashboard'))
        ->assertForbidden();

    $this->actingAs($penerima)
        ->get(route('pelaksana.dashboard'))
        ->assertForbidden();
});

test('kepala dinas can access kepala dinas dashboard but cannot access other role dashboards', function () {
    $kepala = User::factory()->kepalaDinas()->create();

    $this->actingAs($kepala)
        ->get(route('kepala-dinas.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Kepala Dinas Sosial');

    $this->actingAs($kepala)
        ->get(route('admin.dashboard'))
        ->assertForbidden();

    $this->actingAs($kepala)
        ->get(route('petugas.dashboard'))
        ->assertForbidden();

    $this->actingAs($kepala)
        ->get(route('pelaksana.dashboard'))
        ->assertForbidden();
});

test('petugas pelaksana can access pelaksana dashboard but cannot access other role dashboards', function () {
    $pelaksana = User::factory()->petugasPelaksana()->create();

    $this->actingAs($pelaksana)
        ->get(route('pelaksana.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard Petugas Pelaksana');

    $this->actingAs($pelaksana)
        ->get(route('admin.dashboard'))
        ->assertForbidden();

    $this->actingAs($pelaksana)
        ->get(route('petugas.dashboard'))
        ->assertForbidden();

    $this->actingAs($pelaksana)
        ->get(route('kepala-dinas.dashboard'))
        ->assertForbidden();
});

test('generic dashboard route redirects each role to their dedicated dashboard', function () {
    $admin = User::factory()->admin()->create();
    $penerima = User::factory()->petugasPenerima()->create();
    $kepala = User::factory()->kepalaDinas()->create();
    $pelaksana = User::factory()->petugasPelaksana()->create();

    $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    $this->actingAs($penerima)->get(route('dashboard'))->assertRedirect(route('petugas.dashboard'));
    $this->actingAs($kepala)->get(route('dashboard'))->assertRedirect(route('kepala-dinas.dashboard'));
    $this->actingAs($pelaksana)->get(route('dashboard'))->assertRedirect(route('pelaksana.dashboard'));
});
