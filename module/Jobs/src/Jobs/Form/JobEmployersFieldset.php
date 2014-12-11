<?php

namespace Jobs\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * ??
 *
 * @package Jobs\Form
 */
class JobEmployersFieldset extends Fieldset
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
        $this->setAttribute('id', 'jobemployers-fieldset');
        $this->setLabel('Employers');

    }
}