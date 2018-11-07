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
use Core\Form\ViewPartialProviderInterface;
use Core\Form\Element\ViewHelperProviderInterface;

//  implements ViewPartialProviderInterface

class PreviewLink extends Element implements ViewHelperProviderInterface
{
    protected $attributes = array(
        'type' => 'previewLink',
    );

    protected $helper = 'jobPreviewLink';

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
