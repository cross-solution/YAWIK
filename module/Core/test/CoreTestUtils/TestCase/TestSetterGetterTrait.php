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
 * Trait for testing setters and getters of the SUT.
 *
 * TestCases using this trait can test all setters and getters simply by defining
 * either a property $properties or by redefining the method "propertiesProvider()"
 *
 * The property $target should contain the SUT instance.
 *
 * The property $properties should be an array as returned by an PHPUnit data provider.
 * As you cannot instatiate new Objects in a property definition, you can redefine the
 * method "propertiesProvider()", which should return the same array of arrays.
 *
 * For each specified property the following actions will be taken:
 * * The default value is checked, if provided.
 * * The setter is called with the <value> and its return value is checked.
 * * The getter is called and its return value is checked against the <value>.
 *
 * Assertions method:
 * Based on the type of <value>, the assertion will be made
 * * with "assertEquals": default assertion
 * * with "assertTrue" or "assertFalse": if <value> is boolean
 * * with "assertSame": if <value> is an object.
 *
 *
 *
 * The $properties array:
 * [
 *      [ <property-name>, <value> ],
 *      [ <property-name>, [ 'value' => <value>
 *                         {, 'expect' => <expect>}
 *                         {, 'expect_property' => <expect>}
 *                         {, 'expect_property' => [ <name>, <expect> ]}
 *                         {, 'default' => <default>}
 *                         {, 'default_args' => array}
 *                         {, 'setter_args' => array}
 *                         {, 'getter_args' => array}
 *                         {, 'setter_value' => <value>}
 *                         {, 'setter_exception => <expection class>}
 *                         {, 'setter_exception => [ <exception class>, <exception message> ]}
 *                         {, 'getter_exception => <expection class>}
 *                         {, 'getter_exception => [ <exception class>, <exception message> ]}
 *                         {, 'target' => <FQCN>}
 *                         {, 'target' => <object>}
 *                         {, 'target' => [ <FQCN>, <constructor args> ]}
 * ];
 *
 * <value>: The value that should be set and on which the getter assertion operates.
 *          If it's a string prefixed with '@', its assumed to be a class name and it will be tried
 *          to instantiate an instance. If more complex construction is needed, you should
 *          redefine the propertiesProvider() method as mentioned above.
 * <@value>: Creates an object instance from the provided specs:
 *           If a string is passed, it is used as class name.
 *           If an array is passed, the first entry must be a string holding the class name, the second item
 *           can be an array of constructor arguments.
 * <expect>: If your setter modifies the value passed to it, you can specify what value should be expected to be
 *           returned by the getter.
 * 'expect_property': If there's no getter, and no other mean to check if the setter works, provide the
 *                    value the property should contain here. (implies 'ignore_getter')
 *                    If the property name differs from the setter name, specify an array with
 *                      the name as first item and the expected value as the second item.
 *                      if the expected value is an array, and the property name does not differ,
 *                      pass that array as the first item.
 * <default>: If the getter provides a default value, specify it here, it will be checked before the setter call.
 *            If <default> is a string prefixed with '@', the returned value from the getter will be checked
 *            using "assertInstanceOf()".
 * <@default>: Same as @value, but used for the default test.
 * <default-args>: If the getter requires arguments, pass them here (for the default value check),
 * <getter-args>: Getter arguments for the "regular" getter test.
 * <setter-args>: If the setter requires more arguments than the value, provide them here. The value will be prepended
 *                to this arguments.
 * <setter-value>: If omitted, a fluent interface implementation is assumed. If the setter returns other value than
 *                 the SUT instance, specify it here.
 * 'setter_exception':
 *          If the setter throws an exception under certain circumstances, you can test this, too.
 *          The setter will be called with the value (and additional args) and the specified exception class
 *          (and message, if provided) will be expected. There's no check on the getter in that case though.
 *
 * 'getter_exception':
 *          As setter_exception, except that before testing for a thrown exception, the setter is called with the
 *          provided value (and additional args).
 *
 * 'target':
 *          You can provide an alternative target for each specific test.
 *          Either pass in an object, an FQCN or an array with FQCN as first item and the constructor args as array
 *          as the second item. The target instance will then be used in the following test scenario.
 *
 * @property object $target
 * @property array $properties
 * @method fail
 * @method assertInstanceOf
 * @method assertEquals
 * @method assertSame
 * @method assertAttributeEquals
 * @method assertAttributeSame
 * @method setExpectedException
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
trait TestSetterGetterTrait
{
    public function propertiesProvider()
    {
        return property_exists($this, 'properties') ? $this->properties : [];
    }

    /**
     * @testdox Allows setting and/or getting object property values.
     * @dataProvider propertiesProvider
     */
    public function testSetterAndGetter($name, $spec)
    {
        $errTmpl = __METHOD__ . ': ' . get_class($this);
        if (!property_exists($this, 'target') || !is_object($this->target)) {
            $this->fail($errTmpl . ' must define the property "target" and the value must be an object.');
        }

        if (!is_array($spec)) {
            $spec = [ 'value' => $spec ];
        }

        if (isset($spec['@value'])) {
            $spec['value'] = $this->_setterGetterTrait_createInstance($spec['@value']);
        }

        if (!isset($spec['value'])) {
            $this->fail($errTmpl. ': Specification must contain the key "value".');
        }

        if (isset($spec['target'])) {
            $this->target = is_object($spec['target'])
                ? $spec['target']
                : $this->_setterGetterTrait_createInstance($spec['target']);
        }

        if (isset($spec['@default'])) {
            $spec['default'] = $this->_setterGetterTrait_createInstance($spec['@default']);
        }

        if (is_string($spec['value']) && 0 === strpos($spec['value'], '@')) {
            $spec['value'] = $this->_setterGetterTrait_createInstance(substr($spec['value'], 1));
        }

        $hook = function ($type, $spec) {
            if (!isset($spec[$type])) {
                return;
            }

            $cb = $spec[$type];

            if (is_array($cb)) {
                $method = $cb[0];
                $args   = isset($cb[1]) ? $cb[1] : [];

            } else {
                $method = $cb;
                $args   = [];
            }

            $args = array_map(
                function($item) use ($spec) {
                    if ('###' == $item) { return $spec; }
                    if (is_array($item) && isset($item[0]) && is_string($item[0])) {
                        if (0 === strpos($item[0], '->')) {

                            $args = isset($item[1]) ? $item[1] : [];
                            return call_user_func_array([$this->target, substr($item[0], 2)], $args);
                        }

                        if (0 === strpos($item[0], '::')) {
                            $args = isset($item[1]) ? $item[1] : [];
                            return call_user_func_array([$this, substr($item[0], 2)], $args);
                        }
                    }

                    return $item;
                },
                $args
            );

            $callback = is_callable($method) ? $method : [$this, $method];
            call_user_func_array($callback, $args);
        };

        $hook('pre', $spec);

        $getterArgs = isset($spec['getter_args']) ? $spec['getter_args'] : false;
        $setterArgs = isset($spec['setter_args']) ? $spec['setter_args'] : false;

        if (isset($spec['setter_exception'])) {
            $this->assertSetterGetterException($name, $spec['setter_exception'], $spec['value'], $setterArgs);
            return;
        }


        if (isset($spec['default'])) {
            $defaultGetterArgs = isset($spec['default_args']) ? $spec['default_args'] : $getterArgs;
            $assert = isset($spec['default_assert']) ? $spec['default_assert'] : null;
            $this->assertGetterValue($name, $spec['default'], $defaultGetterArgs, $assert, true);
        }

        if (!isset($spec['ignore_setter']) || !$spec['ignore_setter']) {
            $assert = isset($spec['setter_assert']) ? $spec['setter_assert'] : null;
            $this->assertSetterValue($name, $spec['value'], $setterArgs, $assert, array_key_exists('setter_value', $spec) ? $spec['setter_value'] : '__FLUENT_INTERFACE__');
        }

        if (isset($spec['getter_exception'])) {
            $this->assertSetterGetterException($name,  $spec['getter_exception'], '__GETTER_EXCEPTION__', $getterArgs);
            return;
        }

        if (isset($spec['expect_property'])) {
            $assert = isset($spec['property_assert']) ? $spec['property_assert'] : null;
            $this->assertPropertyValue($name, $spec['expect_property'], $assert);

        } else  if (!isset($spec['ignore_getter']) || !$spec['ignore_getter']) {
            $assert = isset($spec['getter_assert']) ? $spec['getter_assert'] : null;
            $this->assertGetterValue($name, isset($spec['expect']) ? $spec['expect'] : $spec['value'], $getterArgs, $assert);
        }

        $hook('post', $spec);
    }

    public function assertGetterValue($name, $value, $args, $assert, $isDefaultValue = false)
    {
        $getter = "get$name";
        $err = sprintf(
            '%s: %s: %s for %s::%s is not as expected',
            __TRAIT__, get_class($this), $isDefaultValue ? 'Default value' : 'Value', get_class($this->target), $getter
        );

        if (false === $args) {
            $returned = $this->target->$getter();
        } else {
            $returned = call_user_func_array([$this->target, $getter], $args);
        }

        if ($assert) {
            call_user_func([ $this, $assert ], $name, $returned, $value);
            return;
        }

        switch (gettype($value)) {
            default:
                if (!is_array($value) && 0 === strpos($value, '@')) {
                    $value = substr($value, 1);
                    $this->assertInstanceOf($value, $returned, $err);
                } else {
                    $this->assertEquals($value, $returned, $err);
                }
                break;

            case "boolean":
                $method = 'assert' . ($value ? 'True' : 'False');
                $this->$method($returned, $err);
                break;

            case "object":
                if ($isDefaultValue) {
                    $this->assertInstanceOf(get_class($value), $returned, $err);

                } else {
                    $this->assertSame($value, $returned, $err);
                }
                break;
        }
    }

    public function assertSetterValue($name, $value, $args, $assert, $expect = null)
    {
        $setter = "set$name";

        if ('__FLUENT_INTERFACE__' === $expect) {
            $expect = $this->target;
        }

        $err    = __TRAIT__ . ': ' . get_class($this) . ': Setter ' . get_class($this->target) . '::' . $setter
                . ($expect === $this->target ? ' breaks fluent interface.' : ' does not return expected value.');


        if (false === $args) {
            $returned = $this->target->$setter($value);
        } else {
            array_unshift($args, $value);
            $returned = call_user_func_array([$this->target, $setter], $args);
        }

        if ($assert) {
            call_user_func([$this, $assert], $name, $returned, $expect);
            return;
        }

        switch (gettype($expect)) {
            default:
                $this->assertEquals($expect, $returned, $err);
                break;

            case "boolean":
                $method = 'assert' . ($expect ? 'True' : 'False');
                $this->$method($returned, $err);
                break;

            case "object":
                $this->assertSame($expect, $returned, $err);
                break;
        }
    }

    public function assertPropertyValue($name, $value, $assert)
    {
        $err    = __TRAIT__ . ': ' . get_class($this) . ': Property ' . $name . ' does not have expected value.';

        $propertyName = $name;

        if (is_array($value)) {
            if (is_array($value[0])) {
                $value = $value[0];
            } else {
                $propertyName = $value[0];
                $value = $value[1];
            }
        }

        if ($assert) {
            call_user_func([$this, $assert], $propertyName, $value, $name);
            return;
        }

        switch (gettype($value)) {
            default:
                $this->assertAttributeEquals($value, $propertyName, $this->target, $err);
                break;

            case "object":
                $this->assertAttributeSame($value, $propertyName, $this->target, $err);
                break;
        }
    }

    /**
     *
     *
     * @param       $name
     * @param       $exception
     * @param null  $value
     * @param array|bool $args
     */
    public function assertSetterGetterException($name, $exception, $value = null, $args = [])
    {
        if (is_array($exception)) {
            $message = $exception[1];
            $exception = $exception[0];
        } else {
            $message = null;
        }

        $this->setExpectedException($exception, $message);

        if ('__GETTER_EXCEPTION__' == $value) {
            $method = "get$name";

        } else {
            $method = "set$name";
            if (false === $args) {
                $args = [ $value ];
            } else {
                array_unshift($args, $value);
            }
        }

        if (false === $args) {
            $this->target->$method();
        } else {
            call_user_func_array([$this->target, $method], $args);
        }
    }

    private function _setterGetterTrait_createInstance($spec)
    {
        if (!is_array($spec) || !isset($spec[1])) {
            $spec = (array) $spec;
            return new $spec[0]();
        }

        $reflection = new \ReflectionClass($spec[0]);
        $instance   = $reflection->newInstanceArgs($spec[1]);

        return $instance;
    }
}