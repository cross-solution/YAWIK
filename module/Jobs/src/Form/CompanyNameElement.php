<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Laminas\Form\Element;
use Core\Form\Element\ViewHelperProviderInterface;

/**
 * this Class is a rudimentary base for the future extension of entering the hiring organization name
 *
 * Class CompanyNameElement
 *
 * @package Jobs\Form
 */
class CompanyNameElement extends Element implements ViewHelperProviderInterface
{
    protected $attributes = array(
        'type' => 'text',
    );

    // a distinct helper will be needed if this element is expanded
    // for the moment a inputfield is all we need, and this is achieved by the text
    //protected $helper = 'jobPreviewLink';
    protected $helper = 'formText';

    /**
     * @param string|\Laminas\View\Helper\HelperInterface $helper
     * @return $this|ViewHelperProviderInterface
     */
    public function setViewHelper($helper)
    {
        $this->helper = $helper;
        return $this;
    }

    /**
     * @return string|\Laminas\View\Helper\HelperInterface
     */
    public function getViewHelper()
    {
        return $this->helper;
    }
}
