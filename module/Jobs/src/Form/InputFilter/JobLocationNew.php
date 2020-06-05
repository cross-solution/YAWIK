<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** Job.php */
namespace Jobs\Form\InputFilter;

class JobLocationNew extends JobLocationEdit
{

    public function init()
    {
        parent::init();
        $input = $this->get('applyId')
                      ->getValidatorChain()
                      ->attachByName('Jobs/Form/UniqueApplyId');
    }
}
