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
