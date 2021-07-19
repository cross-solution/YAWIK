<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Organizations\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Organizations\Repository\Organization;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class FeaturedCompaniesController extends AbstractActionController
{
    private $repository;

    public function __construct(Organization $repository)
    {
        $this->repository = $repository;
    }

    public function indexAction()
    {

    }
}
