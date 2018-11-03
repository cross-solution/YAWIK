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

use Core\Form\ViewPartialProviderInterface;
use Core\Form\Element\Select;

/**
 * Form select element to select hiring organizations to be associated to a job
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class HiringOrganizationSelect extends OrganizationSelect implements ViewPartialProviderInterface
{
    /**
     * View partial name.
     *
     * @var string
     */
    protected $partial = 'jobs/form/hiring-organization-select';

    public function setViewPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }

    public function getViewPartial()
    {
        return $this->partial;
    }
}
