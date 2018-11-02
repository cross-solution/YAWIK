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

use Core\Form\HeadscriptProviderInterface;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\Element\Select;

/**
 * Form select element to select channels on which job openings should be posted
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class MultipostingSelect extends Select implements ViewPartialProviderInterface, HeadscriptProviderInterface
{
    /**
     * View partial name.
     *
     * @var string
     */
    protected $partial = 'jobs/form/multiposting-select';

    /**
     * Headscripts to inject in view
     *
     * @var array
     */
    protected $headscripts = array(
        'modules/Jobs/js/form.multiposting-select.js'
    );

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
