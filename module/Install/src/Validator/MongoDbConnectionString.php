<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Install\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class MongoDbConnectionString extends AbstractValidator
{
    const INVALID = 'invalidConnectionString';
    const NO_CONNECTION = 'connectionFails';

    protected $options = array(
        'translatorTextDomain' => 'Install',
    );

    protected $messageTemplates = array(
        self::INVALID => /* @translate */ 'Invalid connection string',
        self::NO_CONNECTION => /* @translate */ 'Connecting the database failed: %databaseError%',
    );

    protected $messageVariables = array(
        'databaseError' => 'databaseError'
    );

    protected $databaseError;

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

        try {
            @new \MongoClient($value);
        } catch (\MongoConnectionException $e) {
            $this->databaseError = $e->getMessage();
            $this->error(self::NO_CONNECTION);
            return false;
        }

        return true;
    }


}