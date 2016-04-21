<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTestUtils\TestCase;

/**
 * Creates a service manager mock with configured services.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
trait ServiceManagerMockTrait
{

    /**
     * Gets a service manager mock
     *
     * <b>$services:</b>
     * <pre>
     * [
     *      <service-name> => <service|FALSE>,
     *      <service-name> => [
     *          'service' => <service|FALSE>,
     *          'service_options' => ARRAY,
     *          'use_peering_service_managers' => <BOOL|['has'=> BOOL, 'get' => BOOL]>,
     *          'check_abstract_factories' => BOOL,
     *          'aliases' => [<alias-name>, ...],
     *      ],
     *      ...
     * ]
     * </pre>
     *
     * <b>$options:</b>
     * <pre>
     * [
     *      'manager_class' => <FQCN>,
     *      'is_plugin_manager' => BOOL,
     *      'mockes_objects' => [ <method_name>, ... ],
     * ]
     * </pre>
     *
     * @param array $services
     * @param array $options
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getServiceManagerMock(array $services, array $options = [])
    {
        $defaultOptions = [
            'manager_class' => '\Zend\ServiceManager\ServiceManager',
            'is_plugin_manager' => false,
            'mocked_methods' => [ ],
        ];
        $options = array_merge($defaultOptions, $options);

        $mock = $this->getMockBuilder($options['manager_class'])
            ->disableOriginalConstructor()
            ->setMethods(array_merge([ 'has', 'get' ], $options['mocked_methods']))
            ->getMock();

        $hasMap = $getMap = [];

        $defaultSpec = [
            'service' => false,
            'service_options' => [],
            'use_peering_service_managers' => true,
            'check_abstract_factories' => true,
            'aliases' => [],
        ];
        foreach ($services as $name => $spec) {
            if (!is_array($spec)) {
                $spec = [ 'service' => $spec ];
            }

            $spec = array_merge($defaultSpec, $spec);

            if (!is_array($spec['use_peering_service_managers'])) {
                $hasUsePeering = $getUsePeering = $spec['use_peering_service_managers'];
            } else {
                $hasUsePeering = isset($spec['use_peering_service_managers']['has'])
                               ? $spec['use_peering_service_managers']['has']
                               : true;

                $getUsePeering = isset($spec['use_peering_service_managers']['get'])
                               ? $spec['use_peering_service_managers']['get']
                               : true;

            }

            $names = $spec['aliases'];
            array_push($names, $name);

            foreach ($names as $name) {
                $hasMap[] = [ $name, $spec['check_abstract_factories'], $hasUsePeering, (bool) $spec['service'] ];

                if (false === $spec['service']) { continue; }

                if ($options['is_plugin_manager']) {
                    $getMap[] = [ $name, $spec['service_options'], $getUsePeering, $spec['service'] ];
                } else {
                    $getMap[] = [ $name, $getUsePeering, $spec['service'] ];
                }
            }
        }

        $mock->method('has')->will($this->returnValueMap($hasMap));
        $mock->method('get')->will($this->returnValueMap($getMap));

        return $mock;

    }
}