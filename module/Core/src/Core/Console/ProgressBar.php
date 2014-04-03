<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** ProgressBar.php */ 
namespace Core\Console;

use Zend\ProgressBar\ProgressBar as ZfProgressBar;
use Zend\ProgressBar\Adapter\Console;

class ProgressBar extends ZfProgressBar
{
    public function __construct($max = 100, $persistenceNamespace = null)
    {
        
        $adapter = new Console(array(
            'elements' => array(
                Console::ELEMENT_TEXT,
                Console::ELEMENT_BAR,
                Console::ELEMENT_PERCENT,
                Console::ELEMENT_ETA
            ),
            'textWidth' => 20,
            'barLeftChar' => '-',
            'barRightChar' => ' ',
            'barIndicatorChar' => '>',
        ));
        parent::__construct($adapter, 0, $max, $persistenceNamespace);
    }
    
    public function finish()
    {
        $this->update($this->max, 'Done');
        return parent::finish();
    }
}

