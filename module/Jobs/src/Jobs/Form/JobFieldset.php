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

        $this->setName('jobTitleLocation');


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
                'label' => /*@translate*/ 'Job title',
                'description' => /*@translate*/ 'Please enter the job title'
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
                'label' => /*@translate*/ 'Location',
                'description' => /*@translate*/ 'Please enter the location of the job'
            ),
        ));
    }
}