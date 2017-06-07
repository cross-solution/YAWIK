<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;
use Core\Entity\Hydrator\MappingEntityHydrator;
use Core\Form\HydratorStrategyAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Jobs\Entity\Location;
use Jobs\Form\Hydrator\Strategy\LocationStrategy;
use Zend\Form\Exception;
use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\Form\FieldsetInterface;
use Core\Form\CustomizableFieldsetInterface;
use Core\Form\CustomizableFieldsetTrait;

/**
 * Defines the formular fields of the base formular of a job opening.
 */
class BaseFieldset extends Fieldset implements CustomizableFieldsetInterface
{
    use CustomizableFieldsetTrait;

    /**
     * @return \Zend\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new MappingEntityHydrator([
                'locations' => 'geoLocation'
            ]);

            $geoLocationIsMultiple = $this->get('geoLocation')->getAttribute('multiple', false);
            $geoLocationStrategy = $this->get('geoLocation')->getHydratorStrategy();

            $locationsStrategy = new \Zend\Hydrator\Strategy\ClosureStrategy(
                /* extract */
                function ($value) use ($geoLocationStrategy, $geoLocationIsMultiple)
                {
                    $value = $geoLocationIsMultiple ? $value : $value->first();

                    return $geoLocationStrategy->extract($value);
                },

                /* hydrate */
                function ($value) use ($geoLocationStrategy, $geoLocationIsMultiple)
                {
                    if ($geoLocationIsMultiple) {
                        return $geoLocationStrategy->hydrate($value);
                    }

                    return new ArrayCollection([$geoLocationStrategy->hydrate($value)]);
                }
            );

            $hydrator->addStrategy('locations', $locationsStrategy);

            /*
            $datetimeStrategy = new Hydrator\DatetimeStrategy();
            $datetimeStrategy->setHydrateFormat(Hydrator\DatetimeStrategy::FORMAT_MYSQLDATE);
            $hydrator->addStrategy('datePublishStart', $datetimeStrategy);
            */

            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function init()
    {
        $this->setAttribute('id', 'job-fieldset');

        $this->setName('jobBase');

        $this->add(
            [
                'type' => 'Text',
                'name' => 'title',
                'options' => [
                    'label' => /*@translate*/ 'Job title',
                    'description' => /*@translate*/ 'Please enter the job title'
                ],
            ]
        );


        $this->add(
            [
                'type' => 'LocationSelect',
                'name' => 'geoLocation',
                'options' => [
                    'label' => /*@translate*/ 'Location',
                    'description' => /*@translate*/ 'Please enter the location of the job',
                    'location_entity' => Location::class,
                    'summary_value' => [$this, 'getLocationsSummaryValue'],
                ],
                'attributes' => [
                    'data-width' => '100%',
                ]
            ]
        );

    }

    /**
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getLocationsSummaryValue()
    {
        $element = $this->get('geoLocation');
        $isMultiple = $element->getAttribute('multiple', false);

        $values = [];
        foreach ($this->object->getLocations() as $loc) {
            $values[] = trim(
                $loc->getPostalCode() . ' ' . $loc->getCity() . ', ' . $loc->getRegion(),
                ' ,'
            );
        }

        if (count($values)) {
            if ($isMultiple) {
                return '<ul><li>' . join('</li><li>', $values) . '</li></ul>';
            } else {
                return $values[0];
            }
        } else {
            return '';
        }
    }
}

