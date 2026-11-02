<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users are redirected from generic dashboard to their role dashboard', function () {
    $user = User::factory()->petugasPenerima()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('petugas.dashboard'));
});

test('authenticated users can visit their role dashboard', function () {
    $user = User::factory()->petugasPenerima()->create();
    $this->actingAs($user);

    $response = $this->get(route('petugas.dashboard'));
    $response->assertOk();
});
