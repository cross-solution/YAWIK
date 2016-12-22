<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Factory\Form\Tree;

use Core\Form\Hydrator\Strategy\TreeSelectStrategy;
use Core\Form\Tree\Select;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class SelectFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    protected $options = [];


    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!is_array($options)
            || !isset($options['tree']['entity'])
            || (!isset($options['tree']['value']) && !isset($options['tree']['name']))
        ) {
            throw new \DomainException('You must specify ["tree"]["entity"] and either ["tree"]["value"] or ["tree"]["name"]');
        }

        if (isset($options['tree']['value'])) {
            $criteria = ['value' => $options['tree']['value']];
        } else {
            $criteria = ['name' => $options['tree']['name']];
        }

        $root = $container
            ->get('repositories')
            ->get($options['tree']['entity'])
            ->findOneBy($criteria);

        if (!$root) {
            throw new \RuntimeException('Tree root not found');
        }

        $select = new Select();

        if (isset($options['name'])) {
            $select->setName($options['name']);
        }

        if (isset($options['options'])) {
            $select->setOptions($options['options']);
        }

        if (isset($options['attributes'])) {
            $select->setAttributes($options['attributes']);
        }

        $valueOptions = $this->createValueOptions($root);
        if (!isset($options['use_root_item']) || !$options['use_root_item']) {
            $valueOptions = array_shift($valueOptions);
            $valueOptions = is_array($valueOptions) ? $valueOptions['options'] : [];
        }
        $select->setValueOptions($valueOptions);

        $strategy = new TreeSelectStrategy();
        $strategy->setTreeRoot($root);
        $strategy->setAllowSelectMultipleItems(function() use ($select) {
                return $select->isMultiple();
        });

        $select->setHydratorStrategy($strategy);

        return $select;
    }

    /**
     * Set creation options
     *
     * @param  array $options
     *
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }


    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $select = $this($serviceLocator->getServiceLocator(), self::class, $this->options);
        $this->options = [];

        return $select;
    }

    /**
     *
     *
     * @param $leaf
     *
     * @return array
     */
    protected function createValueOptions($leaf, $isRoot=true)
    {
        $key    = $leaf->getValue();
        $name   = $leaf->getName();

        if ($leaf->hasChildren()) {
            $leafOptions = [];

            if (isset($this->options['allow_select_nodes']) && $this->options['allow_select_nodes'] && !$isRoot) {
                $leafOptions[$key] = $name;
                $key = "$key-group";
            }

            foreach ($leaf->getChildren() as $child) {
                $leafOptions += $this->createValueOptions($child, false);
            }

            $value = [
                'label' => $name,
                'options' => $leafOptions
            ];

        } else {
            $value = $name;
        }

        return [$key => $value];
    }
}