<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** GetText.php */
namespace Geo\Form;

use Geo\Form\GeoText\Converter;
use Jobs\Entity\Location;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Text;
use Core\Form\ViewPartialProviderInterface;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\FormInterface;

/**
 * Class GeoText
 *
 * @package Geo\Form
 */
class GeoText extends Text implements ViewPartialProviderInterface, ElementPrepareAwareInterface
{
    
    protected $partial = 'geo/form/GeoText';

    protected $nameElement;
    protected $dataElement;
    protected $typeElement;
    protected $converter;
    protected $filter;

    /**
     * Creates the GeoText element
     *
     * @param null  $name
     * @param array $options
     */
    public function __construct($name = null, array $options = null)
    {
        $this->nameElement = new Text('name');
        $this->dataElement = new Hidden('data');
        $this->typeElement = new Hidden('type');

        parent::__construct($name, $options);
    }

    /**
     * Sets options of the GeoText element.
     *
     * @param array|\Traversable $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['engine_type'])) {
            $this->setType($options['engine_type']);
        }

        return $this;
    }

    /**
     * Sets the converter
     *
     * @param $converter
     *
     * @return $this
     */
    public function setConverter($converter)
    {
        $this->converter = $converter;

        return $this;
    }

    /**
     * Sets the filter
     *
     * @param $filter
     *
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Gets the converter
     *
     * @return mixed
     */
    public function getConverter()
    {
        if (!$this->converter) {
            $this->setConverter(new Converter());
        }
        return $this->converter;
    }

    /**
     * Gets the view partial
     *
     * @return string
     */
    public function getViewPartial()
    {
        return $this->partial;
    }

    /**
     * Sets the view partial
     *
     * @param String $partial
     *
     * @return $this
     */
    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }

    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param FormInterface $form
     *
     * @return mixed
     */
    public function prepareElement(FormInterface $form)
    {
        $name = $this->getName();
        $id = str_replace(array('[', ']'), array('-', ''), $name);
        $this->setAttribute('id', $id);
        $this->nameElement->setName($name . '[name]')
                          ->setAttribute('id', $id . '-name')
                          ->setAttribute('class', 'form-control geolocation');
        $this->dataElement->setName($name . '[data]')
                          ->setAttribute('id', $id . '-data');
        $this->typeElement->setName($name . '[type]')
                          ->setAttribute('id', $id . '-type');
    }

    /**
     * Gets the data of an element
     *
     * @return mixed
     */
    public function getDataElement()
    {
        return $this->dataElement;
    }

    /**
     * Gets the name of an element
     *
     * @return mixed
     */
    public function getNameElement()
    {
        return $this->nameElement;
    }

    /**
     * Gets the type of an element
     *
     * @return Hidden
     */
    public function getTypeElement()
    {
        return $this->typeElement;
    }

    /**
     * Sets the type of an element
     *
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->typeElement->setValue($type);

        return $this;
    }

    /**
     * Sets the value of an element
     *
     * @param mixed $value
     * @param null  $type
     *
     * @return $this
     */
    public function setValue($value, $type=null)
    {
        if ($value instanceOf Location) {
            $value = $this->getConverter()->toValue($value, $type ?: $this->typeElement->getValue());
        }
        if ('geo' == $value['type']) {
            $lonLat = $this->getConverter()->toCoordinates($value['name']);
            foreach($lonLat as $k=>$v) {
                 list($lon,$lat) = explode(',', $v, 2);
                 $latLon[]=$lat.','.$lon;
            }

            $lon = $lat = 0;

            $value['data'] = [
                'coordinates'=>[
                    (float) $lat,
                    (float) $lon
                    ],
                'type'=>'Point',
                'city' => substr($value['name'],0,strrpos($value['name'], ',' )),
                'region' =>  substr($value['name'],strrpos($value['name'], ',' )+2),
                'postalcode' =>'',
                'country' => 'DE'];
        }
        if (!is_array($value)) {
            $value = explode('|', $value, 2);
            $value = [
                'name' => $value[0],
                'data' => isset($value[1]) ? $value[1] : '',
            ];
        }

        $this->nameElement->setValue($value['name']);
        $this->dataElement->setValue($value['data']);

        return $this;
    }

    /**
     * Gets the value of an element
     *
     * @param string $type
     *
     * @return array|mixed
     */
    public function getValue($type = 'name')
    {
        switch ($type) {
            case 'entity':
            default:
                return $this->getConverter()->toEntity($this->dataElement->getValue(), $this->typeElement->getValue());
                break;

            case 'all':
                return [
                    'name' => $this->nameElement->getValue(),
                    'data' => $this->dataElement->getValue(),
                    'type' => $this->typeElement->getValue(),
                ];
                break;

            case 'name':
                return $this->nameElement->getValue();
                break;

            case 'data':
                return $this->dataElement->getValue();
                break;

            case 'type':
                return $this->typeElement->getValue();
        }
    }
}