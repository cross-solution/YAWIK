<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SocialProfilesFieldset.php */ 
namespace Auth\Form\ViewHelper;

use Core\Form\View\Helper\FormCollection;
use Zend\Form\ElementInterface;

class SocialProfilesFieldset extends FormCollection
{
    
    public function render(ElementInterface $element)
    {
        $view       = $this->getView();
        $headscript = $view->plugin('headscript');
        $basepath   = $view->plugin('basepath');

        $headscript->appendFile($basepath('Auth/js/form.socialprofiles.js'));
        
        $markup = parent::render($element);

        $modal = <<<HTML
    <div id="%s" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">%s</button>
        </div>
     </div>
  </div>
</div>
HTML;
        $markup .= sprintf(
            $modal, 
            $element->getAttribute('id') . '-preview-box', 
            $view->plugin('translate')->__invoke('Close')
        );
        
        return $markup;
    }
}

