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
use Jobs\Form\Hydrator\PreviewLinkHydrator;

class JobPreviewFieldset extends Fieldset
{
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $serviceLocater = $this->getFormFactory()->getFormElementManager()->getServiceLocator();
            //$hydrator = new PreviewLinkHydrator();
            $hydrator = $serviceLocater->get('Jobs/PreviewLinkHydrator');
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function init()
    {
        $this->setAttribute('id', 'jobpreview-fieldset');
        $this->setLabel('Preview');

        $this->add(
             array(
                'type' => 'Jobs/JobPreviewLink',
                'property' => true,
                'name' => 'previewLink',
            )
        );

        $this->add(
             array(
            'type' => 'infocheckbox',
            'name' => 'acceptedPrivacyPolicy',
            'options' => array(
                'headline' => /*@translate*/ 'Privacy Policy',
                'long_label' => /*@translate*/ 'I have read the %s and accept it',
                'linktext' => /*@translate*/ 'Privacy Policy',
                'route' => 'lang/applications/disclaimer',
            ),
            'attributes' => array(
                'data-validate' => 'acceptedPrivacyPolicy',
                'data-trigger' => 'submit',
            ),
        ));

    }
}