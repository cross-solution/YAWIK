<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** TranslatorAwareMessage.php */ 
namespace Core\Mail;

use Zend\I18n\Translator\TranslatorAwareInterface;

class TranslatorAwareMessage extends Message implements TranslatorAwareInterface
{
    protected $translator;
    protected $translatorEnabled = false;
    protected $translatorTextDomain = 'default';
    
    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::getTranslator()
     */
    public function getTranslator ()
    {
        return $this->translator;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::getTranslatorTextDomain()
     */
    public function getTranslatorTextDomain ()
    {
        return $this->translatorTextDomain;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::hasTranslator()
     */
    public function hasTranslator ()
    {
        return null !== $this->translator;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::isTranslatorEnabled()
     */
    public function isTranslatorEnabled ()
    {
        return $this->hasTranslator() && $this->translatorEnabled;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::setTranslator()
     */
    public function setTranslator (\Zend\I18n\Translator\Translator $translator = null, $textDomain = 'default')
    {
        if ($translator) {
            $this->translator = $translator;
        }
        $this->setTranslatorTextDomain($textDomain);
        return $this;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::setTranslatorEnabled()
     */
    public function setTranslatorEnabled ($enabled = true)
    {
        $this->translatorEnabled = (bool) $enabled;
        return $this;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::setTranslatorTextDomain()
     */
    public function setTranslatorTextDomain ($textDomain = 'default')
    {
        $this->translatorTextDomain = $textDomain;
        return $this;
    }

}

