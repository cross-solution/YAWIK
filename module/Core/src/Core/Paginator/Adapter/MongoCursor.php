<?php

namespace Core\Paginator\Adapter;

use Zend\Paginator\Adapter\AdapterInterface;

class MongoCursor implements AdapterInterface
{
    public function __construct($cursor, $builder)
    {
        $this->cursor = $cursor;
        $this->builder = $builder;
    }
    
    public function count()
    {
        return $this->cursor->count();
    }
    
    public function getItems($offset, $itemCountPerPage)
    {
        $this->cursor->skip($offset);
        $this->cursor->limit($itemCountPerPage);
        return $this->builder->buildCollection($this->cursor);
    }
}
