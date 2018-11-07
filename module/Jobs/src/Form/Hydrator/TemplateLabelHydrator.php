<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator;

use Jobs\Entity;
use Core\Entity\Hydrator\EntityHydrator;
use Organizations\Repository;

class TemplateLabelHydrator extends EntityHydrator
{
    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    /**
     * @inheritdoc
     */
    public function extract($object)
    {
        $data = array();

        /** @var Entity\Job $object */
        if ($object->getOrganization()) {
            $data['description-label-requirements'] = $object->getOrganization()->getTemplate()->getLabelRequirements();
            $data['description-label-qualifications'] = $object->getOrganization()->getTemplate()->getLabelQualifications();
            $data['description-label-benefits'] = $object->getOrganization()->getTemplate()->getLabelBenefits();
        }
        return $data;
    }

    public function hydrate(array $data, $object)
    {
        $object = parent::hydrate($data, $object);
        /* @var \Organizations\Entity\Template  $template */
        $template=$object->getOrganization()->getTemplate();
        if (isset($data['description-label-requirements'])) {
            $template->setLabelRequirements($data['description-label-requirements']);
        }
        if (isset($data['description-label-qualifications'])) {
            $template->setLabelQualifications($data['description-label-qualifications']);
        }
        if (isset($data['description-label-benefits'])) {
            $template->setLabelBenefits($data['description-label-benefits']);
        }
        return $object;
    }
}
