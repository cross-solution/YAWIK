<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** TranslatorAwareMessage.php */
namespace Core\Mail;

use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorInterface;

class TranslatorAwareMessage extends Message implements TranslatorAwareInterface
{
    protected $translator;
    protected $translatorEnabled = false;
    protected $translatorTextDomain = 'default';
    
    /**
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::getTranslator()
     *
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::getTranslatorTextDomain()
     */
    public function getTranslatorTextDomain()
    {
        return $this->translatorTextDomain;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::hasTranslator()
     */
    public function hasTranslator()
    {
        return null !== $this->translator;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::isTranslatorEnabled()
     */
    public function isTranslatorEnabled()
    {
        return $this->hasTranslator() && $this->translatorEnabled;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::setTranslator()
     */
    public function setTranslator(TranslatorInterface $translator = null, $textDomain = null)
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
    public function setTranslatorEnabled($enabled = true)
    {
        $this->translatorEnabled = (bool) $enabled;
        return $this;
    }

    /* (non-PHPdoc)
     * @see \Zend\I18n\Translator\TranslatorAwareInterface::setTranslatorTextDomain()
     */
    public function setTranslatorTextDomain($textDomain = 'default')
    {
        $this->translatorTextDomain = $textDomain;
        return $this;
    }

    /**
     * Sets the message subject.
     *
     * The passed string is automatically translated.
     * Any additional argument is passed in to sprintf to replace
     * placeholders in the subject string.
     *
     * If the $translate is FALSE, the subject is NOT translated.
     *
     * <pre>
     * <?php
     *      $mail->setSubject('translated subject');
     *      $mail->setSubject('translated %s with placeholder', 'subject');
     *      $mail->setSubject('untranslated subject', false);
     * ?>
     * </pre>
     *
     * @param string $subject
     * @param bool|mixed $translate
     *
     * @since 0.19
     * @since 0.29 Add sprintf support for translation
     *
     * @return Message
     */
    public function setSubject($subject, $translate = true)
    {
        if (false !== $translate) {
            $translator = $this->getTranslator();
            $domain     = $this->getTranslatorTextDomain();

            if (true === $translate) {
                $subject = $translator->translate($subject, $domain);
            } else {
                $args = func_get_args();
                $args[0] = $translator->translate($args[0], $domain);
                $subject = call_user_func_array('sprintf', $args);
            }
        }

        return parent::setSubject($subject);
    }

    /**
     *
     *
     * @param $formatString
     *
     * @return \Zend\Mail\Message
     *
     * @deprecated since 0.29 use setSubject(formatString, replacement, ...)
     * @codeCoverageIgnore
     */
    public function setFormattedSubject($formatString)
    {
        $args       = func_get_args();
        $translator = $this->getTranslator();
        $args       = array_slice($args, 1);
        $domain     = $this->getTranslatorTextDomain();
        $subject    = $translator->translate($formatString, $domain);
        array_unshift($args, $subject);
        $subject    = call_user_func_array('sprintf', $args);
        return parent::setSubject($subject);
    }
}
