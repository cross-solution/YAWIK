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
use Jobs\Entity\Category;
use Jobs\Listener\Events\JobEvent;
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
        $services = $this->serviceLocator;

        $forms = $services->get('forms');
        $form = $forms->get('Jobs/AdminCategories');

        $repositories = $services->get('repositories');
        $rep = $repositories->get('Jobs/Category');

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

        $form->setEntity($professions, 'professions');
        $form->setEntity($types, 'employmentTypes');

        return $form;
    }

    private function processPost($container)
    {
        $identifier = $this->params()->fromQuery('form');
        $form       = $container->getForm($identifier);
        $form->setData($_POST);
        $valid = $form->isValid();
        $this->serviceLocator->get('repositories')->store($form->getObject());
        $form->bind($form->getObject());
        $form->setRenderMode(SummaryForm::RENDER_SUMMARY);
        $helper = $this->serviceLocator->get('ViewHelperManager')->get('summaryform');

        return new JsonModel([
            'content' => $helper($form),
            'valid' => $valid,
            'errors' => $form->getMessages()
        ]);
    }
}