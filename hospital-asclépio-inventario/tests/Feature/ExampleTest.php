<?php

test('unauthenticated root request redirects to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
