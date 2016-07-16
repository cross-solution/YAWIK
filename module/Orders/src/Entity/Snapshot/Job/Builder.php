<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Entity\Snapshot\Job;

use Core\Entity\EntityInterface;
use Core\Exception\MissingDependencyException;
use Jobs\Entity\JobInterface;
use Orders\Entity\Snapshot\BuilderInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Builder implements BuilderInterface
{
    /**
     * The Source interface
     *
     * @var JobInterface
     */
    protected $source;

    /**
     * The target entity
     *
     * @var |Orders\Entity\Snapshot\Job\JobSnapshot
     */
    protected $target = '\Orders\Entity\Snapshot\Job\JobSnapshot';

    public function setTargetEntity($entity)
    {
        $this->target = $entity;

        return $this;
    }

    public function getTargetEntity()
    {
        if (is_object($this->target)) {
            return clone $this->target;
        }

        $class = $this->target;

        if (class_exists($class, true)) {
            return new $class();
        }

        throw new MissingDependencyException('target', $this);
    }

    public function build(EntityInterface $entity)
    {
        $target = $this->getTargetEntity();
        $this->target = $target;
        $this->source = $entity;

        $this->copySimpleValues();
        $this->copyOrganization();
        $this->copyLocations();
        $this->copyAtsMode();

        $target->setJob($entity);

        $this->source = null;

        return $target;
    }

    protected function copySimpleValues()
    {
        $properties = [
            'Title', 'Reference', 'Language', 'Link', 'DatePublishStart',
            'UriApply', 'ApplyId', 'UriPublisher',
        ];

        foreach ($properties as $property) {
            $getter = "get$property";
            $setter = "set$property";

            $this->target->$setter($this->source->$getter());
        }
    }

    protected function copyOrganization()
    {
        /* @var \Organizations\Entity\Organization $organization */
        $organization = $this->source->getOrganization();
        if (!$organization) {
            return;
        }

        $name = $organization->getName();
        $this->target->setOrganizationName($name);

        if ($organization->isHiringOrganization()) {
            $parent = $organization->getParent()->getName();
            $this->target->setOrganizationParent($parent);
        }
    }

    protected function copyLocations()
    {
        $locations = $this->source->getLocations();
        $copy = [];

        foreach ($locations as $location) {
            $copy[] = [
                'city' => $location->getCity(),
                'region' => $location->getRegion(),
                'zipCode' => $location->getPostalCode(),
                'country' => $location->getCountry(),
                'coordinates' => $location->getCoordinates()->getCoordinates(),
            ];
        }

        $this->target->setLocations($copy);
    }

    protected function copyAtsMode()
    {
        $atsMode = $this->source->getAtsMode();

        $copy = [
            'mode' => $atsMode->getMode(),
            'uri' => $atsMode->isUri() ? $atsMode->getUri() : '',
            'email' => $atsMode->isEmail() ? $atsMode->getEmail() : '',
        ];

        $this->target->setAtsMode($copy);
    }


}