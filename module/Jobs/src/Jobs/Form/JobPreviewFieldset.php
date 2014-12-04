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

class JobPreviewFieldset extends Fieldset
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
        $this->setAttribute('id', 'jobpreview-fieldset');
        $this->setLabel('Preview');

        /*
         * taken out in accordance by the team and written in the view
        $this->add(
             array(
                'type' => 'Jobs/JobPreviewLink',
                'property' => true,
                'name' => 'previewLink',
            )
        );
        */

        $this->add(
             array(
            'type' => 'infocheckbox',
            'name' => 'termsAccepted',
            'options' => array(
                'headline' => /*@translate*/ 'Terms and conditions',
                'long_label' => /*@translate*/ 'I have read the %s and accept it',
                'linktext' => /*@translate*/ 'terms an conditions',
                'route' => 'lang/jobs/terms',
            ),
            'attributes' => array(
                'data-trigger' => 'submit',
            ),
        ));

    }
}