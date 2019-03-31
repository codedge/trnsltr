<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Repository\TranslateRepository;
use Predis\Client;
use Tests\BaseTestCase;

class TranslateTest extends BaseTestCase
{
    public function test_translate_success()
    {
        $response = $this->request('GET', '/api/v1/translate/text/hello/target-lang/de/source-lang/en');
        $body = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $body['status']);
        $this->assertIsBool($body['loaded_from_cache']);
    }

    public function test_translate_validation_error()
    {
        $response = $this->request('GET', '/api/v1/translate/text/hello/target-lang/e');
        $body = json_decode((string)$response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Validation Error', $body['status']);
        $this->assertIsBool($body['loaded_from_cache']);
    }

    public function test_all_translations()
    {
        $response = $this->request('GET', '/api/v1/translation');
        $body = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $body['status']);
    }

    public function test_edit_translation_success()
    {
        $response = $this->request('PATCH', '/api/v1/translation', [
            'source_text' => 'short',
            'source_lang' => 'en',
            'target_text' => 'kurz',
            'target_lang' => 'de',
        ]);
        $body = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $body['status']);
    }

    public function test_edit_translation_validation_error()
    {
        $response = $this->request('PATCH', '/api/v1/translation', [
            'source_text' => '',
            'source_lang' => '',
            'target_text' => 'kurz',
            'target_lang' => 'de',
        ]);
        $body = json_decode((string)$response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Validation Error', $body['status']);
        $this->assertEquals($body['loaded_from_cache'], false);
        $this->assertIsBool($body['loaded_from_cache']);
    }

    public function test_delete_translation_success()
    {
        $payload = [
            'source_text' => 'bag',
            'source_lang' => 'en',
            'target_lang' => 'de',
        ];

        /** @var Client $cache */
        $cache = $this->app->getContainer()['cache'];

        $cache->set(
            $payload['source_lang']
            . TranslateRepository::TRANSLATION_DIVIDER
            . $payload['source_text']
            . TranslateRepository::TRANSLATION_DIVIDER
            . $payload['target_lang']
            ,
            'Tasche'
        );

    
        $response = $this->request('DELETE', '/api/v1/translation', $payload);
        $body = json_decode((string)$response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $body['status']);
    }

    public function test_delete_translation_validation_error()
    {
        $response = $this->request('DELETE', '/api/v1/translation', [
            'source_text' => 'bag',
            'source_lang' => 'en',
            'target_lang' => 'de',
        ]);
        $body = json_decode((string)$response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Validation Error', $body['status']);
    }

}
