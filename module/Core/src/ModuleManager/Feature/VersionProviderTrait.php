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
    private $vptComposerData;

    public function getName(): string
    {
        if (defined('static::NAME')) {
            return static::NAME;
        }

        return $this->vptGetComposerData('name') ?? (new \ReflectionClass($this))->getNamespaceName();
    }

    public function getVersion(): string
    {
        $version = defined('static::VERSION') ? static::VERSION : 'n/a';

        if ('production' != getenv('APPLICATION_ENV')) {
            $path = dirname((new\ReflectionObject($this))->getFilename());

            if (strpos($path, 'vendor/') !== false) {
                return $version;
            }

            $command = sprintf(
                'cd %1$s && git describe %2$s && git rev-parse --abbrev-ref HEAD %2$s',
                $path,
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

    public function getUrl(): ?string
    {
        return $this->vptGetComposerData('homepage') ?? $this->vptGetComposerData('support.source');
    }

    private function vptParseComposerData()
    {
        if ($this->vptComposerData !== null) {
            return;
        }

        $reflClass = new \ReflectionClass($this);
        $psr4ComposerFile = dirname($reflClass->getFilename(), 2) . '/composer.json';
        $psr0ComposerFile = dirname($psr4ComposerFile, 2) . '/composer.json';

        $composerFile =
            file_exists($psr4ComposerFile)
            ? $psr4ComposerFile
            : (
                file_exists($psr0ComposerFile) ? $psr0ComposerFile : null
            )
        ;

        if ($composerFile === null) {
            $this->vptComposerData = [];
            return;
        }

        $composerJson = file_get_contents($composerFile);
        $composerJson = \Zend\Json\Json::decode($composerJson, \Zend\Json\Json::TYPE_ARRAY);

        $this->vptComposerData = $composerJson;
    }

    private function vptGetComposerData(string $key, $default = null)
    {
        $this->vptParseComposerData();

        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $data = $this->vptComposerData;
            foreach ($keys as $k) {
                if (!isset($data[$k])) {
                    return $default;
                }
                $data = $data[$k];
            }
            return $data;
        }

        return $this->vptComposerData[$key] ?? $default;
    }
}
