<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Install\Controller\Plugin;

use Auth\Entity\Filter\CredentialFilter;
use Install\Filter\DbNameExtractor;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Helper for initial user creation.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.20
 */
class UserCreator extends AbstractPlugin
{
    /**
     * Database name extractor
     *
     * @var \Install\Filter\DbNameExtractor
     */
    protected $databaseNameExtractor;

    /**
     *
     *
     * @var \Auth\Entity\Filter\CredentialFilter
     */
    protected $credentialFilter;

    public function __construct(DbNameExtractor $dbNameExtractor, CredentialFilter $credentialFilter)
    {
        $this->databaseNameExtractor = $dbNameExtractor;
        $this->credentialFilter = $credentialFilter;
    }

    /**
     * Inserts a minimalistic user document into the database.
     *
     * @param string $dbConn Database connection string
     * @param string $username Login name
     * @param string $password Credential
     *
     * @return bool
     *
     * @codeCoverageIgnore Untestable due to missing test database
     */
    public function process($dbConn, $username, $password)
    {
        $m  = @new \MongoClient($dbConn);
        $dbName = $this->databaseNameExtractor->filter($dbConn);
        $credential = $this->credentialFilter->filter($password);
        $db = $m->selectDB($dbName);
        $collection = $db->selectCollection('users');
        $document = array(
            'isDraft' => false,
            'role' => 'admin',
            'login' => $username,
            'credential' => $credential
        );

        $result = $collection->insert($document);

        return isset($result['ok']) && 1 === $result['ok'];
    }
}
