<?php declare(strict_types=1);
/**
 * YAWIK
 *
 * @filesource
 * @copyright 2019 CROSS Solution <https://www.cross-solution.de>
 * @license MIT
 */

namespace Core\View\Helper;

use Core\ModuleManager\Feature\VersionProviderInterface;
use OutOfBoundsException;
use Zend\View\Helper\AbstractHelper;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * TODO: write tests
 */
class ModuleVersion extends AbstractHelper implements VersionProviderInterface
{
    /** @var array */
    private $modules;

    /** @var VersionProviderInterface */
    private $module;

    public function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @return string|self|null
     */
    public function __invoke(string $module = 'Core'): ModuleVersion
    {
        if ($module) {
            $this->setModule($module);
        }

        return $this;
    }

    public function setModule(string $module): ModuleVersion
    {
        if (!isset($this->modules[$module])) {
            throw new OutOfBoundsException('Unknown module ' . $module);
        }

        $this->module = $this->modules[$module];

        return $this;
    }

    public function getVersion(): string
    {
        return $this->getVersionData('version');
    }

    public function getName(): string
    {
        return $this->getVersionData('name');
    }

    public function getUrl(): ?string
    {
        return $this->getVersionData('url');
    }

    private function getVersionData(string $key): ?string
    {
        $getter = "get$key";

        if ($this->module instanceof VersionProviderInterface
            || method_exists($this->module, $getter)
        ) {
            return $this->module->$getter();
        }

        return $key == 'url' ? null : '';
    }

    public function full(): string
    {
        $version = $this->getVersion();
        $name    = $this->getName();
        $url     = $this->getUrl();

        if ($url) {
            $name = sprintf('<a href="%s">%s</a>', $url, $name);
        }

        return sprintf('%s %s', $name, $version);
    }

    public function __toString(): string
    {
        return $this->getVersion();
    }
}
