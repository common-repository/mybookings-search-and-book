<?php

/**
 * Class TranslationService
 */
class TranslationService
{
    /** @var string[] */
    protected $foreignLanguages = ['en', 'es', 'it', 'fr'];

    /**
     * @param string $phrase
     * @return string
     */
    public function translate($phrase)
    {
        $currentLanguage = pll_current_language();

        if ($currentLanguage === false) { // backend
            $currentLanguage = 'de';
        }

        $translations = $this->getTranslations();

        $this->checkAvailableLanguages();

        if (!array_key_exists($phrase, $translations)) {
            $this->addTranslation($phrase);

            return $phrase;
        }

        $existingPhrase = $translations[$phrase];

        if ($currentLanguage === 'de') {
            return $phrase;
        }

        $translation = $existingPhrase[$currentLanguage];

        return !empty($translation) ? $translation : $phrase;
    }

    private function checkAvailableLanguages()
    {
        $translations = $this->getTranslations();

        if (count($translations) === 0) {
            return;
        }

        $availableLanguages = $this->foreignLanguages;

        foreach ($translations as $phrase => $translation) {
            $existingLanguages = array_keys($translation);

            $missingLanguages = array_diff($availableLanguages, $existingLanguages);

            if (count($missingLanguages) === 0) {
                return;
            }

            foreach ($missingLanguages as $missingLanguage) {
                $translations[$phrase][$missingLanguage] = '';
            }
        }

        file_put_contents(plugin_dir_path(__FILE__) . 'translations.json', json_encode($translations));
    }

    private function getTranslations()
    {
        return json_decode(file_get_contents(plugin_dir_path(__FILE__) . 'translations.json'), TRUE);
    }

    /**
     * @param string $phrase
     */
    private function addTranslation($phrase)
    {
        $translations = $this->getTranslations();

        foreach ($this->foreignLanguages as $foreignLanguage) {
            $translations[$phrase][$foreignLanguage] = '';
        }

        file_put_contents(plugin_dir_path(__FILE__) . 'translations.json', json_encode($translations));
    }
}

function ___($phrase)
{
    return $GLOBALS['mbTranslation']->translate($phrase);
}