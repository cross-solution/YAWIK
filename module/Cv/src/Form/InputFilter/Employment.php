<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Cv\Form\InputFilter;

use Laminas\InputFilter\InputFilter;

/**
 *
 * @author fedys
 * @since 0.26
 */
class Employment extends InputFilter
{

    /**
     *
     * @see \Laminas\InputFilter\BaseInputFilter::setData()
     */
    public function setData($data)
    {
        $this->add([
            'name' => 'endDate',
            'required' => ! $data['currentIndicator']
        ]);
        
        return parent::setData($data);
    }
}
