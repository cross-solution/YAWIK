<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Listener\Events;


use Core\Paginator\PaginatorService;
use Zend\EventManager\Event;
use Zend\Paginator\Paginator;

/**
 * Class CreatePaginatorEvent
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.27
 */
class CreatePaginatorEvent extends Event
{
    /**
     * Event is fired when CreatePaginator plugins creating a paginator
     */
    const EVENT_CREATE_PAGINATOR = 'core.create_paginator';

    /**
     * @var string
     */
    protected $paginatorName;

    /**
     * @var array
     */
    protected $paginatorParams = array();

    /**
     * @var PaginatorService
     */
    protected $paginators;

    /**
     * @var Paginator
     */
    protected $paginator;
	
	/**
     * @return string
     */
    public function getPaginatorName()
    {
        return $this->paginatorName;
    }

    /**
     * @param string $paginatorName
     * @return CreatePaginatorEvent
     */
    public function setPaginatorName($paginatorName)
    {
        $this->paginatorName = $paginatorName;

        return $this;
    }

    /**
     * @return PaginatorService
     */
    public function getPaginators()
    {
        return $this->paginators;
    }

    /**
     * @param PaginatorService $paginators
     * @return CreatePaginatorEvent
     */
    public function setPaginators($paginators)
    {
        $this->paginators = $paginators;

        return $this;
    }

    /**
     * @return array
     */
    public function getPaginatorParams()
    {
        return $this->paginatorParams;
    }

    /**
     * @param array $paginatorParams
     * @return CreatePaginatorEvent
     */
    public function setPaginatorParams($paginatorParams)
    {
        $this->paginatorParams = $paginatorParams;

        return $this;
    }

    /**
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @param Paginator $paginator
     * @return CreatePaginatorEvent
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function setParams($params)
    {
        if(is_array($params)){
            if(isset($params['paginatorParams'])){
                $this->setPaginatorParams($params['paginatorParams']);
                unset($params['paginatorParams']);
            }
            if(isset($params['paginators'])){
                $this->setPaginators($params['paginators']);
                unset($params['paginators']);
            }
            if(isset($params['paginatorName'])){
                $this->setPaginatorName($params['paginatorName']);
                unset($params['paginatorName']);
            }
        }
        parent::setParams($params);
    }
}