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

use Core\Entity\Tree\NodeInterface;
use Core\Form\Hydrator\Strategy\TreeSelectStrategy;
use Core\Form\Tree\Select;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
//use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for a tree select form element.
 *
 * @author  Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.29
 * @TODO    [ZF3] Check if removed MutableCreationOptionsInterface affecting Yawik
 */
class SelectFactory implements FactoryInterface
{

    /**
     * Creation options.
     *
     * @var array
     */
    protected $options = [];


    /**
     * Creates a tree select form element
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Select
     * @throws \RuntimeException
     * @throws \DomainException
     */
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

        $valueOptions = $this->createValueOptions($root, isset($options['allow_select_nodes']) && $options['allow_select_nodes']);
        if (!isset($options['use_root_item']) || !$options['use_root_item']) {
            $valueOptions = array_shift($valueOptions);
            $valueOptions = is_array($valueOptions) ? $valueOptions['options'] : [];
        }
        $select->setValueOptions($valueOptions);

        $strategy = new TreeSelectStrategy();
        $strategy->setTreeRoot($root);
        $strategy->setAllowSelectMultipleItems(function () use ($select) {
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
     * Create a tree select form element.
     *
     * @internal
     *      proxies to {@link __invoke}.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Select
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var \Zend\ServiceManager\AbstractPluginManager $serviceLocator */
        $select = $this($serviceLocator, self::class, $this->options);
        $this->options = [];

        return $select;
    }

    /**
     * Create value options from a node.
     *
     * @param NodeInterface $node
     * @param bool $allowSelectNodes
     * @param bool $isRoot
     *
     * @return array
     */
    protected function createValueOptions(NodeInterface $node, $allowSelectNodes = false, $isRoot=true)
    {
        $key    = $isRoot ? $node->getValue() : $node->getValueWithParents();
        $name   = $node->getName();

        if ($node->hasChildren()) {
            $leafOptions = [];

            if ($allowSelectNodes && !$isRoot) {
                $leafOptions[$key] = $name;
                $key = "$key-group";
            }

            foreach ($node->getChildren() as $child) {
                $leafOptions += $this->createValueOptions($child, $allowSelectNodes, false);
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
