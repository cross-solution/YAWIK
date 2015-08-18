<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** SocialProfiles.php */
namespace Auth\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Auth\Controller\Plugin\SocialProfiles\AbstractAdapter;

class SocialProfiles extends AbstractPlugin
{
    protected $adapterMap = array(
        'facebook' => '\\Auth\\Controller\\Plugin\\SocialProfiles\\Facebook',
        'xing'     => '\\Auth\\Controller\\Plugin\\SocialProfiles\\Xing',
        'linkedin' => '\\Auth\\Controller\\Plugin\\SocialProfiles\\LinkedIn',
            
    );
    
    public function __construct($hybridAuth, array $adapters = array())
    {
        $this->hybridAuth = $hybridAuth;
        foreach ($adapters as $network => $adapter) {
            $this->addAdapter($network, $adapter);
        }
    }
    
    public function __invoke($network)
    {
        return $this->fetch($network);
    }
    
    public function fetch($network)
    {
        $returnUri    = $this->getController()->getRequest()->getRequestUri();
        $hauthAdapter = $this->hybridAuth->authenticate($network, array('hauth_return_to' => $returnUri));
        $api          = $hauthAdapter->api();
        $adapter      = $this->getAdapter($network);
        $profile      = $adapter->fetch($api);
        
        return $profile;
    }
    
    public function addAdapter($network, $adapter)
    {
        if (is_string($adapter)) {
            $this->adapterMap[$network] = $adapter;
        } elseif ($adapter instanceof AbstractAdapter) {
            $this->adapters[$network] = $adapter;
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    'Adapter must be either a string or an instance of \Auth\Controller\Plugin\SocialProfiles\AbstractAdapter, but received %s',
                    is_object($adapter) ? get_class($adapter) : '(' . gettype($adapter) . ')'
                )
            );
        }
        
        return $this;
    }
    
    public function getAdapter($network)
    {
        if (isset($this->adapters[$network])) {
            return $this->adapters[$network];
        }
        
        if (isset($this->adapterMap[$network])) {
            $adapterClass = $this->adapterMap[$network];
            $adapter      = new $adapterClass();
            $this->adapters[$network] = $adapter;
            return $adapter;
        }
        
        throw new \InvalidArgumentException(
            sprintf(
                'No adapter registered for key %s in the adapter map.',
                $network
            )
        );
    }
}
