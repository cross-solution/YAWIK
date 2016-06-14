<?php

namespace Cv\Form;

use Cv\Entity\PreferredJob;
use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

class PreferredJobFieldset extends Fieldset
{
    /**
     * Type of Application Options
     *
     * @var array
     */
    public static $typoOfApplicationOptions = [
        '' => '', // needed for jquery select2 to render the placeholder
        "temporary" => /*@translate*/ "Temporary",
        "permanent" => /*@translate*/ "Permanent",
        "contract"=> /*@translate*/ "Contracting",
        "freelance" => /*@translate*/ "Freelance",
        "internship" => /*@translate*/ "Internship"
    ];

    public static $willingnessToTravelOptions = [
        '' => '', // needed for jquery select2 to render the placeholder
        "yes"=>/*@translate*/ "Yes",
        "conditioned" => /*@translate*/ "conditioned",
        "no"=>/*@translate*/ "No"
    ];

    public function init()
    {
        $this->setName('preferredJob')
             ->setHydrator(new EntityHydrator())
             ->setObject(new PreferredJob())
             ->setLabel('Desired Employment');

        $this->add(
            array(
                'name' => 'typeOfApplication',
                'type' => 'select',
                'options' => [
                    'value_options' => self::$typoOfApplicationOptions,
                    'label' => /*@translate */ 'desired type of work',
                    'description' => /*@translate*/ 'Do you want to work permanently or temporary?',
                ],
                'attributes' => array(
                    'title' => /*@translate */ 'please describe your position',
                    'description' => 'what kind of ',
                    'data-placeholder' => /*@translate*/ 'please select',
                    'data-allowclear' => 'false',
                    'data-searchbox' => -1,
                    'multiple' => true,
                    'data-width' => '100%',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'desiredJob',
                'type' => 'Text',
                'options' => array(
                        'label' => /*@translate */ 'desired job position',
                        'description' => /*@translate*/ 'Enter the title of your desired job. Eg. "Software Developer" or "Customer Service Representative"',
                ),
                'attributes' => array(
                        'title' => /*@translate */ 'please describe your position',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'desiredLocations',
                'type' => 'Location',
                'options' => array(
                    'label' => /*@translate */ 'desired job location',
                    'description' => /*@translate*/ 'Where do you want to work?',
                ),
                'attributes' => array(
                    'title' => /*@translate */ 'please describe your position',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'willingnessToTravel',
                'type' => 'Select',
                'options' => array(
                    'value_options' => self::$willingnessToTravelOptions,
                    'label' => /*@translate*/ 'Willingness to travel',
                    'description' => /*@translate*/ 'Enter your willingness to travel.',
                ),
                'attributes' => array(
                    'data-placeholder' => /*@translate*/ 'please select',
                    'data-allowclear' => 'false',
                    'data-searchbox' => -1,
                    'data-width' => '100%'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'expectedSalary',
                'type' => 'Text',
                'options' => array(
                    'label' => /*@translate */ 'expected Salary',
                    'description' => /*@translate*/ 'What is your expected Salary?',
                ),
                'attributes' => array(
                    'title' => /*@translate */ 'please describe your position',
                ),
            )
        );
    }
}
