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

use Core\Entity\DraftableEntityInterface;
use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

/**
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 */
class MultipostFieldset extends Fieldset
{

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    /**
     * @throws \RuntimeException
     */
    public function init()
    {
        $this->setAttribute('id', 'jobportals-fieldset');
        $this->setName('jobPortals');

        $this->add(
            array(
                 'type' => 'Jobs/MultipostingSelect',
                 'property' => true,
                 'name' => 'portals',
                 'options' => array(
                     'label' => /*@translate*/ 'Portals',
                 ),
             )
        );
    }

    public function setObject($object)
    {
        if ($object instanceof DraftableEntityInterface && !$object->isDraft()) {
            foreach ($this as $element) {
                $element->setAttribute('disabled', 'disabled');
            }
        }
        return parent::setObject($object);
    }
}
