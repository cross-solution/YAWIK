<?php

namespace Applications\Model\Hydrator;

use Core\Model\Hydrator\ModelHydrator;

class ApplicationHydrator extends ModelHydrator 
{
    protected $modelCollectionPropertyNames = array(
        'educations',
        'employments',
        'languages',
    );
    
    
    
}