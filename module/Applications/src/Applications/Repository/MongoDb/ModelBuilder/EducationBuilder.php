<?php

namespace Applications\Repository\MongoDb\ModelBuilder;

class EducationBuilder extends AbstractModelBuilder
{
    
    protected function constructModelPrototype()
    {
        return new \Applications\Model\Education();
    }
    
}