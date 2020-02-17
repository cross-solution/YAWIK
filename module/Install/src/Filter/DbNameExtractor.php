<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Filter;

use Laminas\Filter\AbstractFilter;
use Laminas\Filter\Exception;

/**
 *  Filter to extract the database name from a mongodb connection string
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.20
 */
class DbNameExtractor extends AbstractFilter
{
    /**
     * The default database name
     *
     * @var string
     */
    protected $defaultDatabaseName = 'YAWIK';

    /**
     * Creates an instance.
     *
     * @param null|string|array|\Traversable $optionsOrDefaultDbName
     */
    public function __construct($optionsOrDefaultDbName = null)
    {
        if ($optionsOrDefaultDbName !== null) {
            if (!static::isOptions($optionsOrDefaultDbName)) {
                $this->setDefaultDatabaseName($optionsOrDefaultDbName);
            } else {
                $this->setOptions($optionsOrDefaultDbName);
            }
        }
    }

    /**
     * Gets the default database name.
     *
     * @return string
     */
    public function getDefaultDatabaseName()
    {
        return $this->defaultDatabaseName;
    }

    /**
     * Sets the default database name.
     *
     * @param string $dbName
     *
     * @return self
     */
    public function setDefaultDatabaseName($dbName)
    {
        $this->defaultDatabaseName = (string) $dbName;

        return $this;
    }

    /**
     * Extracts the database name of a mongo db connection string.
     *
     * If no database is specified in the connection string, the default
     * value {@link defaultDatabaseName} is returned.
     *
     * @param  mixed $value
     *
     * @return string
     */
    public function filter($value)
    {
        // extract database
        $dbName = preg_match('~^mongodb://[^/]+/([^\?]+)~', $value, $match)
            ? $match[1]
            : $this->getDefaultDatabaseName();

        return $dbName;
    }
}
