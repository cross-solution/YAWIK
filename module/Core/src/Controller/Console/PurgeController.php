<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Controller\Console;

use Core\Console\ProgressBar;
use Core\Controller\Plugin\EntityEraser;
use Core\Entity\EntityInterface;
use Core\Service\EntityEraser\LoadEvent;
use Zend\Console\ColorInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Console\Controller\AbstractConsoleController;

/**
 * Purge console action controller
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class PurgeController extends AbstractConsoleController
{
    /**
     *
     *
     * @var \Core\EventManager\EventManager
     */
    private $loadEvents;

    /**
     * @param \Core\EventManager\EventManager $loadEvents
     *
     * @return self
     */
    public function setLoadEvents($loadEvents)
    {
        $this->loadEvents = $loadEvents;

        return $this;
    }


    /**
     * @return void
     */
    public function indexAction()
    {
        // console purge <entity> <id>

        /* @var EntityEraser $eraser */

        $console  = $this->getConsole();
        $eraser   = $this->plugin(EntityEraser::class);
        $options  = \Zend\Json\Json::decode($this->params('options', '{}'), \Zend\Json\Json::TYPE_ARRAY);
        $eraser->setOptions($options);
        $entities = $eraser->loadEntities($this->params('entity'), $this->params('id'));

        $found = count($entities);
        $console->writeLine(sprintf('Found %s entities to purge.', $found));

        if (0 == $found) {
            return;
        }

        if (!$this->params('no-check')) {
            $console->writeLine('Checking dependencies ... ' . PHP_EOL);

            $eraser = $this->plugin(EntityEraser::class);
            $counts = [];
            $totalCount = 0;
            foreach ($entities as $entity) {
                $console->writeLine('    ' . $this->entityToString($entity));
                $totalCount += 1;
                $dependencies = $eraser->checkDependencies($entity);

                foreach ($dependencies as $dependencyList) {
                    if ($dependencyList->isCount()) {
                        $entitiesCount = $dependencyList->getEntities();
                        $dependendEntities = [];
                    } else {
                        $dependendEntities = $dependencyList->getEntities();
                        $entitiesCount = count($dependendEntities);
                    }

                    $console->writeLine('        ' . $entitiesCount . ' ' . $dependencyList->getName() . ': ' . $dependencyList->getDescription());

                    $totalCount += $entitiesCount;
                    if (!isset($counts[$dependencyList->getName()])) {
                        $counts[$dependencyList->getName()] = 0;
                    }
                    $counts[$dependencyList->getName()] += $entitiesCount;

                    foreach ($dependendEntities as $dependendEntity) {
                        $console->writeLine('        - ' . $this->entityToString($dependendEntity));
                    }
                    $console->writeLine(' ');
                }
                $console->writeLine('');
            }

            $console->writeLine($totalCount . ' entities affected:');
            $console->writeLine('    ' . count($entities) . ' ' . $this->params('entity'));
            foreach ($counts as $name => $count) {
                $console->writeLine('    ' . $count . ' ' . $name);
            }

            $console->writeLine('');
            $confirmed = \Zend\Console\Prompt\Confirm::prompt('Proceed? [y/n] ');

            if (!$confirmed) {
                $console->writeLine('Aborted.');
                exit(1);
            }
        }


        $totalCount = 0;
        $counts = [];

        $progress     = new ProgressBar(count($entities));
        $i = 0;
        foreach ($entities as $entity) {
            $progress->update(++$i, $entity->getId());
            $dependencies = $eraser->erase($entity);

            $totalCount += 1;
            foreach ($dependencies as $list) {
                $entitiesCount = $list->isCount() ? $list->getEntities() : count($list->getEntities());
                $totalCount += $entitiesCount;
                if (!isset($counts[$list->getName()])) {
                    $counts[$list->getName()] = [0, $list->getDescription()];
                }
                $counts[$list->getName()][0] += $entitiesCount;
            }
        }

        $progress->finish();
        $console->writeLine('');
        $console->writeLine('Processed ' . $totalCount . ' entities.');
        $console->writeLine('    ' . count($entities) . ' ' . $this->params('entity') . ' deleted.');
        foreach ($counts as $name => $count) {
            $console->writeLine('    ' . $count[0] . ' ' . $name . ' ' . $count[1]);
        }
    }

    public function listAction()
    {
        $responses = $this->loadEvents->trigger(LoadEvent::FETCH_LIST, $this);
        $console = $this->getConsole();

        $console->writeLine('');
        foreach ($responses as $response) {
            if (!is_array($response) || !isset($response['key'])) {
                continue;
            }

            $console->writeLine(sprintf("%-20s %s", $response['key'], isset($response['description']) ? $response['description'] : ''), ColorInterface::BLUE);

            if (isset($response['options']) && is_array($response['options'])) {
                $console->writeLine('');
                foreach ($response['options'] as $name => $desc) {
                    $console->writeLine(sprintf("%20s- %-15s %s", ' ', $name, $desc));
                }
            }
            $console->writeLine('');
        }
        $console->writeLine();
    }

    /**
     * Get a string representation from an entity.
     *
     * Uses the entitys' __toString method, if available.
     * Otherwise just returns the class name and if available, the ID.
     *
     * @param EntityInterface $entity
     *
     * @return string
     */
    private function entityToString(EntityInterface $entity)
    {
        if (method_exists($entity, '__toString')) {
            return $entity->__toString();
        }

        $str = get_class($entity);

        if ($entity instanceof \Core\Entity\IdentifiableEntityInterface) {
            $str .= '( ' . $entity->getId() . ' )';
        }

        return $str;
    }
}
