<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Form\BaseForm;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\propagateAttributeInterface;


class Multipost extends BaseForm implements propagateAttributeInterface
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

    public function enableAll($enable = true)
    {
        foreach ($this as $forms) {
            if ($forms instanceof propagateAttributeInterface) {
                $forms->enableAll($enable);
            }
        }
        return $this;
    }
}