<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Form;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the Multiposting select box
 * 
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class MultipostingMultiCheckboxFactory extends MultipostingSelectFactory
{
    /**
     * Creates the multiposting select box.
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $select = parent::createService($serviceLocator);
        $select->setViewPartial('jobs/form/multiposting-checkboxes');
        $select->setHeadscripts(array('Jobs/js/form.multiposting-checkboxes.js'));

        return $select;
    }
}