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

use Jobs\Entity\TemplateValuesInterface;
use Zend\Form\Fieldset;
use Jobs\Form\Hydrator\JobDescriptionHydrator;

/**
 * Defines the formular fields of a job opening
 *
 * @package Jobs\Form
 */
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

    public function allowObjectBinding($object)
    {
        return $object instanceof TemplateValuesInterface || parent::allowObjectBinding($object);
    }

    public function init()
    {
        $this->setAttribute('id', 'description-fieldset');
        $this->setLabel('Description');


        $this->add(
            array(
            'type' => 'Texteditor',
            'name' => 'descriptionqualification',
            'options' => array(
                'label' => /*@translate*/ 'Job qualification'
            ),
            )
        );

        $this->add(
            array(
            'type' => 'Texteditor',
            'name' => 'descriptionbenefits',
            'options' => array(
                'label' => /*@translate*/ 'Job benefits'
            ),
            )
        );


        $this->add(
            array(
            'type' => 'Texteditor',
            'name' => 'descriptionrequirements',
            'options' => array(
                'label' => /*@translate*/ 'Job requirements'
            ),
            )
        );
    }
}
