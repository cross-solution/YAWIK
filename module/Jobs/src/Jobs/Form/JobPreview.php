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


class JobPreview extends Form
{

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function init()
    {
        $this->setName('jobs-form-preview');
        $this->setAttributes(array(
            'id' => 'jobs-form-preview',
            //'data-handle-by' => 'native'
        ));


        $this->add(array(
            'type' => 'Jobs/PreviewFieldset',
            'name' => 'jobPreview',
            'options' => array(
                'use_as_base_fieldset' => true,
            ),
        ));
    }

    public function setViewPartial($partial) {
        return $this;
    }

    public function getViewPartial() {
        return 'form/preview.phtml';
    }

}