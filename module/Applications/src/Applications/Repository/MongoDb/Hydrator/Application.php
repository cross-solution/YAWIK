<?php

namespace Applications\Repository\MongoDb\Hydrator;

use Core\Repository\MongoDb\Hydrator;

class Application extends Hydrator\ModelHydrator
{
    
    public function __construct($educationHydrator)
    
    protected function init()
    {
        $this->addStrategy('dateCreated', new Hydrator\DatetimeStrategy())
             ->addStrategy('dateModified', new Hydrator\DatetimeStrategy(/*extractResetDate*/ true));
             ->addStrategy('educations', new Hydrator\SubDocumentsStrategy());
    }
}