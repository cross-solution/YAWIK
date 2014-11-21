<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator;

use Core\Entity\Hydrator\EntityHydrator;

use Zend\Stdlib\Hydrator\AbstractHydrator;
use Core\Entity\EntityInterface;

// @TODO The JobdescriptionHydrator is used for every Form in the Description, this produces some overhead,
// @TODO correctly their should be one Hydrator for every Form
class JobDescriptionHydrator extends EntityHydrator
{

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    protected function init()
    {
        $this->addStrategy('descriptionrequirements', new Strategy\JobDescriptionRequirementsStrategy());
        $this->addStrategy('descriptionbenefits', new Strategy\JobDescriptionBenefitsStrategy());
        $this->addStrategy('descriptionqualifications', new Strategy\JobDescriptionQualificationsStrategy());
        $this->addStrategy('descriptiontitle', new Strategy\JobDescriptionTitleStrategy());
    }

    /* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\HydratorInterface::extract()
     */
    public function extract ($object)
    {
        $data = parent::extract($object);
        $data['description-requirements']  = $this->extractValue('descriptionrequirements', $object);
        $data['description-benefits']      = $this->extractValue('descriptionbenefits', $object);
        $data['description-qualifications'] = $this->extractValue('descriptionqualifications', $object);
        $data['description-title']         = $this->extractValue('descriptiontitle', $object);
        return $data;
    }

    public function hydrate (array $data, $object)
    {
        $object = parent::hydrate($data, $object);
        $this->hydrateValue('descriptionrequirements', $data, $object);
        $this->hydrateValue('descriptionbenefits', $data, $object);
        $this->hydrateValue('descriptionqualifications', $data, $object);
        $this->hydrateValue('descriptiontitle', $data, $object);
        return $object;
    }
}
