<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Form;

use Core\Form\ViewPartialProviderInterface;
use Zend\Form\Element\Select;

/**
 * Form select element to select channels on which job openings should be posted
 * 
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class MultipostingSelect extends Select implements ViewPartialProviderInterface
{
    /**
     * View partial name.
     *
     * @var string
     */
    protected $partial = 'jobs/form/multiposting-select';

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