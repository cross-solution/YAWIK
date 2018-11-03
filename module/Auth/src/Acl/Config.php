<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Config.php */
namespace Acl;

use Zend\Permissions\Acl\AclInterface;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Resource\GenericResource;
use Acl\Assertion\AssertionManager;

class Config
{
    protected $config;
    protected $assertions;

    /**
     * @param array $config
     * @param AssertionManager $assertionManager
     */
    public function __construct($config = array(), AssertionManager $assertionManager)
    {
        $this->config = $config;
        $this->assertions = $assertionManager;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = array();
        if (!isset($this->config['roles'])) {
            return $roles;
        }
        
        foreach ($this->config['roles'] as $roleId => $parents) {
            if (is_int($roleId)) {
                $roleId = $parents;
                $parents = null;
            }
            if (is_string($parents)) {
                $parents = array($parents);
            }
            $roles[$roleId] = $parents;
        }
        
        return $roles;
    }

    /**
     * @param $roleId
     * @param $type
     * @return array
     */
    public function getRules($roleId, $type)
    {
        if (!isset($this->config['rules'][$roleId][$type])) {
            return array();
        }

        $config = $this->config['rules'][$roleId][$type];
        if (!is_array($config)) {
            return [];
        }

        if ("__ALL__" === $config) {
            return array(array(
                'resource' => null,
                'privilege' => null,
                'assertion' => null,
            ));
        }
        $rules = array();
        foreach ($config as $resourceId => $spec) {
            if (is_int($resourceId)) {
                $rules[] = array(
                    'resource' => $spec,
                    'privilege' => null,
                    'assertion' => null,
                );
                continue;
            }
            if (is_string($spec)) {
                $rules[] = array(
                    'resource' => $resourceId,
                    'privilege' => '__ALL__' == $spec ? null : $spec,
                    'assertion' => null
                );
                continue;
            }
            
            foreach ($spec as $privilege => $assertion) {
                if (is_int($privilege)) {
                    $rules[] = array(
                        'resource' => $resourceId,
                        'privilege' => '__ALL__' == $assertion ? null : $assertion,
                        'assertion' => null,
                    );
                    continue;
                }
                if (is_string($assertion)) {
                    $assertionInstance = $this->assertions->get($assertion);
                } else {
                    $assertionInstance = $this->assertions->get($assertion[0], $assertion[1]);
                }
                $rules[] = array(
                    'resource' => $resourceId,
                    'privilege' => '__ALL__' == $privilege ? null : $privilege,
                    'assertion' => $assertionInstance,
                );
            }
        }
        return $rules;
    }

    /**
     * @param AclInterface $acl
     * @return AclInterface
     */
    public function configureAcl(AclInterface $acl)
    {
        foreach ($this->getRoles() as $roleId => $parents) {
            $acl->addRole(new GenericRole($roleId), $parents);
            
            foreach ($this->getRules($roleId, 'allow') as $spec) {
                if (!$acl->hasResource($spec['resource'])) {
                    $acl->addResource(new GenericResource($spec['resource']));
                }
                $acl->allow($roleId, $spec['resource'], $spec['privilege'], $spec['assertion']);
            }
            
            foreach ($this->getRules($roleId, 'deny') as $spec) {
                if (null !== $spec['resource'] && !$acl->hasResource($spec['resource'])) {
                    $acl->addResource(new GenericResource($spec['resource']));
                }
                $acl->deny($roleId, $spec['resource'], $spec['privilege'], $spec['assertion']);
            }
        }
        
        return $acl;
    }
}
