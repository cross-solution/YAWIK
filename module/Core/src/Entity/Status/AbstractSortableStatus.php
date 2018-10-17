<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity\Status;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Abstract sortable status.
 *
 * Defined state are associated to an sortable integer value.
 *
 * To use this, it must be extended, the available states must be defined in a
 * static property called $sortMap. It is recommended to also define class constants
 * for ease of use.
 *
 * <pre>
 * // Do not forget the ODM annotations!
 *
 * class PrioStatus extends AbstractPrioritizedStatus
 * {
 *      const NEW = 'new';
 *      const MODIFIED = 'modified';
 *
 *      // This must at least be "protected" because this class needs to see it.
 *      // Format: string:stateName => int:order
 *      //
 *      protected static $sortMap = [
 *          'state'          => 3
 *          static::NEW      => 1,
 *          static::MODIFIED => 2,
 *
 *      ];
 * }
 * </pre>
 *
 * getStates() would return <pre>
 * [
 *      static::NEW,
 *      static::MODIFIED,
 *      'state'
 * ];</pre>
 * in above example.
 *
 * The sort value is only used to be used in database queries and such the usage of
 * this class is the same as {@link AbstractStatus}
 *
 * @ODM\MappedSuperClass
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
abstract class AbstractSortableStatus extends AbstractStatus
{
    /**
     * The sort value of this status.
     *
     * @ODM\Field(type="int")
     * @var int
     */
    protected $sort;

    public static function getStates()
    {
        $sort = self::getSortMap();
        asort($sort, SORT_NUMERIC);

        return array_keys($sort);
    }

    public function __construct($state)
    {
        parent::__construct($state);

        $map        = self::getSortMap();
        $this->sort = $map[$state];
    }

    protected static function getSortMap()
    {

        /** @noinspection PhpUndefinedFieldInspection */
        if (isset(static::$sortMap)) {

            /** @noinspection PhpUndefinedFieldInspection */
            return static::$sortMap;
        }

        throw new \RuntimeException(sprintf(
            'The class %s does not define the static property $sortMap, which is required when extending %s',
            static::class,
            self::class
        ));
    }
}
