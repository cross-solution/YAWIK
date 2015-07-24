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

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class YawikConfigCreator extends AbstractPlugin
{

    public function process($dbConn, $user, $pass)
    {
        // extract database
        $dbName = preg_match('~/([^\?]+)~', substr($dbConn, 10), $match)
                ? $match[1]
                :'YAWIK';


        $config = array(
            'doctrine' => array(
                'connection' => array(
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

            'Auth' => array(
                'default_user' => array(
                    'login' => $user,
                    'password' => $pass,
                ),
            ),
        );

        $content = $this->generateConfigContent($config);
        $ok      = $this->writeConfigFile($content);

        return $ok ? true : $content;
    }

    protected function generateConfigContent(array $config)
    {
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

        return $content;

    }

    protected function writeConfigFile($content)
    {
        if (!is_writable('config/autoload')) {
            return false;
        }

        $file = 'config/autoload/yawik.config.global.php';

        return @file_put_contents($file, $content);
    }
    
}