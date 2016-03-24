<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form;

use Zend\Form\Element;
use Zend\Stdlib\PriorityList;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class WizardContainer extends Container implements HeadscriptProviderInterface, \IteratorAggregate
{
    protected $tabs = [];
    protected $scripts = [
        '/js/jquery.bootstrapwizard.min.js',
    ];

    protected $tabContainerPrototype;

    /**
     * Sets the array of script names.
     *
     * @param string[] $scripts
     *
     * @return self
     */
    public function setHeadscripts(array $scripts)
    {
        $this->scripts = $scripts;

        return $this;
    }

    /**
     * Gets the array of script names.
     *
     * @return string[]
     */
    public function getHeadscripts()
    {
        return $this->scripts;
    }

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

    public function getForm($key, $asInstance = true)
    {
        $form = parent::getForm($key, $asInstance);

        /*
         * We must check here, if a lazy loaded top level form is an
         * instance of Container.
         */
        if ($asInstance && false === strpos($key, '.') && (!$form instanceOf Container || !$form->getLabel())) {
            throw new \UnexpectedValueException(sprintf(
                'The registered form with key "%s" is not an instance of \Core\Form\Container or does not have a label.', $key
            ));
        }

        return $form;
    }
}