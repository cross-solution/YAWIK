<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Form;

use Core\Form\SummaryForm;
use Core\Form\WizardContainer;

/**
 * Container for the management of job categories.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class CategoriesContainer extends WizardContainer
{

    public function init()
    {
        $this->setForms([
            'professions' => [
                'options' => [
                    'label' => /*@translate*/ 'Professions',
                ],
                'entity' => 'professions',
                'property' => true,
                'forms' => [
                    'professions' => [
                        'type' => 'Core/Tree/Management',
                        'property' => true,
                        'options' => [
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Manage the professions you want to assign to jobs.' .
                                             /*@translate*/ 'The order of categories can be modified by drag&drop.',
                            'display_mode' => SummaryForm::DISPLAY_SUMMARY,
                        ],
                    ],
                ],
            ],
            'industries' => [
                'options' => [
                    'label' => /*@translate*/ 'Industries',
                ],
                'entity' => 'industries',
                'property' => true,
                'forms' => [
                    'industries' => [
                        'type' => 'Core/Tree/Management',
                        'property' => true,
                        'options' => [
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Manage the industries you want to assign to jobs.' .
                                             /*@translate*/ 'The order of categories can be modified by drag&drop.',
                            'display_mode' => SummaryForm::DISPLAY_SUMMARY,
                        ],
                    ],
                ],
            ],
            'employmentTypes' => [
                'options' => [
                    'label' => /*@translate*/ 'Employment Types',
                ],
                'entity' => 'employmentTypes',
                'property' => true,
                'forms' => [
                    'employmentTypes' => [
                        'type' => 'Core/Tree/Management',
                        'property' => true,
                        'options' => [
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Manage the employment types you want to assign to jobs.'.
                                             /*@translate*/ 'The order of categories can be modified by drag&drop.',
                            'display_mode' => SummaryForm::DISPLAY_SUMMARY,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
