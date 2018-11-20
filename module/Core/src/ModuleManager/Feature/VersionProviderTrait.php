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
 * Implementation of the {@link VersionProviderInterface}
 *
 * Name retrieval:
 * * Uses a constant 'NAME', if defined in the class.
 * * Tries to load the composer.json file located in the modules' root directory,
 *   if the module adheres to the PSR-4 standard. Reads the composer.json and
 *   uses its 'name' attribute.
 * * If all of the above fails, it returns the namespace of the consuming class.
 *
 * Version retrieval in production environment:
 * * returns the value of the constant 'VERSION' - if defined - or 'n/a'
 *
 * Version retrieval in development environment:
 * * Tries to run the command 'git describe' in the module directory.
 *   If that fails, behaves like in production environment.
 * * Use the output of git describe and additionally tries to get the
 *   name of the current branch.
 *
 * @var VersionProviderInterface self
 * @const VERSION
 * @const NAME
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
trait VersionProviderTrait
{
    public function getName()
    {
        if (defined('static::NAME')) {
            return static::NAME;
        }

        $reflClass = new \ReflectionClass($this);
        $composerFile = dirname(dirname($reflClass->getFilename())) . '/composer.json';

        if (file_exists($composerFile)) {
            $composerJson = file_get_contents($composerFile);
            $composerData = \Zend\Json\Json::decode($composerJson);
            if (isset($composerData->name)) {
                return $composerData->name;
            }
        }

        return $reflClass->getNamespaceName();
    }

    public function getVersion()
    {
        $version = defined('static::VERSION') ? static::VERSION : 'n/a';

        if ('production' != getenv('APPLICATION_ENV')) {
            $command = sprintf(
                'cd %1$s && git describe %2$s && git rev-parse --abbrev-ref HEAD %2$s',
                dirname((new\ReflectionObject($this))->getFilename()),
                '2>/dev/null'
            );

            exec($command, $output);

            if (!empty($output)) {
                $version = sprintf(
                    '%s%s',
                    str_replace('-g', '@', $output[0]),
                    isset($output[1]) ? ' [' . $output[1] . ']' : ''
                );
            }
        }

        return $version;
    }
}
