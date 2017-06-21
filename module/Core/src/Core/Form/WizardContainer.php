<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Form;

use Zend\Form\Element;

/**
 * A wizard style form container.
 *
 * Holds instances of form containers which each represent a "tab" of an wizard.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.22
 */
class WizardContainer extends Container implements HeadscriptProviderInterface, \IteratorAggregate
{
    /**
     * Headscripts.
     *
     * @var array
     */
    protected $scripts = [
        '/assets/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js',
    ];

    public function setHeadscripts(array $scripts)
    {
        $this->scripts = $scripts;

        return $this;
    }

    public function getHeadscripts()
    {
        return $this->scripts;
    }

    /**
     * Sets a form container.
     *
     * Either pass in an object of type \Core\Form\Container or provide a $spec array.
     * Only instances of \Core\Form\Container which have a label are allowed.
     *
     * @see \Core\Form\Container::setForm
     *
     * @param string                            $key
     * @param array|string|\Core\Form\Container $spec
     * @param bool                              $enabled
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setForm($key, $spec, $enabled = true)
    {
        if (is_object($spec)) {
            if (!$spec instanceOf Container) {
                throw new \InvalidArgumentException('Tab container must be of the type \Core\Form\Container');
            }

            if (!$spec->getLabel()) {
                throw new \InvalidArgumentException('Container instances must have a label.');
            }
        }

        if (is_array($spec)) {
            if (!isset($spec['type'])) {
                $spec['type'] = 'Core/Container';
            }

            /*
             * For convenience, forms may be specified outside the options array.
             * But in order to be passed through to the form element manager,
             * we must move it to the options.
             */
            if (!isset($spec['options']['forms']) && isset($spec['forms'])) {
                $spec['options']['forms'] = $spec['forms'];
                unset($spec['forms']);
            }
        }

        return parent::setForm($key, $spec, $enabled);
    }

    /**
     * Gets a specific formular.
     *
     * This formular will be created upon the first retrievement.
     * If created, the formular gets passed the formular parameters set in this container.
     *
     * @see Container::getForm
     *
     * @param string $key
     * @param bool   $asInstance if false, the specification array is returned.
     *
     * @return Container|null|\Zend\Form\FormInterface|array
     * @throws \UnexpectedValueException
     */
    public function getForm($key, $asInstance = true)
    {
        $form = parent::getForm($key, $asInstance);
        
        /*
         * We must check here, if a lazy loaded top level form is an
         * instance of Container.
         */
        if ($asInstance && false === strpos($key, '.') && (!$form instanceOf Container || !$form->getLabel())) {
            throw new \UnexpectedValueException(sprintf(
                                                    'The registered form with key "%s" is not an instance of \Core\Form\Container or does not have a label.',
                                                    $key
                                                ));
        }

        return $form;
    }
}