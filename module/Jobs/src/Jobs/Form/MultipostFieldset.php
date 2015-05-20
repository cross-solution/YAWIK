<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\Stdlib\ArrayUtils;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\propagateAttributeInterface;

class MultipostFieldset extends Fieldset implements propagateAttributeInterface
{

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    /**
     * @throws \RuntimeException
     */
    public function init()
    {
        // all necessary informations about the portals are provided in the configs
        $portals = array();
        $config = $this->getFormFactory()->getFormElementManager()->getServiceLocator()->get('Config');
        if (array_key_exists('multiposting',$config)) {
            $portals = ArrayUtils::merge($portals, $config['multiposting']['channels']);
        }

        $this->setAttribute('id', 'jobportals-fieldset');
        $this->setName('jobPortals');


        foreach ($portals as $key=>$portal) {
            if (empty($portal['label'])) {
                throw new \RuntimeException('missing label');
            }
            $options=$portal;
            $this->add(
                 array(
                     // at some point we need an own Element for additional specific information like duration or premiums
                     // InfoCheckbox is just a surrogate
                     //'type' => 'Jobs/portalsElement',
                     'type' => 'InfoCheckbox',
                     'property' => true,
                     'name' => $key,
                     'options' => $options,
                     'attributes' => array(
                         'data-trigger' => 'submit',
                     ),
                 )
            );
        }
    }

    public function enableAll($enable = true)
    {
        foreach ($this as $forms) {
            $forms->setAttribute('disabled', 'disabled');
        }
        return $this;
    }

    /**
     * @return array
     */
    protected function extract()
    {
        $object = $this->getObject();
        $values = $object->getPortals();
        $formValues = array();
        foreach ($values as $key => $value) {
            $formValues[$key] = $value['active'];
        }
        return $formValues;
    }

    public function bindValues(array $values = array())
    {
        $aggregatValues = $this->makeAggregateValues($values);
        $object = $this->getObject();
        $object->setPortals($aggregatValues);
        return $this->object;
    }

    /**
     * usual this function should write all values into the
     * @param array|\Traversable $data
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function populateValues($data)
    {
        if (!is_array($data) && !$data instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of data; received "%s"',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }

        $aggregateValues = $this->makeAggregateValues($data);
    }

    public function makeAggregateValues($data)
    {
        $aggregateValues = array();
        foreach ($data as $portalName => $portalValue) {
            $valueExists = array_key_exists($portalName, $this->byName);
            if (!$valueExists) {
                throw new Exception\InvalidArgumentException('value does not exist');
            }

            // set the element Values
            // @TODO set the element values in populateValues (that means untwisting them there)
            $element = $this->byName[$portalName];
            $element->setValue($data[$portalName]);
            $aggregateValues[$portalName] = array('active' => $portalValue, 'name' => $portalName);
        }
        return $aggregateValues;
    }

}