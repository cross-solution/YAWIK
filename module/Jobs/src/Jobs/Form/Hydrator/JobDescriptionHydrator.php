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
        $this->addStrategy('descriptionqualification', new Strategy\JobDescriptionQualificationsStrategy());
    }

    /* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\HydratorInterface::extract()
     */
    public function extract ($object)
    {
        $data = parent::extract($object);
        $data['descriptionrequirements']  = $this->extractValue('descriptionrequirements', $object);
        $data['descriptionbenefits']      = $this->extractValue('descriptionbenefits', $object);
        $data['descriptionqualification'] = $this->extractValue('descriptionqualification', $object);
        return $data;
    }

    public function hydrate (array $data, $object)
    {
        return $object;
    }
}
