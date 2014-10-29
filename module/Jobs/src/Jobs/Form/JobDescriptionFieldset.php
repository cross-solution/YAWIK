<?php

namespace Jobs\Form;

use Zend\Form\Fieldset;
//use Core\Entity\Hydrator\EntityHydrator;
use Jobs\Form\Hydrator\JobDescriptionHydrator;

class JobDescriptionFieldset extends Fieldset
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
        $this->setAttribute('id', 'description-fieldset');
        $this->setLabel('Description');


        $this->add(array(
            'type' => 'Texteditor',
            'name' => 'descriptionqualification',
            'options' => array(
                'label' => /*@translate*/ 'Job qualification'
            ),
        ));

        $this->add(array(
            'type' => 'Texteditor',
            'name' => 'descriptionbenefits',
            'options' => array(
                'label' => /*@translate*/ 'Job benefits'
            ),
        ));


        $this->add(array(
            'type' => 'Texteditor',
            'name' => 'descriptionrequirements',
            'options' => array(
                'label' => /*@translate*/ 'Job requirements'
            ),
        ));

        //$this->add(array(
        //    'type' => 'Texteditor',
        //    'name' => 'description',
        //    'options' => array(
        //        'label' => /*@translate*/ 'Job description'
        //    ),
        //));


    }
}