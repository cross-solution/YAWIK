<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Jobs\Form\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class JobManagerStrategy implements StrategyInterface
{
    /**
     *
     *
     * @var array
     */
    private $metaData = [];


    public function extract($value, ?object $object = null)
    {
        $this->metaData = $value;

        if (!isset($value['organizations:managers'])) {
            return [];
        }

        $ids = [];
        foreach ($value['organizations:managers'] as $manager) {
            $ids[] = $manager['id'];
        }

        return $ids;
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param mixed $value The original value.
     * @param array $data  (optional) The original data for context.
     *
     * @return mixed Returns the value that should be hydrated.
     */
    public function hydrate($value, ?array $data)
    {
        $metaData = $this->metaData;
        $managers = [];

        if ('__empty__' == $value) {
            unset($metaData['organizations:managers']);
        } else {
            foreach ($value as $manager) {
                list($id, $name, $email) = explode('|', $manager, 3);
                $managers[] = [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                ];
            }
            $metaData['organizations:managers'] = $managers;
        }

        return $metaData;
    }
}
