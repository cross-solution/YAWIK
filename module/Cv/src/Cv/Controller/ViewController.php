<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Cv\Controller;

use Cv\Repository\Cv as CvRepository;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ViewController extends AbstractActionController
{

    /**
     *
     *
     * @var \Cv\Repository\Cv
     */
    private $repository;

    public function __construct(CvRepository $repository)
    {
        $this->repository = $repository;
    }

    public function indexAction()
    {
        /** @var string|null $id */
        $id = $this->params('id');
        $resume = $this->repository->find($id);

        if (!$resume) {
            throw new \Exception('No resume found with id ' . $id);
        }

        /* @todo REMOVE THIS
         * @codeCoverageIgnoreStart */
        if (!$resume->getDateCreated()) {
            $resume->setDateCreated();
        }
        /* @codeCoverageIgnoreEnd */

        $this->acl($resume, 'view');

        return [
            'resume' => $resume
        ];
    }
    
}