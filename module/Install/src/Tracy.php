<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Install;

use Tracy\Debugger;
use Tracy\Logger;

/**
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @since 0.28
 * @package Core\Service
 */
class Tracy
{
    /**
     * @param array $config
     */
    public function register(array $config)
    {
    	try{
		    // enable logging of all error types globally
		    Debugger::enable($config['mode'], $config['log'], $config['email']);
		    Debugger::$strictMode = $config['strict'];
		    Debugger::$showBar = $config['bar'];
		
		    /** @var \Tracy\Logger $logger */
		    $logger = Debugger::getLogger();
		    $logger->emailSnooze = $config['email_snooze'];
	    }catch (\Exception $e){
    		throw $e;
	    }
     
    }
}
