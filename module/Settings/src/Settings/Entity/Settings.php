<?php

/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Settings\Entity;

class Settings extends AwareEntity implements SettingsInterface {

    /**
     * die Setting-Entity consists of Namespaces, which can 
     * subsequently be adressed
     * @param string $nameSpace
     * @return Settings\Entity\Settings
     */
    public function get($nameSpace) {
        if (isset($this->data)) {
            if (isset($this->data[$nameSpace])) {
                if ($this->spawnsAsEntities) {
                    if (!$this->data[$nameSpace] instanceof $this) {
                        // transform array into the Entity-Class
                        $entity = new static($this);
                        $entity->setData($this->data[$nameSpace]);
                        $this->data[$nameSpace] = $entity;
                    }
                }
                return $this->data[$nameSpace];
            } else {
                // create a new entity
                $this->data[$nameSpace] = new static($this);
                return $this->data[$nameSpace];
            }
        }
        // the Entity is empty
        return Null;
    }

}