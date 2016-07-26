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
 * Classes implementing this interface can hint to the FormElementManager to inject
 * javascripts to the Headscript view helper via an initializer.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.19
 */
interface HeadscriptProviderInterface
{

    /**
     * Sets the array of script names.
     *
     * @param string[] $scripts
     *
     * @return self
     */
    public function setHeadscripts(array $scripts);

    /**
     * Gets the array of script names.
     *
     * @return string[]
     */
    public function getHeadscripts();
}
