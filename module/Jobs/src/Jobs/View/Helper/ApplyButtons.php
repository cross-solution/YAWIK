<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * View helper to display apply buttons
 *
 * @since 0.30 - do not throw exception on invalid data, but
 */
class ApplyButtons extends AbstractHelper
{
    
    /**
     * @var string
     */
    protected $partial = 'partials/buttons';
    
    /**
     * Renders apply buttons according to passed $options
     * Following optional options are recognized:
     *      partial:            custom partial script for rendering the buttons (relative to current template script), default: 'partials/buttons'
     *      oneClickOnly:       display only one click apply buttons, default: false
     *      defaultLabel:       label of default apply button, default: 'Apply now'
     *      oneClickLabel:      label of one click apply buttons, default: 'Apply with %s'
     *      sendImmediately:    flag whether to sent application immediately, default: false
     *
     * Usage example with defaults:
     * <code>
     *      <?=$this->jobApplyButtons($this->applyButtons)?>
     * </code>
     *
     * Usage example with options set explicitly:
     * <code>
     *      <?=$this->jobApplyButtons($this->applyButtons , [
     *          'partial' => 'partials/mybuttons',
     *          'oneClickOnly' => true,
     *          'defaultLabel' => $this->translate('Send application'),
     *          'oneClickLabel' => $this->translate('Send application with my %s social profile'),
     *          'sendImmediately' => true,
     *      ])?>
     * </code>
     * @param array $data
     * @param array $options
     * @return string
     */
    public function __invoke(array $data, array $options = [])
    {
        // check data
        if (!isset($data['uri']) || !isset($data['oneClickProfiles']) || !isset($data['applyId']))
        {
            return '';
        }
        
        $variables = [
            'default' => null,
            'oneClick' => [],
        ];
        $options = array_merge([
            'partial' => $this->partial,
            'oneClickOnly' => false,
            'defaultLabel' => null,
            'oneClickLabel' => null,
            'sendImmediately' => false
        ], $options);
        $view = $this->view;
		$currentTemplate = $view->viewModel()
            ->getCurrent()
            ->getTemplate();
        $partial = dirname($currentTemplate) . '/' . $options['partial'];
        
        if (!$options['oneClickOnly'] && $data['uri']) {
            $variables['default'] = [
                'label' => $options['defaultLabel'] ?: $view->translate('Apply now'),
                'url' => $data['uri']
            ];
        }
        
        if ($data['oneClickProfiles']) {
            $label = $options['oneClickLabel'] ?: $view->translate('Apply with %s');
            
            foreach ($data['oneClickProfiles'] as $network) {
				$variables['oneClick'][] = [
                    'label' => sprintf($label, $network),
                    'url' => $view->url('lang/apply-one-click', ['applyId' => $data['applyId'], 'network' => $network, 'immediately' => $options['sendImmediately'] ?: null], ['force_canonical' => true]),
				    'network' => $network
                ];
            }
        }
        
        return $view->partial($partial, $variables);
    }
    
	/**
	 * @return string
	 */
	public function getPartial()
	{
		return $this->partial;
	}

	/**
	 * @param string $partial
	 * @return ApplyButtons
	 */
	public function setPartial($partial)
	{
		$this->partial = $partial;
		
		return $this;
	}
}
