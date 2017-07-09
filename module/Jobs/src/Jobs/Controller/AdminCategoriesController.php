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

use Core\Form\SummaryForm;
use Interop\Container\ContainerInterface;
use Jobs\Entity\Category;
use Jobs\Listener\Events\JobEvent;
use Jobs\Repository\Categories;
use Zend\Form\FormInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Handles the management of the job categories.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class AdminCategoriesController extends AbstractActionController
{
	private $adminCategoriesForm;
	
	/**
	 * @var Categories
	 */
	private $jobsCategoryRepo;
	
	private $repositories;
	
	private $viewHelperManager;
	
	static public function factory(ContainerInterface $container)
	{
		$ob = new static();
		$ob->initContainer($container);
		return $ob;
	}

	public function initContainer(ContainerInterface $container)
	{
		$this->adminCategoriesForm = $container->get('forms')->get('Jobs/AdminCategories');
		$this->repositories = $container->get('repositories');
		$this->jobsCategoryRepo = $container->get('repositories')->get('Jobs/Category');
		$this->viewHelperManager = $container->get('ViewHelperManager');
	}
	
    public function indexAction()
    {
        $form = $this->setupContainer();

        if ($this->getRequest()->isPost()) {
            return $this->processPost($form);
        }

        $model = new ViewModel([
            'form' => $form
        ]);
        $model->setTemplate('jobs/admin/categories');

        return $model;
    }

    private function setupContainer()
    {
        //$services = $this->serviceLocator;
        $form = $this->adminCategoriesForm;
	    $repositories = $this->repositories;
        $rep = $this->jobsCategoryRepo;

        $professions = $rep->findOneBy(['value' => 'professions']);
        if (!$professions) {
            $professions = new Category('Professions');
            $repositories->store($professions);
        }

        $types = $rep->findOneBy(['value' => 'employmentTypes']);
        if (!$types) {
            $types = new Category('Employment Types', 'employmentTypes');
            $repositories->store($types);
        }

        $industries = $rep->findOneBy(['value' => 'industries']);
        if (!$industries) {
            $industries = new Category('Industries', 'industries');
            $repositories->store($industries);
        }

        $form->setEntity($professions, 'professions');
        $form->setEntity($types, 'employmentTypes');
        $form->setEntity($industries, 'industries');

        return $form;
    }

    private function processPost($container)
    {
        $identifier = $this->params()->fromQuery('form');
        $form       = $container->getForm($identifier);
        $form->setData($_POST);
        $valid = $form->isValid();
        $this->repositories->store($form->getObject());
        $form->bind($form->getObject());
        $form->setRenderMode(SummaryForm::RENDER_SUMMARY);
        $helper = $this->viewHelperManager->get('summaryForm');

        return new JsonModel([
            'content' => $helper($form),
            'valid' => $valid,
            'errors' => $form->getMessages()
        ]);
    }
}