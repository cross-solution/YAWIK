<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Validates a mongo db connection string.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.20
 */
class MongoDbConnectionString extends AbstractValidator
{
    const INVALID       = 'invalidConnectionString';

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
        self::INVALID       => /* @translate */ 'Invalid connection string',
    );

    /**
     * Regular expression of a valid connection string.
     *
     * @var string
     */
    protected $pattern = '~^mongodb://(?:[^ :]+:[^ @]+@)?[^ :,/]+(?::\d+)?(?:,[^ :,/]+(?::\d+)?)*(?:/[^ \?\.]*)?(?:\?[^ ]+)?$~';

    /**
     * Returns true if the passed string is a valid mongodb connection string.
     *
     * {@inheritDoc}
     *
     * @param  string $value
     *
     */
    public function isValid($value)
    {
        $this->databaseError = null;

        // Empty connection string is valid (defaults to localhost:defaultPort)
        if (!empty($value) && !preg_match($this->pattern, $value)) {
            $this->error(self::INVALID);

            return false;
        }

        return true;
    }
}
