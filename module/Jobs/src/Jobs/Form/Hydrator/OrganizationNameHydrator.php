<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator;

use Jobs\Entity;
use Core\Entity\Hydrator\EntityHydrator;
use Organizations\Repository;
use Organizations\Entity\Organization;

class OrganizationNameHydrator extends EntityHydrator
{
    /**
     * @var Repository\Organization
     */
    private $organizationRepository;

    public function __construct(Repository\Organization $organizationRepository)
    {
        parent::__construct();

        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @inheritdoc
     */
    public function extract($object)
    {
        $data = array();

        /** @var Entity\Job $object */
        if ($object->getOrganization()) {
            $data['company'] = $object->getOrganization()->getOrganizationName()->getName();
            $data['companyId'] = $object->getOrganization()->getId();
        } else { // old versions
            $data['company'] = $object->getCompany();
        }

        return $data;
    }

    public function hydrate(array $data, $object)
    {
        /** @var Entity\Job $object */
        $object = parent::hydrate($data, $object);

        /** @var Organization $organization */
        if (($organization = $this->organizationRepository->find($data['companyId']))) {
            $object->setOrganization($organization);
        } else {
            $object->setOrganization(null);
        }

        return $object;
    }
}