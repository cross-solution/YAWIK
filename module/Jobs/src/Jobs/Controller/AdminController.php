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

use Core\Factory\ContainerAwareInterface;
use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;
use Jobs\Listener\Events\JobEvent;
use Zend\Form\FormElementManager\FormElementManagerTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class AdminController extends AbstractActionController implements ContainerAwareInterface
{
	/**
	 * @var RepositoryService
	 */
	private $repositories;
	
	/**
	 * @var FormElementManagerTrait
	 */
	private $formManager;
	
	private $jobEvents;
	
	static public function factory(ContainerInterface $container)
	{
		$ob = new self();
		$ob->setContainer($container);
		return $ob;
	}
	
	public function setContainer( ContainerInterface $container )
	{
		$this->repositories     = $container->get('repositories');
		$this->formManager      = $container->get('forms');
		$this->jobEvents        = $container->get('Jobs/Events');
	}
	
	
	public function indexAction()
    {
        $params = $this->params()->fromQuery();
        return $this->pagination([
            'params'    => [ 'Jobs_Admin', ['text', 'page' => 1, 'companyId', 'status' ] ],
            'form'      => [ 'as' => 'form', 'Jobs/AdminSearch' ],
            'paginator' => [ 'as' => 'jobs', 'Jobs/Admin' ],
        ]);
    }

    public function editAction()
    {
        $repositories = $this->repositories;
        $jobs         = $repositories->get('Jobs');
        $job          = $jobs->find($this->params()->fromQuery('id'));
        $forms        = $this->formManager;
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
                    $events = $this->jobEvents;
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
