<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the Multiposting select box
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class MultipostingMultiCheckboxFactory implements FactoryInterface
{
    /**
     * The parent factory
     *
     * @var FactoryInterface
     */
    protected $parent;

    /**
     * Sets the parent factory
     *
     * @param FactoryInterface $factory
     *
     * @return self
     */
    public function setParentFactory(FactoryInterface $factory)
    {
        $this->parent = $factory;

        return $this;
    }

    /**
     * Gets the parent factory.
     *
     * @return FactoryInterface
     */
    public function getParentFactory()
    {
        if (!$this->parent) {
            $this->setParentFactory(new MultipostingSelectFactory());
        }

        return $this->parent;
    }

    /**
     * Creates the multiposting select box.
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $factory = $this->getParentFactory();
        $select = $factory->createService($serviceLocator);
        $select->setViewPartial('jobs/form/multiposting-checkboxes');
        $select->setHeadscripts(array('Jobs/js/form.multiposting-checkboxes.js'));

        return $select;
    }
}
