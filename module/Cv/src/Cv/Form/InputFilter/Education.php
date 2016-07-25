<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Cv\Form\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 *
 * @author fedys
 * @since 0.26
 */
class Education extends InputFilter
{

    /**
     *
     * @see \Zend\InputFilter\BaseInputFilter::setData()
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
