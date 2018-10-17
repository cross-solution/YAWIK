<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core view helpers */
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Renders the Language Switcher
 *
 * <code>
 *
 *      // Renders the language switcher
 *      echo $this->languageSwitcher($lang);
 *
 * </code>
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class LanguageSwitcher extends AbstractHelper
{

    /**
    * @var string
    */
    protected $partial = 'partial/language-switcher';

    /**
     * generates a select2 form with enabled languages
     *
     * @return string
     */
    public function __invoke($options = [])
    {

        $options = array_merge([
                                   'partial' => $this->partial,
                               ], $options);

        $view = $this->view;
        $partial = $options['partial'];


        $variables=[];

        return $view->partial($partial, $variables);
    }

    /**
     * @return string
     */
    public function getPartial()
    {
        return $this->partial;
    }

    /**
     * @param string $partial
     * @return LanguageSwitcher
     */
    public function setPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }
}
