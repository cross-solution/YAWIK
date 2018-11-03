<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.26
 */

namespace Core\I18n;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Request;
use Auth\Entity\UserInterface as User;

class Locale
{
    
    /**
     * @var string
     */
    protected $defaultLanguage;
    
    /**
     * @var array
     */
    protected $supportedLanguages;
    
    /**
     * @param array $supportedLanguages
     */
    public function __construct(array $supportedLanguages)
    {
        $this->supportedLanguages = $supportedLanguages;
        $languages = array_keys($supportedLanguages);
        $this->defaultLanguage = reset($languages);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return string
     */
    public function detectLanguage(Request $request, User $user = null)
    {
        if (isset($user)) {
            $settings = $user->getSettings('Core');
            
            if (isset($settings->localization)
                && isset($settings->localization->language)
                && $settings->localization->language != '') {
                // return language by user's settings
                return $settings->localization->language;
            }
        }
        
        $headers = $request->getHeaders();
        
        if ($headers->has('Accept-Language')) {
            $locales = $headers->get('Accept-Language')->getPrioritized();
            foreach ($locales as $locale) {
                $language = $locale->type;
                
                if (isset($this->supportedLanguages[$language])) {
                    // return language by browser's accept language
                    return $language;
                }
            }
        }
        
        // no match, therefore return default language
        return $this->defaultLanguage;
    }
    
    /**
     * @param string $lang
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getLocaleByLanguage($lang)
    {
        if (!isset($this->supportedLanguages[$lang])) {
            throw new \InvalidArgumentException(sprintf('Unsupported language: "%s"', $lang));
        }
        
        return $this->supportedLanguages[$lang];
    }
    
    /**
     * @param string $lang
     * @return boolean
     */
    public function isLanguageSupported($lang)
    {
        return isset($this->supportedLanguages[$lang]);
    }
    
    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }
}
