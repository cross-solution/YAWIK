<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Jobs\Entity\Category;
use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * Fieldset for the category management.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class ClassificationsFieldset extends Fieldset
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
        $this->setName('classifications');
        $formElements = $this->getFormFactory()->getFormElementManager();

        $professions = $formElements->get(
            'Core/Tree/Select',
            [
                'tree' => [
                    'entity' => Category::class,
                    'value' => 'professions',
                ],
                'allow_select_nodes' => true,
                'name' => 'professions',
                'options' => [
                    'description' => /*@translate*/ 'Select the professions of the job opening. This allows an applicant to find job openings by a certain profession.',
                    'label' => /*@translate*/ 'Professions',
                ],
                'attributes' => [
                    'data-width' => '100%',
                    'multiple' => true,
                ],
            ]
        );
        $this->add($professions);

        $industries = $formElements->get(
            'Core/Tree/Select',
            [
                'tree' => [
                    'entity' => Category::class,
                    'value' => 'industries',
                ],
                'allow_select_nodes' => true,
                'name' => 'industries',
                'options' => [
                    'label' => /*@translate*/ 'Industries',
                    'description' => /*@translate*/ 'Select the industry of the hiring organization. This allows an applicant to search for job opening by industry.',
                ],
                'attributes' => [
                    'data-width' => '100%',
                    'multiple' => true,
                ],
            ]
        );
        $this->add($industries);

        $types = $formElements->get(
            'Core/Tree/Select',
            [
                'tree' => [
                    'entity' => Category::class,
                    'value' => 'employmentTypes',
                ],
                'name' => 'employmentTypes',
                'options' => [
                    'label' => /*@translate*/ 'Employment Types',
                    'description' => /*@translate*/ 'Manage the employment types you want to assign to jobs.',
                ],
                'attributes' => [
                    'data-width' => '100%',
                    'multiple' => true,
                ]
            ]
        );
        $this->add($types);


        $hydrator = $this->getHydrator();
        $hydrator->addStrategy('professions', $professions->getHydratorStrategy());
        $hydrator->addStrategy('employmentTypes', $types->getHydratorStrategy());
        $hydrator->addStrategy('industries', $industries->getHydratorStrategy());
    }
}
