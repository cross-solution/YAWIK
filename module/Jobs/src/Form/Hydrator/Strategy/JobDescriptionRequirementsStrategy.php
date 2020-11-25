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

class JobDescriptionRequirementsStrategy implements StrategyInterface
{
    public function extract($value, ?object $object = null)
    {
        /* @var \Jobs\Entity\Job $value */
        $result = null;
        if (method_exists($value, 'getTemplateValues')) {
            $result = $value->getTemplateValues()->getRequirements();
        }
        return $result;
    }

    public function hydrate($value, ?array $data)
    {
        /* @var \Jobs\Entity\Job $object */
        if (isset($value['description-requirements'])) {
            $object->getTemplateValues()->setRequirements($value['description-requirements']);
        }
        return;
    }
}
