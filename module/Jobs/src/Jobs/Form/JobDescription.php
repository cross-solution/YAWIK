<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Form\Form;
use Core\Form\Container;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;
use Core\Form\ViewPartialProviderInterface;

class JobDescription extends Container implements ViewPartialProviderInterface
{

    public function init()
    {
        $this->setName('jobs-form-description');
        $this->setAttributes(array(
            'id' => 'jobs-form-description',
            'data-handle-by' => 'native'
        ));
    }

    public function setViewPartial($partial) {
        return $this;
    }

    public function getViewPartial() {
        return 'iframe/iFrame.phtml';
    }

}