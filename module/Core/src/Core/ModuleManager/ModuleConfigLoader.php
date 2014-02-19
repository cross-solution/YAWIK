<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** */ 
namespace Core\ModuleManager;

/**
 * Simple module configuration file loader and merger.
 * 
 * Helps keeping module configuration in multiple files.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @copyright (c) 2013-2014 CrossSolution <http://cross-solution.de>
 */
class ModuleConfigLoader
{
    /**
     * Static class
     */
    private function __construct()
    { }
    
    
    public static function load($directory, array $handlers = array())
    {
        $directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $config = array();
        foreach (new \DirectoryIterator($directory) as $file) {
            if ($file->isDir() || $file->isDot() || !$file->isReadable()
                || !preg_match('~^(.*)\.([^\.]+)\.php$~', $file->getFilename(), $match)) {
                continue;
            }
            
            $handler = $match[2];
            $cfg = include $directory . $file->getFilename();
            if (isset($handlers[$handler]) && is_callable($handlers[$handler])) {
                $cfg = call_user_func($handlers[$handler], $match[1], $cfg);
            } else if ('config' == $handler) {
                $cfg = array($match[1] => $cfg);
            }
                
            $config = array_merge($config, $cfg);
        }
        return $config;
    } 
}

