<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** */
namespace Cv\Controller;

use Cv\Form\SearchForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as Session;

/**
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Munthi
 *
 * @since 0.29 - uses PaginationBuilder controller plugin.
 */
class IndexController extends AbstractActionController
{
    /**
     * Home site
     *
     */
    public function indexAction()
    {
        $result = $this->pagination([
                'params' => ['Cvs_Index', [
                    'search',
                    'page' => 1,
                    'l',
                    'd' => 10]
                ],
                'form' => ['as' => 'filterForm', 'Cv/SearchForm'],
                'paginator' => ['as' => 'resumes', 'Cv/Paginator']
            ]);

        return $result;
    }

    public function viewAction()
    {
    }
}
