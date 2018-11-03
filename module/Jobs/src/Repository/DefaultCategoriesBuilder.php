<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Repository;

use Jobs\Entity\Category;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class DefaultCategoriesBuilder
{

    /**
     * The module config path.
     *
     * @var string
     */
    private $moduleConfigPath;

    /**
     * The global config paths.
     *
     * @var string[]
     */
    private $globalConfigPaths;

    /**
     * Prototype of a Category
     *
     * @var Category
     */
    private $categoryPrototype;

    public function __construct($moduleConfigPath, array $globalConfigPaths, Category $categoryPrototype)
    {
        $this->moduleConfigPath = $moduleConfigPath;
        $this->globalConfigPaths = $globalConfigPaths;
        $this->categoryPrototype = $categoryPrototype;
    }
    
    public function build($type)
    {
        $categories = $this->loadCategoriesConfig($type);
        
        if (!is_array($categories)) {
            $categories = ['name' => $type, 'value' => $type];
        }
        
        $category = $this->buildCategory($categories);

        return $category;
    }
    
    private function loadCategoriesConfig($type)
    {
        $file = "jobs.categories.$type.php";

        foreach ($this->globalConfigPaths as $path) {
            if (file_exists($path . $file)) {
                return include $path . $file;
            }
        }

        if (file_exists($this->moduleConfigPath . $file)) {
            return include $this->moduleConfigPath . $file;
        }

        return false;
    }

    private function buildCategory($spec)
    {
        if (is_string($spec)) {
            $spec = ['name' => $spec];
        }

        $category = clone $this->categoryPrototype;
        $category
            ->setName(isset($spec['name']) ? $spec['name'] : '')
            ->setValue(isset($spec['value']) ? $spec['value'] : '')
        ;

        if (isset($spec['children']) && is_array($spec['children'])) {
            foreach ($spec['children'] as $childSpec) {
                $child = $this->buildCategory($childSpec);
                $category->addChild($child);
            }
        }

        return $category;
    }
}
