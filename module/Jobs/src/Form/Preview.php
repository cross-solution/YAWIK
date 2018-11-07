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

use Core\Form\Form;
use Core\Form\Container;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;
use Core\Form\propagateAttributeInterface;

/**
 * Defines the form on the 3rd page when entering a job position
 *
 * @package Jobs\Form
 */
class Preview extends Form implements propagateAttributeInterface
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
        $this->setAttributes(
            array(
            'id' => 'jobs-form-preview',
            //'data-handle-by' => 'native'
            )
        );


        $this->add(
            array(
            'type' => 'Jobs/PreviewFieldset',
            'name' => 'jobPreview',
            'options' => array(
                'use_as_base_fieldset' => true,
            ),
            )
        );
    }

    public function setViewPartial($partial)
    {
        return $this;
    }

    public function getViewPartial()
    {
        return 'form/preview.phtml';
    }

    public function enableAll($enable = true)
    {
        foreach ($this as $forms) {
            if ($forms instanceof propagateAttributeInterface) {
                $forms->enableAll($enable);
            }
        }
        return $this;
    }
}
