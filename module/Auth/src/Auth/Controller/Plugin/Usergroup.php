<?php

namespace Auth\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Usergroup extends AbstractPlugin
{
    public function __invoke()
    {
        return $this;
    }
}