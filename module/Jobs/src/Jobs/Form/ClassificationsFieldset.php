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
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
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
                    'label' => /*@translate*/ 'Professions',
                ],
                'attributes' => [
                    'data-width' => '100%',
                    'multiple' => true,
                ],
            ]
        );
        $this->add($professions);

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
                ],
                'attributes' => [
                    'data-width' => '100%',
                    //'multiple' => true,
                ]
            ]
        );
        $this->add($types);


        $hydrator = $this->getHydrator();
        $hydrator->addStrategy('professions', $professions->getHydratorStrategy());
        $hydrator->addStrategy('employmentTypes', $types->getHydratorStrategy());
    }
}
