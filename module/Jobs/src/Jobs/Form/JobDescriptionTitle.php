<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;
use Core\Form\ViewPartialProviderInterface;
use Jobs\Form\Hydrator\JobDescriptionHydrator;

/**
 * Defines the formular field "title" of a job opening.
 *
 * @package Jobs\Form
 */
class JobDescriptionTitle extends Form implements InputFilterProviderInterface
{

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new JobDescriptionHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function init()
    {
        $this->setName('jobs-form-title');
        $this->setAttributes(array(
            'id' => 'jobs-form-title',
            'data-handle-by' => 'yk-form'
        ));

        $this->add(array(
            'type' => 'TextEditorLight',
            'name' => 'description-title',
            'options' => array(
                'use_as_base_fieldset' => true,
            ),
        ));


    }

    public function getInputFilterSpecification()
    {
        return array(
        );
    }


}