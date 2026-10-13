<?php

use function Pest\Laravel\get;

test('unauthenticated root request redirects to login', function () {
    $response = get('/');

    $response->assertRedirect('/login');
});
