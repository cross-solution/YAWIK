<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Controller\Plugin;

use Core\Entity\SnapshotGeneratorProviderInterface;
use Core\Service\SnapshotGenerator;
use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Stdlib\ArrayUtils;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Repository\RepositoryInterface;

/**
 * Class EntitySnapshot
 * @package Core\Controller\Plugin
 */
class EntitySnapshot implements PluginInterface
{
    /**
     * @var
     */
    protected $serviceLocator;

    /**
     * @var RepositoryInterface
     */
    protected $repositories;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var
     */
    protected $entity;

    /**
     * @var SnapshotGenerator
     */
    protected $generator;

    /**
     * @param $serviceLocator
     * @return $this
     */
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param $repositories
     * @return $this
     */
    public function setRepositories($repositories)
    {
        $this->repositories = $repositories;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRepositories()
    {
        return $this->repositories;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param null $entity
     * @param array $options
     * @return $this
     */
    public function __invoke($entity = null, $options = array())
    {
        if (is_array($entity)) {
            $options = $entity;
            $entity = null;
        }
        $this->entity = $entity;
        $this->options = $options;
        $this->generator = null;
        if (!isset($entity)) {
            return $this;
        }
        $this->snapshot();
        return $this;
    }

    /**
     * @param null $entity
     * @return $this
     */
    public function snapshot($entity = null)
    {
        if (isset($entity)) {
            $this->entity = $entity;
        }
        if (!isset($this->entity)) {
            // or throw an exception ? since we expect to get a snapshot
            return $this;
        }

        if ($this->entity instanceof SnapshotGeneratorProviderInterface) {
            $generator = $this->getGenerator();
            $data = $generator->getSnapshot();

            // snapshot-class
            $target = $this->getTarget();

            if (isset($target)) {
                $target->__invoke($data);
                $className = get_class($this->entity);
                // @TODO, have to be abstract
                $snapShotMetaClassName = '\\' . $className . 'SnapshotMeta';
                $meta = new $snapShotMetaClassName;
                $meta->setEntity($target);
                $meta->setSourceId($this->entity->id);
                $this->getRepositories()->store($meta);
            }
        }
        return $this;
    }

    /**
     * shows the differences between the last snapshot and the given entity
     * return Null = there is no snapshot
     * return array() = there is a snapshot but no difference
     *
     * @param $entity
     * @return array|null
     */
    public function diff($entity)
    {
        if ($entity instanceof SnapshotGeneratorProviderInterface) {
            $this->entity = $entity;
            $generator = $this->getGenerator();
            $targetClass = $this->getTarget(false);
            $dataHead = $generator->getSnapshot();
            if (empty($dataHead) || empty($targetClass)) {
                return null;
            }
            $repositorySnapshotMeta = $this->getRepositories()->getRepository($targetClass . "Meta");
            $snapshot = $repositorySnapshotMeta->findSnapshot($this->entity);
            // an snapshot has to be so simple that there is no need for a special hydrator
            $hydrator = new EntityHydrator();
            $dataLast = $hydrator->extract($snapshot);
            if (empty($dataLast)) {
                // there is no Snapshot, but returning an empty array would make a wrong conclusion,
                // that there is a snapshot, and it has no differences.
                // actually, if there is a snapshot, it always differ (dateCreated)
                return null;
            }
            return $this->array_compare($dataLast, $dataHead);
        } else {
            // entity is not an implementation of SnapshotGeneratorProviderInterface
        }
        return $this;
    }

    /**
     * the Target is the snapshotMeta-Class
     *
     * @param bool $generateInstance    most of the time we need an instance of the snapshot
     *                                  but we need sometimes just the repository of the snapshotMeta,
     *                                  and we just want the className.
     *                                  If we make this parameter to False we just get the className
     * @return null|string
     */
    protected function getTarget($generateInstance = true)
    {
        $serviceLocator = $this->getServicelocator();
        // set the actual options
        $this->getGenerator();
        $target = null;
        if (array_key_exists('target', $this->options)) {
            $target = $this->options['target'];
            if (is_string($target)) {
                if ($serviceLocator->has($target)) {
                    $target = $serviceLocator->get($target);
                    if ($generateInstance) {
                        $target = get_class($target);
                    }
                } else {
                    if ($generateInstance) {
                        $target = new $target;
                    }
                }
            }
        }
        return $target;
    }

    /**
     * the generator transforms an entity into an array
     *
     * what a generator ought to do more than an hydrator is to unriddle all related data,
     * which can imply that from other entities there also a snapshot can be created
     * @return SnapshotGenerator|mixed|null
     */
    protected function getGenerator()
    {
        if (isset($this->generator)) {
            return $this->generator;
        }

        if ($this->entity instanceof SnapshotGeneratorProviderInterface) {
            $serviceLocator = $this->getServicelocator();

            // the snapshotgenerator is a service defined by the name of the entity
            // this is the highest means, all subsequent means just add what is not set
            $className = get_class($this->entity);
            if ($serviceLocator->has('snapshotgenerator' . $className)) {
                $generator = $this->serviceLocator->get('snapshotgenerator' . $className);
                if (is_array($generator)) {
                    $this->options = ArrayUtils::merge($generator, $this->options);
                    $generator = null;
                }
            }

            // the snapshotgenerator is provided by the entity
            // this can either be a generator-entity of a array with options
            if (!isset($generator)) {
                $generator = $this->entity->getSnapshotGenerator();
                if (is_array($generator)) {
                    $this->options = ArrayUtils::merge($generator, $this->options);
                    if (array_key_exists('generator', $generator)) {
                        $generator = $this->options['generator'];
                        unset($this->options['generator']);
                    } else {
                        $generator = null;
                    }
                }
                if (is_string($generator)) {
                    $generator = $serviceLocator->get($generator);
                }
            }

            // the last possibility to get a generator
            if (!isset($generator)) {
                // defaultGenerator
                $generator = new SnapshotGenerator();
            }

            // *** filling the options
            // hydrator
            // can be a class, but if it's a string, consider it to be an hydrator in the hydratormanager
            if (array_key_exists('hydrator', $this->options)) {
                $hydrator = $this->options['hydrator'];
                if (is_string($hydrator) && !empty($hydrator)) {
                    $hydrator = $serviceLocator->get('HydratorManager')->get($hydrator);
                }
                $generator->setHydrator($hydrator);
            }

            // exclude
            // add the elements, that should not be transferred
            if (array_key_exists('exclude', $this->options)) {
                // it is very likely that the hydrator is set by the snapshot-class,
                // so we have to asume, that may know the hydrator
                $hydrator = $generator->getHydrator();
                $exclude = $this->options['exclude'];
                if (is_array($exclude)) {
                    $hydrator->setExcludeMethods($exclude);
                }
            }
            $generator->setSource($this->entity);
            $this->generator = $generator;
        }
        return $this->generator;
    }

    /**
     * makes a recursiv difference between array1 and array2
     * found commands like  'array_diff_assoc' wanting
     *
     * the result looks like
     * key => array( old, new)
     * in subarrays it looks like
     * key.subkey = array( old, new)
     *
     * @param $array1
     * @param $array2
     * @param int $maxDepth
     * @return array
     */
    protected function array_compare($array1, $array2, $maxDepth = 2)
    {
        $result = array();
        $arraykeys = array_unique(array_merge(array_keys($array1), array_keys($array2)));
        foreach ($arraykeys as $key) {
            if (!empty($key) && is_string($key) && $key[0] != "\0" && substr($key, 0, 8) != 'Doctrine') {
                if (array_key_exists($key, $array1) && !array_key_exists($key, $array2)) {
                    $result[$key] = array($array1[$key], '');
                }
                if (!array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
                    $result[$key] = array('', $array2[$key]);
                }
                if (array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
                    $subResult = null;
                    if (is_array($array1[$key]) && is_array($array2[$key])) {
                        if (0 < $maxDepth) {
                            $subResult = $this->array_compare($array1[$key], $array2[$key], $maxDepth - 1);
                        }
                    } elseif (is_object($array1[$key]) && is_object($array2[$key])) {
                        if (0 < $maxDepth) {
                            $hydrator = new EntityHydrator();
                            $a1 = $hydrator->extract($array1[$key]);
                            $a2 = $hydrator->extract($array2[$key]);
                            $subResult = $this->array_compare($a1, $a2, $maxDepth - 1);
                        }
                    } else {
                        if ($array1[$key] != $array2[$key]) {
                            $result[$key] = array( $array1[$key], $array2[$key]);
                        }
                    }
                    if (!empty($subResult)) {
                        foreach ($subResult as $subKey => $subValue) {
                            if (!empty($subKey) && is_string($subKey)) {
                                $result[$key . '.' . $subKey] = $subValue;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }


    /**
     * @param Dispatchable $controller
     */
    public function setController(Dispatchable $controller)
    {
    }

    /**
     * @return null|void|Dispatchable
     */
    public function getController()
    {
    }
}
