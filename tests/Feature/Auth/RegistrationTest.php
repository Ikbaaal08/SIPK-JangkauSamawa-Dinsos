<?php

use Illuminate\Support\Facades\Http;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register and are redirected to whatsapp verification', function () {
    Http::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('verification.whatsapp'));

    $this->assertAuthenticated();
});
