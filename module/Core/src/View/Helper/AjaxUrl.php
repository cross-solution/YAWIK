<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\View\Helper;

use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Renderer\PhpRenderer;

/**
 * Builds urls to use the ajax call api.
 * The basepath is prepended automatically.
 *
 * Build urls with name and optional params
 * <pre>
 * <a href="<?=$this->ajaxUrl('ajaxEventName')?>">Link withoout params (e.g. "?ajax=ajaxEventName")</a>
 * <a href="<?=$this->ajaxUrl('anotherEvent', ['param1' => 'value1'])?>">Link: ?ajax=anotherEvent&param1=value1</a>
 * </pre>
 *
 * Build urls by passing only params:
 * <pre>
 * <?=$this->ajaxUrl(['ajax' => 'name', 'param1' => 'value', ...])?>
 * </pre>
 *
 * Note:
 *      When passing only parameters array, a key named "ajax" is REQUIRED.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.29
 */
class AjaxUrl extends AbstractHelper
{
    /**
     * The basepath
     *
     * @var string
     */
    protected $basePath;

    /**
     * @param string $basePath
     */
    public function __construct($basePath = '')
    {
        $this->basePath = rtrim($basePath, '/') . '/';
    }

    /**
     * Build an ajax url.
     *
     * @param string|array $name
     * @param array        $params
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function __invoke($name, array $params = [])
    {
        if (is_array($name)) {
            if (!isset($name[ 'ajax' ])) {
                throw new \InvalidArgumentException('Key "ajax" is required when passing array as first argument.');
            }
            $params = $name;
        } else {
            $params = array_merge(['ajax' => $name], $params);
        }

        $url = sprintf(
            '%s?%s',
            $this->basePath,
            http_build_query($params)
        );

        return $url;
    }
}
