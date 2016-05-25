<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
