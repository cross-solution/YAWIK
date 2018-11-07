<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** */
namespace Core\ModuleManager;

use Zend\Stdlib\Glob;
use Zend\Stdlib\ArrayUtils;

/**
 * Simple module configuration file loader and merger.
 *
 * Helps keeping module configuration in multiple files.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @copyright (c) 2013-2015 CrossSolution <http://cross-solution.de>
 */
class ModuleConfigLoader
{
    protected $directory;
    protected $config;
    
    /**
     *
     */
    private function __construct()
    {
    }
    
    public static function load($directory)
    {
        $directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $env       = getenv('APPLICATION_ENV') ?: 'production';
        $pattern   = sprintf('%s{,*.}{config,%s}.php', $directory, $env);
        $config    = array();
        
        foreach (Glob::glob($pattern, Glob::GLOB_BRACE) as $file) {
            if (!is_readable($file)) {
                continue;
            }
            
            $cfg    = include $file;
            $config = ArrayUtils::merge($config, $cfg);
        }
        return $config;
    }
}
