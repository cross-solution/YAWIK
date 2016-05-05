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

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\ViewPartialProviderInterface;
use Jobs\Entity\AtsModeInterface;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Base Fieldset for \Jobs\Form\AtsMode
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.19
 */
class AtsModeFieldset extends Fieldset implements ViewPartialProviderInterface, InputFilterProviderInterface
{

    /**
     * View partial name
     *
     * @var string
     */
    protected $partial = 'jobs/form/ats-mode';

    public function setViewPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }

    public function getViewPartial()
    {
        return $this->partial;
    }

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function allowObjectBinding($object)
    {
        return $object instanceof AtsModeInterface || parent::allowObjectBinding($object);
    }


    public function init()
    {
        $this->setName('atsMode');
        $this->add(
            array(
            'type' => 'Select',
            'name' => 'mode',
            'options' => array(
                'label' => /*@translate*/ 'Mode',
                'value_options' => array(
                    'intern' => /*@translate*/ 'Built-In ATS',
                    'uri'    => /*@translate*/ 'Use external link',
                    'email'  => /*@translate*/ 'Get applications via email',
                    'none'   => /*@translate*/ 'Do not track',
                ),
            ),
            'attributes' => array(
                'data-searchbox' => 'false',
                'data-width' => '100%',
                'value' => 'email',
            )
            )
        );

        $this->add(
            array(
            'type' => 'Text',
            'name' => 'uri',
            'options' => array(
                'label' => /*@translate*/ 'URL',
            )
            )
        );

        $this->add(
            array(
            'type' => 'Text',
            'name' => 'email',
            'options' => array(
                'label' => /*@translate*/ 'Email',
            ),
            )
        );
        
        $this->add([
            'type' => 'Checkbox',
            'name' => 'oneClickApply',
            'options' => [
                'label' => /*@translate*/ 'One click apply',
            ]
        ]);
        
        $this->add([
            'type' => 'Select',
            'name' => 'oneClickApplyProfiles',
            'options' => [
                'label' => /*@translate*/ 'Social profiles',
                'value_options' => [
                    'facebook' => 'Facebook',
                    'xing'     => 'Xing',
                    'linkedin' => 'LinkedIn'
                ],
                'use_hidden_element' => true
            ],
            'attributes' => [
                'multiple' => true,
                'data-width' => '100%',
            ]
        ]);
    }

    /**
     * Returns the input filter specification.
     *
     * @internal
     *  Only specifies type to get the input filter from the plugin manager via its
     *  factory.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'type' => 'Jobs/AtsMode',
        );
    }
}
