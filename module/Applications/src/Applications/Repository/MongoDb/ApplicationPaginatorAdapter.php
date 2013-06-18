<?php

namespace Applications\Repository\MongoDb;

use Zend\Paginator\Adapter\AdapterInterface;

class ApplicationPaginatorAdapter implements AdapterInterface
{
    public function __construct($cursor, $modelBuilder) {
        $this->cursor = $cursor;
        $this->modelBuilder = $modelBuilder;
    }
    
    public function count()
    {
        return $this->cursor->count();
    }
    
    public function getItems($offset, $itemCountPerPage)
    {
        $this->cursor->skip($offset);
        $this->cursor->limit($itemCountPerPage);
        return $this->modelBuilder->buildCollection(iterator_to_array($this->cursor));
    }
}