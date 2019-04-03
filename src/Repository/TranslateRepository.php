<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Translation;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\Logger;
use Predis\Client as RedisClient;
use Predis\Response\Status;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tightenco\Collect\Support\Collection;

final class TranslateRepository
{
    const TRANSLATION_DIVIDER = ':';

    /**
     * Configuration settings.
     *
     * @var array
     */
    private $settings;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var RedisClient
     */
    protected $cache;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * TranslateRepository constructor.
     *
     * @param ContainerInterface $container
     * @param Client             $httpClient
     * @param array              $settings
     * @param Logger             $logger
     */
    public function __construct(ContainerInterface $container, Client $httpClient, array $settings, Logger $logger)
    {
        $this->cache = $container->get('cache');
        $this->httpClient = $httpClient;

        $this->settings = $settings;
        $this->logger = $logger;
    }

    /**
     * Edit translation.
     *
     * @param string $sourceText
     * @param string $sourceLang
     * @param string $targetLang
     * @param string $targetText
     *
     * @return Translation|bool
     */
    public function edit(string $sourceText, string $sourceLang, string $targetLang, string $targetText)
    {
        $translation = new Translation();
        $translation->setSourceLanguage($sourceLang)
                    ->setSourceText($sourceText)
                    ->setTargetLanguage($targetLang)
                    ->setTargetText($targetText);

        $saved = $this->saveToCache($translation);
        if ($saved) {
            return $translation;
        }

        return false;
    }

    /**
     * Delete translation.
     *
     * @param string $sourceText
     * @param string $sourceLang
     * @param string $targetLang
     *
     * @return bool
     */
    public function delete(string $sourceText, string $sourceLang, string $targetLang): bool
    {
        return (bool) $this->cache->del([
            $sourceLang
            .self::TRANSLATION_DIVIDER
            .$sourceText
            .self::TRANSLATION_DIVIDER
            .$targetLang,
        ]);
    }

    /**
     * Get all translations from cache.
     *
     * @return Collection
     */
    public function getAllTranslations(): Collection
    {
        $allKeys = $this->cache->keys('*');
        $translationCollection = (new Collection($allKeys))->map(function ($item) {
            return $this->getFromCache($item);
        });

        return $translationCollection;
    }

    /**
     * Translate a text/string.
     *
     * @param string $text
     * @param string $targetLang
     * @param string $sourceLang
     *
     * @return Translation
     */
    public function translate(string $text, string $targetLang, string $sourceLang = ''): Translation
    {
        $translation = new Translation();

        try {
            if ($this->existsInCache($text, $sourceLang, $targetLang)) {
                return $this->getFromCache($sourceLang
                                           .self::TRANSLATION_DIVIDER
                                           .$text
                                           .self::TRANSLATION_DIVIDER
                                           .$targetLang);
            }

            // Process DeepL response
            $response = $this->deeplTranslate($targetLang, $text, $sourceLang);
            $content = json_decode($response->getBody()->getContents());
            $targetText = $content->translations[0]->text;
            $sourceLang = $content->translations[0]->detected_source_language;

            $translation->setSourceText($text)
                        ->setSourceLanguage($sourceLang)
                        ->setTargetLanguage($targetLang)
                        ->setTargetText($targetText)
                        ->setLoadedFromCache(false);

            // Save item to cache
            $this->saveToCache($translation);

            return $translation;
        } catch (GuzzleException $e) {
            $this->logger->warning($e->getMessage());
        }

        return $translation;
    }

    /**
     * Get translation from DeepL.
     *
     * @param string $targetLang
     * @param string $text
     * @param string $sourceLang
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return ResponseInterface
     */
    protected function deeplTranslate(string $targetLang, string $text, string $sourceLang = ''): ResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            '/'.$this->settings['api_version'].'/translate',
            [
            'query' => [
                'auth_key'            => $this->settings['api_token'],
                'preserve_formatting' => 1,
                'text'                => $text,
                'source_lang'         => $sourceLang,
                'target_lang'         => $targetLang,
            ],
            ]
        );
    }

    /**
     * Check if item exists in cache.
     *
     * @param string $text
     * @param string $sourceLanguage
     * @param string $targetLanguage
     *
     * @return bool
     */
    private function existsInCache(string $text, string $sourceLanguage, string $targetLanguage): bool
    {
        return (bool) $this->cache->exists(
            $sourceLanguage
            .self::TRANSLATION_DIVIDER
            .$text
            .self::TRANSLATION_DIVIDER
            .$targetLanguage
        );
    }

    /**
     * Save item to cache.
     *
     * @param Translation $translation
     *
     * @return bool
     */
    private function saveToCache(Translation $translation): bool
    {
        $key = $translation->getSourceLanguage()
               .self::TRANSLATION_DIVIDER
               .$translation->getSourceText()
               .self::TRANSLATION_DIVIDER
               .$translation->getTargetLanguage();

        /** @var Status $response */
        $response = $this->cache->set($key, $translation->getTargetText());

        return $response->getPayload() === 'OK' ? true : false;
    }

    /**
     * Get translation from cache.
     *
     * @param string $key
     *
     * @return Translation
     */
    private function getFromCache(string $key): Translation
    {
        $segments = preg_split('/'.self::TRANSLATION_DIVIDER.'/', $key);
        $sourceLang = $segments[0];
        $sourceText = $segments[1];
        $targetLang = $segments[2];
        $targetText = $this->cache->get($key);

        $translation = new Translation();
        $translation->setSourceLanguage($sourceLang)
                    ->setSourceText($sourceText)
                    ->setTargetLanguage($targetLang)
                    ->setTargetText($targetText)
                    ->setLoadedFromCache(true);

        return $translation;
    }
}
