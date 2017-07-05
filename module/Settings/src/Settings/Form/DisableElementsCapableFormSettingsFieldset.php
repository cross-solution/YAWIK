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

use Interop\Container\ContainerInterface;
use Zend\Form\Fieldset;
use Settings\Form\Element\DisableElementsCapableFormSettings;
use Settings\Entity\Hydrator\SettingsEntityHydrator;
use Settings\Entity\Hydrator\Strategy\DisableElementsCapableFormSettings as DisableElementsStrategy;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Fieldset for toggling form elements of DisableCapable forms.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class DisableElementsCapableFormSettingsFieldset extends Fieldset
{
    /**
     * The form elements manager.
     *
     * @var ServiceLocatorInterface
     */
    protected $formManager;

    /**
     * Flag if this fieldset is build.
     * @var bool
     */
    protected $isBuild = false;
    
    /**
     * @param ServiceLocatorInterface $formManager
     */
    public function __construct(ServiceLocatorInterface $formManager)
    {
        parent::__construct();
        $this->formManager = $formManager;
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
        $form     = $this->formManager->get($settings->getForm());

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
        $element = new DisableElementsCapableFormSettings('disableElements');
        $element->setForm($form);
        $this->add($element);

        $this->isBuild = true;
    }
	
	/**
	 * @param ContainerInterface $container
	 *
	 * @return DisableElementsCapableFormSettingsFieldset
	 */
    public static function factory(ContainerInterface $container)
    {
        return new static($container->get('FormElementManager'));
    }
}
