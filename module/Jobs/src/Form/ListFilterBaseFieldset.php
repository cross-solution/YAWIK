<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ListFilterBaseFieldset.php */
namespace Jobs\Form;

use Jobs\Entity\Status;
use Zend\Form\Fieldset;

/**
 * Defines the formular fields of the job opening search formular. The ListFilterBaseFieldset contains the fulltext
 * search
 *
 * @package Jobs\Form
 */
class ListFilterBaseFieldset extends Fieldset
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function init()
    {
        $this->setName('params');
        
        $this->add(
            array(
            'type' => 'Hidden',
            'name' => 'page',
            'attributes' => array(
                'value' => 1,
            )
            )
        );
        
        $this->add(
            array(
            'name' => 'search',
            'options' => array(
                'label' => /*@translate*/ 'Job title',
            ),
            )
        );
    }
}
