<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator;

use Core\Entity\Hydrator\EntityHydrator;

// @TODO The JobDescriptionHydrator is used for every Form in the Description, this produces some overhead,
// @TODO correctly their should be one Hydrator for every Form
class JobDescriptionHydrator extends EntityHydrator
{
    /* (non-PHPdoc)
     * @see \Laminas\Hydrator\HydratorInterface::extract()
     */
    public function extract($object)
    {
        $data = parent::extract($object);
        if (!method_exists($object, 'getTemplateValues')) {
            return $data;
        }

        /** @var \Jobs\Entity\TemplateValues $values */
        $values = $object->getTemplateValues();
        $data['description-description']    = $values->getDescription();
        $data['description-requirements']   = $values->getRequirements();
        $data['description-benefits']       = $values->getBenefits();
        $data['description-qualifications'] = $values->getQualifications();
        $data['description-title']          = $values->getTitle();
        $data['description-html']           = $values->getHtml();

        return $data;
    }

    public function hydrate(array $data, $object)
    {
        $object = parent::hydrate($data, $object);

        if (!method_exists($object, 'getTemplateValues')) {
            return $object;
        }

        $values = $object->getTemplateValues();

        $this->hydrateTemplateValue('description', $data, $values);
        $this->hydrateTemplateValue('requirements', $data, $values);
        $this->hydrateTemplateValue('benefits', $data, $values);
        $this->hydrateTemplateValue('qualifications', $data, $values);
        $this->hydrateTemplateValue('title', $data, $values);
        $this->hydrateTemplateValue('html', $data, $values);

        return $object;
    }

    private function hydrateTemplateValue($name, $data, $object)
    {
        $key = "description-$name";
        $setter = "set$name";

        if (isset($data[$key])) {
            $object->$setter($data[$key]);
        }
    }
}
