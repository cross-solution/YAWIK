<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Zend\Form\Element;
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
     * @param string|\Zend\View\Helper\HelperInterface $helper
     * @return $this|ViewHelperProviderInterface
     */
    public function setViewHelper($helper)
    {
        $this->helper = $helper;
        return $this;
    }

    /**
     * @return string|\Zend\View\Helper\HelperInterface
     */
    public function getViewHelper()
    {
        return $this->helper;
    }
}
