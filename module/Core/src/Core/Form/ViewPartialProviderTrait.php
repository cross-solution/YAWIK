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
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
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
        return $this->partial;
    }
}