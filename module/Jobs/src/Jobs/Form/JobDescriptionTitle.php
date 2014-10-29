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
use Core\Entity\Hydrator\EntityHydrator;
use Zend\InputFilter\InputFilterProviderInterface;
use Core\Form\ViewPartialProviderInterface;
use Jobs\Form\Hydrator\JobDescriptionHydrator;

class JobDescriptionTitle extends Form implements InputFilterProviderInterface
{

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new JobDescriptionHydrator();
            //$hydrator->addStrategy('descriptionbenefits', new Hydrator\Strategy\JobDescriptionBenefitsStrategy());
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function init()
    {
        $this->setName('jobs-form-title');
        $this->setAttributes(array(
            'id' => 'jobs-form-benefits',
            'data-handle-by' => 'native'
        ));

        $this->add(array(
            'type' => 'TextEditor',
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