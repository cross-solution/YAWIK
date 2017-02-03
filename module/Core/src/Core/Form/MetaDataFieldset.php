<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form;

use Core\Form\Hydrator\MetaDataHydrator;
use Zend\Form\Fieldset;

/**
 * Fieldset for Entity Meta Data.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class MetaDataFieldset extends Fieldset
{
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new MetaDataHydrator());
        }

        return $this->hydrator;
    }
}