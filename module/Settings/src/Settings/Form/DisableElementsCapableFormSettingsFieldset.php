<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright 2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Settings\Form;

use Zend\Form\Fieldset;
use Settings\Entity\Hydrator\SettingsEntityHydrator;
use Settings\Entity\Hydrator\Strategy\DisableElementsCapableFormSettings as DisableElementsStrategy;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Fieldset for toggling form elements of DisableCapable forms.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class DisableElementsCapableFormSettingsFieldset extends Fieldset implements ServiceLocatorAwareInterface
{
    /**
     * The form elements manager.
     *
     * @var ServiceLocatorInterface
     */
    protected $forms;

    /**
     * Flag if this fieldset is build.
     * @var bool
     */
    protected $isBuild = false;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->forms = $serviceLocator;
        return $this;
    }
    
    public function getServiceLocator()
    {
        return $this->forms;
    }

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new SettingsEntityHydrator();
            $hydrator->addStrategy('disableElements', new DisableElementsStrategy());
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    /**
     * @uses build()
     */
    public function setObject($object)
    {
        parent::setObject($object);
        $this->build();
        return $this;
    }

    /**
     * Builds this fieldset.
     *
     * Adds the disableElements element and populate its values,
     * which is only possible, if the bound object is set.
     */
    public function build()
    {
        
        if ($this->isBuild) {
            return;
        }

        $settings = $this->getObject();
        $form     = $this->forms->get($settings->getForm());

        $this->setLabel(
            $form->getOption('settings_label') ?: sprintf(
                /*@translate*/ 'Customize enabled elements for %s',
                get_class($form)
            )
        );

        $this->add(
            array(
            'type' => 'Checkbox',
            'name' => 'isActive',
            'options' => array(
                'label' => /*@translate*/ 'Activate',
                'long_label' => /*@translate*/ 'Enables the form element customization.',
            ),
            'attributes' => array(
                'class' => 'decfs-active-toggle',
            ),
            )
        );
        $element = new \Settings\Form\Element\DisableElementsCapableFormSettings('disableElements');
        $element->setForm($form);
        $this->add($element);

        $this->isBuild = true;
    }
}
