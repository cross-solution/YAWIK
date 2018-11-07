<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Organizations\Form\Element;

use Core\Form\HeadscriptProviderInterface;
use Core\Form\ViewPartialProviderInterface;
use Zend\Form\Element\Text;

/**
 * Form element to allow entering an email address and click a invite button.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.19
 */
class InviteEmployeeBar extends Text implements HeadscriptProviderInterface, ViewPartialProviderInterface
{
    /**
     * Provide these scripts to the headscript view helper
     *
     * @var array
     */
    protected $headscripts = array(
        'modules/Organizations/js/form.invite-employee.js'
    );

    /**
     * View partial name
     *
     * @var string
     */
    protected $partial = 'organizations/form/invite-employee-bar';

    public function setViewPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }

    public function getViewPartial()
    {
        return $this->partial;
    }

    public function setHeadscripts(array $scripts)
    {
        $this->headscripts = $scripts;

        return $this;
    }

    public function getHeadscripts()
    {
        return $this->headscripts;
    }
}
