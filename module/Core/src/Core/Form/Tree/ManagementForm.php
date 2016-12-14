<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form\Tree;

use Core\Form\HeadscriptProviderInterface;
use Core\Form\SummaryForm;


/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ManagementForm extends SummaryForm implements HeadscriptProviderInterface
{

    protected $baseFieldset = 'Core/Tree/ManagementFieldset';

    protected $scripts = [ 'Core/js/forms.tree-management.js' ];

    protected $attributes = [
        'method' => 'POST', /* keep default value from \Zend\Form\Form */
        'class' => 'yk-tree-management-form',
    ];

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
}