<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Core\Form;

use Core\Form\Hydrator\MetaDataHydrator;
use Laminas\Form\Fieldset;

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