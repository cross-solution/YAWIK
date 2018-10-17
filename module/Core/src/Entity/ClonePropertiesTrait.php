<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity;

use Core\Entity\Collection\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
trait ClonePropertiesTrait
{
    public function __clone()
    {
        $this->cloneProperties();
    }

    private function cloneProperties(array $properties = null)
    {
        if (null === $properties) {
            $properties = isset($this->cloneProperties) ? $this->cloneProperties : [];
        }

        foreach ($properties as $property) {
            if (0 === strpos($property, '!')) {
                $property = substr($property, 1);
                $loop = false;
            } else {
                $loop = true;
            }

            $value = $this->{$property};

            if (!is_object($value)) {
                continue;
            }

            if ($value instanceof Collection && $loop) {
                $collection = new ArrayCollection();
                foreach ($value as $item) {
                    $collection->add(clone $item);
                }
                $value = $collection;
            } elseif (null === $value) {
            } else {
                $value = clone $value;
            }

            $this->{$property} = $value;
        }

        if ($this instanceof IdentifiableEntityInterface) {
            $this->setId(null);
        }

        if (is_callable('parent::__clone')) {
            /** @noinspection PhpUndefinedMethodInspection */
            parent::__clone();
        }
    }
}
