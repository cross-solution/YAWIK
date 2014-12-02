<?php

namespace Jobs\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * Basic Job form. Contains the Jobtitle and the location of the Job
 */
class JobFieldset extends Fieldset 
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
        $this->setAttribute('id', 'job-fieldset');
        $this->setLabel('Job details');


//       $this->add(array(
//            'type' => 'Text',
//            'name' => 'company',
//            'options' => array(
//                'label' => /*@translate*/ 'Company'
//            ),
//        ));

        
       $this->add(array(
            'type' => 'Text',
            'name' => 'title',
            'options' => array(
                'label' => /*@translate*/ 'Job title'
            ),
        ));
       
//       $this->add(array(
//           'type' => 'Jobs/ApplyId',
//           'name' => 'applyId',
//           'options' => array(
//               'label' => /*@translate*/ 'Apply Identifier'
//           ),
//       ));
//
//       $this->add(array(
//           'type' => 'Textarea',
//           'name' => 'description',
//           'options' => array(
//                'label' => /*@translate*/ 'Job description'
//           ),
//       ));
       
       $this->add(array(
            'type' => 'Location',
            'name' => 'location',
            'options' => array(
                'label' => /*@translate*/ 'Location'
            ),
        ));
       
//       $this->add(array(
//            'type' => 'Text',
//            'name' => 'contactEmail',
//            'options' => array(
//                'label' => /*@translate*/ 'Contact email'
//            ),
//
//        ));
//
//
//       $this->add(array(
//            'type' => 'Text',
//            'name' => 'reference',
//            'options' => array(
//                'label' => /*@translate*/ 'Reference number'
//            ),
//
//        ));
       
       //$this->add(array(
       //    'type' => 'Core/PermissionsCollection'
       //));

       // @TODO: insert editable hidden field
       //$this->add(array(
       //     'type'    => 'Jobs/ApplyId',
       //     'name'    => 'applyId',
       //     'options' => array(
       //         'label' => /*@translate*/
       //             'Apply Identifier'
       //     ),
       //));
    }
}