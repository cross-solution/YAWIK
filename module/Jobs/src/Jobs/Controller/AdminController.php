<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Controller;

use Jobs\Listener\Events\JobEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class AdminController extends AbstractActionController
{

    public function indexAction()
    {
        return $this->pagination([
            'params'    => [ 'Jobs_Admin', ['text', 'page' => 1, 'companyId', 'status' ] ],
            'form'      => [ 'as' => 'form', 'Jobs/AdminSearch' ],
            'paginator' => [ 'as' => 'jobs', 'Jobs/Admin' ],
        ]);
    }

    public function editAction()
    {
        $services = $this->serviceLocator;
        $repositories = $services->get('repositories');
        $jobs         = $repositories->get('Jobs');
        $job          = $jobs->find($this->params()->fromQuery('id'));
        $forms        = $services->get('forms');
        $form         = $forms->get('Jobs/AdminJobEdit');
        $request      = $this->getRequest();

        if ($request->isPost()) {
            $post = $this->params()->fromPost();
            $form->setData($post);
            $valid = $form->isValid();
            $errors = $form->getMessages();

            if ($valid) {
                $job->setDatePublishStart($post['datePublishStart']);
                if ($job->getStatus()->getName() != $post['status']) {
                    $oldStatus = $job->getStatus();
                    $job->changeStatus($post['status'], '[System] Status changed via Admin GUI.');
                    $events = $services->get('Jobs/Events');
                    $events->trigger(JobEvent::EVENT_STATUS_CHANGED, $this, [ 'job' => $job, 'status' => $oldStatus ]);
                }
            }

            return new JsonModel([
                'valid' => $valid,
                'errors' => $errors
                                 ]);
        }

        $form->bind($job);

        return [ 'form' => $form, 'job' => $job ];
    }
    
}