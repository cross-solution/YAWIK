<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Form\BaseForm;
use Core\Entity\Hydrator\EntityHydrator;


class Multipost extends BaseForm
{
    protected $baseFieldset = 'Jobs/MultipostFieldset';
    protected $label = /*@translate*/ 'Multiposting';

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    /**
     * overwrite this method to do nothing
     * this blends out the Buttons
     */
    protected function addButtonsFieldset()
    {
    }
}