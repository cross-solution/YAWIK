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

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * Defines the formular fields of the base formular of a job opening.
 */
class BaseFieldset extends Fieldset
{
    /**
     * name of the used geo location Engine
     *
     * @var string  $locationEngineType
     */
    protected $locationEngineType;

    /**
     * @param $locationEngineType
     */
    public function setLocationEngineType($locationEngineType) {
        $this->locationEngineType = $locationEngineType;
    }
 
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

        $this->setName('jobBase');
        
        $this->add(
            array(
            'type' => 'Text',
            'name' => 'title',
            'options' => array(
                'label' => /*@translate*/ 'Job title',
                'description' => /*@translate*/ 'Please enter the job title'
            ),
            )
        );
       
        $this->add(
            array(
            'type' => 'Location',
            'name' => 'location',
            'options' => array(
                'label' => /*@translate*/ 'Location',
                'description' => /*@translate*/ 'Please enter the location of the job',
                'engine_type' => $this->locationEngineType,
            ),
            )
        );
    }
}
