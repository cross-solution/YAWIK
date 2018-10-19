<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Console;

/**
 * Interface ConsoleCommandProviderInterface
 * @package Core\Console
 */
interface ConsoleCommandProviderInterface
{
    /**
     * Register console command into application
     * @param Application $application
     * @return mixed
     */
    public function registerCommands(Application $application);
}
