<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Form\Container;
use Core\Form\ViewPartialProviderInterface;

/**
 * Adds an iFrame to the job formular which holds the editable job opening.
 *
 * @package Jobs\Form
 */
class JobDescription extends Container implements ViewPartialProviderInterface
{
    public function init()
    {
        $this->setName('jobs-form-description');
        $this->setAttributes(
            array(
            'id' => 'jobs-form-description',
            'data-handle-by' => 'native'
            )
        );

        $this->setForms(
            array(
            'atsMode' => array(
                'type' => 'Jobs/AtsMode',
                'property' => 'atsMode',
            )
            )
        );
    }

    public function setViewPartial($partial)
    {
        return $this;
    }

    public function getViewPartial()
    {
        return 'iframe/iFrame.phtml';
    }
}
