<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

test('admin is redirected to admin dashboard upon login', function () {
    $user = User::factory()->admin()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('petugas penerima is redirected to petugas dashboard upon login', function () {
    $user = User::factory()->petugasPenerima()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('petugas.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('kepala dinas is redirected to kepala dinas dashboard upon login', function () {
    $user = User::factory()->kepalaDinas()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('kepala-dinas.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('petugas pelaksana is redirected to pelaksana dashboard upon login', function () {
    $user = User::factory()->petugasPelaksana()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('pelaksana.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('user can logout successfully and is redirected to home', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('home'));
    $this->assertGuest();
});

test('unverified internal users are redirected to whatsapp verification upon login', function () {
    Http::fake();

    $roles = [
        'admin',
        'petugas_penerima',
        'kepala_dinas',
        'petugas_pelaksana',
    ];

    foreach ($roles as $role) {
        $user = User::factory()->unverified()->create([
            'role' => $role,
            'phone' => '081234567890',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('verification.whatsapp'));

        auth()->logout();
    }
});
