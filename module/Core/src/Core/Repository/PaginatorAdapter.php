<?php

namespace Core\Repository;

use Zend\Paginator\Adapter\AdapterInterface;
use Core\Repository\EntityBuilder\EntityBuilderInterface;

class PaginatorAdapter implements AdapterInterface
{
    protected $cursor;
    protected $builder;
    
    public function __construct(\MongoCursor $cursor, EntityBuilderInterface $builder) {
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