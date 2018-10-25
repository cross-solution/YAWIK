<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Controller\Plugin;

use Install\Filter\DbNameExtractor;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Creates configuration file.
 *
 * Either write it directly in the file system or generate the file as string.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.20
 */
class YawikConfigCreator extends AbstractPlugin
{

    /**
     * Database extractor
     *
     * @var DbNameExtractor
     */
    protected $dbNameExctractor;

    /**
     * Creates an instance.
     *
     * @param DbNameExtractor $dbNameExtractor
     */
    public function __construct(DbNameExtractor $dbNameExtractor)
    {
        $this->dbNameExctractor = $dbNameExtractor;
    }

    /**
     * Generates a configuration file.
     *
     * @param string $dbConn
     * @param string $email
     *
     * @return bool|string
     */
    public function process($dbConn, $email)
    {
        // extract database
        $dbName = $this->dbNameExctractor->filter($dbConn);

        $config = array(
            'doctrine' => array(
                'connection'    => array(
                    'odm_default' => array(
                        'connectionString' => $dbConn,
                    ),
                ),
                'configuration' => array(
                    'odm_default' => array(
                        'default_db' => $dbName,
                    ),
                ),
            ),

            'core_options' => array(
                'system_message_email' => $email,
            ),
        );

        $content = $this->generateConfigContent($config);
        $ok      = $this->writeConfigFile($content);

        return $ok ? true : $content;
    }

    /**
     * Generates the content for the configuration file.
     *
     * @param array $config
     *
     * @return string
     */
    protected function generateConfigContent(array $config)
    {
        /* This code is taken from ZF2s' classmap_generator.php script. */

        // Create a file with the class/file map.
        // Stupid syntax highlighters make separating < from PHP declaration necessary
        $content = '<' . "?php\n"
                   . "\n"
                   . 'return ' . var_export($config, true) . ';';

        // Fix \' strings from injected DIRECTORY_SEPARATOR usage in iterator_apply op
        $content = str_replace("\\'", "'", $content);


        // Remove unnecessary double-backslashes
        $content = str_replace('\\\\', '\\', $content);

        // Exchange "array (" width "array("
        $content = str_replace('array (', 'array(', $content);

        // Make the file end by EOL
        $content = rtrim($content, "\n") . "\n";

        var_export($config, true);

        return $content;
    }

    /**
     * Writes the configuration content in a file.
     *
     * Returns false, if file cannot be created.
     *
     * @param string $content
     *
     * @return bool
     */
    protected function writeConfigFile($content)
    {
        if (!is_writable('config/autoload')) {
            return false;
        }

        $file = 'config/autoload/yawik.config.global.php';
        // need to chmod 777 to be usable by docker and local environment
        @touch($file);
        @chmod($file, 0777);
        return (bool) @file_put_contents($file, $content, LOCK_EX);
    }
}
