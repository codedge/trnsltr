<?php declare(strict_types=1);

namespace App\Models;

final class Translation implements \JsonSerializable
{
    /**
     * The original text, that should be translated.
     *
     * @var string
     */
    protected $sourceText = '';

    /**
     * The language of the original (source) text.
     *
     * @var string
     */
    protected $sourceLanguage = '';

    /**
     * The language of the target (translated) text.
     *
     * @var string
     */
    protected $targetLanguage = '';

    /**
     * The translated version of the original (source) text.
     *
     * @var string
     */
    protected $targetText = '';

    /**
     * Indicate if translation comes from cache.
     *
     * @var bool
     */
    protected $loadedFromCache = false;

    /**
     * Set original text, that should be translated.
     *
     * @param string $text
     *
     * @return $this
     */
    public function setSourceText(string $text): Translation
    {
        $this->sourceText = $text;

        return $this;
    }

    /**
     * Get the source text.
     *
     * @return string
     */
    public function getSourceText(): string
    {
        return $this->sourceText;
    }

    /**
     * Set the translation, translated text.
     *
     * @param string $text
     *
     * @return Translation
     */
    public function setTargetText(string $text): Translation
    {
        $this->targetText = $text;

        return $this;
    }

    /**
     * Get the target (translated) text.
     *
     * @return string
     */
    public function getTargetText(): string
    {
        return $this->targetText;
    }

    /**
     * Set the language of the source text.
     *
     * @param string $language
     *
     * @return Translation
     */
    public function setSourceLanguage(string $language): Translation
    {
        $this->sourceLanguage = strtolower($language);

        return $this;
    }

    /**
     * Get the language of the source text.
     *
     * @return string
     */
    public function getSourceLanguage(): string
    {
        return $this->sourceLanguage;
    }

    /**
     * Set language of target text.
     *
     * @param string $language
     *
     * @return Translation
     */
    public function setTargetLanguage(string $language): Translation
    {
        $this->targetLanguage = strtolower($language);

        return $this;
    }

    /**
     * Get the target text language.
     *
     * @return string
     */
    public function getTargetLanguage(): string
    {
        return $this->targetLanguage;
    }

    /**
     * Set if translation is cached.
     *
     * @param bool $flag
     *
     * @return Translation
     */
    public function setLoadedFromCache(bool $flag): Translation
    {
        $this->loadedFromCache = $flag;

        return $this;
    }

    /**
     * Get if translation is cached.
     *
     * @return bool
     */
    public function getLoadedFromCache(): bool
    {
        return $this->loadedFromCache;
    }

    /**
     * Get model properties as array for json serialization.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return
            [
                'source_text' => $this->getSourceText(),
                'source_language' => $this->getSourceLanguage(),
                'target_text' => $this->getTargetText(),
                'target_language' => $this->getTargetLanguage(),
            ];
    }
}
