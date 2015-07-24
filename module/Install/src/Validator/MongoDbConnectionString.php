<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Validates a mongo db connection string.
 *
 * Additionally tries to connect using this string and validates the connection itself.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.20
 */
class MongoDbConnectionString extends AbstractValidator
{
    const INVALID       = 'invalidConnectionString';
    const NO_CONNECTION = 'connectionFails';

    /**
     * Options
     *
     * @var array
     */
    protected $options = array(
        'translatorTextDomain' => 'Install',
    );

    /**
     * Message templates.
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID       => /* @translate */
            'Invalid connection string',
        self::NO_CONNECTION => /* @translate */
            'Connecting the database failed: %databaseError%',
    );

    /**
     * Message variables
     *
     * @var array
     */
    protected $messageVariables = array(
        'databaseError' => 'databaseError'
    );

    /**
     * Last database error message, if one.
     *
     * @var string|null
     */
    protected $databaseError;

    /**
     * Regular expression of a valid connection string.
     *
     * @var string
     */
    protected $pattern = '~^mongodb://(?:[^ :]+:[^ @]@)?[^ :,]+(?::\d+)?(?:,[^ :,/]+(?::\d+)?)*(?:/[^ \?]+)?(?:\?[^ ]+)?$~';

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     *
     * @return bool
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {
        $this->databaseError = null;

        // Empty connection string is valid (defaults to localhost:defaultPort)
        if (!empty($value) && !preg_match($this->pattern, $value)) {
            $this->error(self::INVALID);

            return false;
        }

        // @codeCoverageIgnoreStart
        // This cannot be testes until we have a test database environment.
        try {
            @new \MongoClient($value);
        } catch (\MongoConnectionException $e) {
            $this->databaseError = $e->getMessage();
            $this->error(self::NO_CONNECTION);

            return false;
        }

        return true;
        // @codeCoverageIgnoreEnd
    }


}