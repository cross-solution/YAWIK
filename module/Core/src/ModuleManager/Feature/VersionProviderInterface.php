<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\ModuleManager\Feature;

/**
 * Marks the implementing class as a VersionProvider.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface VersionProviderInterface 
{
    /**
     * Get the name of the module.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the version of the module.
     *
     * @return string
     */
    public function getVersion();
}
