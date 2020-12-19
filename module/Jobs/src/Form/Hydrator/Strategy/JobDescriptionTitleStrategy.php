<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

class JobDescriptionTitleStrategy implements StrategyInterface
{
    public function extract($value, ?object $object = null)
    {
        $result = null;
        if (method_exists($value, 'getTemplateValues')) {
            $result = $value->getTemplateValues()->getTitle();
        }
        return $result;
    }

    public function hydrate($value, ?array $object)
    {
        if (isset($value['description-title'])) {
            $object->getTemplateValues()->setTitle($value['description-title']);
        }
        return;
    }
}
