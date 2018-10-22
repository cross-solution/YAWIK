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
use Zend\Form\Fieldset;

/**
 * Form for tree management
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class ManagementForm extends SummaryForm implements HeadscriptProviderInterface
{

    /**
     * Base fieldset.
     *
     * @var string|Fieldset
     */
    protected $baseFieldset = 'Core/Tree/ManagementFieldset';

    /**
     * Headscripts.
     *
     * @var array
     */
    protected $scripts = [ 'modules/Core/js/html.sortable.min.js', 'modules/Core/js/forms.tree-management.js' ];

    /**
     * Attributes.
     *
     * @var array
     */
    protected $attributes = [
        'method' => 'POST', /* keep default value from \Zend\Form\Form */
        'class' => 'yk-tree-management-form',
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
}
