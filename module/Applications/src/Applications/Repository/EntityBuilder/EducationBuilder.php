<?php

namespace Applications\Repository\EntityBuilder;

class EducationBuilder extends AbstractEntityBuilder
{
    
    protected function constructEntityPrototype()
    {
        return new \Applications\Entity\Education();
    }
    
}