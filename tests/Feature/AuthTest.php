<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\BaseTestCase;

final class AuthTest extends BaseTestCase
{
    public function test_auth_validation_error()
    {
        $response = $this->request('POST', '/auth/token');
        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Validation Error', $body['status']);
    }

    public function test_auth_unauthorized()
    {
        $response = $this->request('POST', '/auth/token', [
            'email'    => 'john.doe@example.com',
            'password' => 'test123',
        ]);
        $body = json_decode((string) $response->getBody(), true);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Authentication error', $body['status']);
    }
}
