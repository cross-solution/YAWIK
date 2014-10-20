<?php

namespace Jobs\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

class JobEmployersFieldset extends Fieldset
{


    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            /*
            $datetimeStrategy = new Hydrator\DatetimeStrategy();
            $datetimeStrategy->setHydrateFormat(Hydrator\DatetimeStrategy::FORMAT_MYSQLDATE);
            $hydrator->addStrategy('datePublishStart', $datetimeStrategy);
             */
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