<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form;

/**
 * Basic implementation of the ViewPartialProviderInterface
 *
 * Since using classes cannot redefine class properties, we
 * work around by checking for an property called "defaultPartial", when no
 * partial is set.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @property string $defaultPartial
 */
trait ViewPartialProviderTrait
{
    /**
     * The partial name.
     *
     * @var string
     */
    protected $partial;

    public function setViewPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }

    public function getViewPartial()
    {
        if (!$this->partial && property_exists($this, 'defaultPartial')) {
            $this->setViewPartial($this->defaultPartial);
        }

        return $this->partial;
    }
}
