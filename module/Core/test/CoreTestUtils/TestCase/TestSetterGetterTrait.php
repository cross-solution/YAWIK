<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTestUtils\TestCase;

use PHPUnit\Framework\Exception as PHPUnitException;
use CoreTestUtils\InstanceCreator;

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
 *                           '@value' => 'FQCN',
 *                           '@value' => [ 'FQCN', [args]],
 *                         {, 'expect' => <expect>}
 *                         {, 'expect_property' => <expect>}
 *                         {, 'expect_property' => [ <name>, <expect> ]}
 *                         {, 'default' => <default>}
 *                         {, '@default' => (= @value) (will create instance!)}
 *                         {, 'default@' => same as ('@<FQCN>') (will not create instance)}
 *                         {, 'default_args' => array}
 *                         {, 'setter_method' => string}
 *                         {, 'setter_args' => array}
 *                         {, 'getter_method' => string}
 *                         {, 'getter_args' => array}
 *                         {, 'setter_value' => <value>}
 *                         {, 'setter_exception => <expection class>}
 *                         {, 'setter_exception => [ <exception class>, <exception message> ]}
 *                         {, 'getter_exception => <expection class>}
 *                         {, 'getter_exception => [ <exception class>, <exception message> ]}
 *                         {, 'target' => <FQCN>}
 *                         {, 'target' => <object>}
 *                         {, 'target' => [ <FQCN>, <constructor args> ]}
 *                         {, 'pre' => 'methodName' }
 *                         {, 'pre' => [ 'methodName', [ arg, arg, .. ]}
 *                         {, 'post' => 'methodName' }
 *                         {, 'post' => [ 'methodName', [ arg, arg, .. ]}
 * ];
 *
 * <value>:
 *          The value that should be set and on which the getter assertion operates.
 *          If it's a string prefixed with '@', its assumed to be a class name and it will be tried
 *          to instantiate an instance. If more complex construction is needed, you should
 *          either use '@value' or redefine the propertiesProvider() method as mentioned above.
 *
 * <expect>:
 *          If your setter modifies the value passed to it, you can specify what value should be expected to be
 *          returned by the getter.
 *
 * 'expect_property':
 *          If there's no getter, and no other mean to check if the setter works, provide the
 *          value the property should contain here. (implies 'ignore_getter')
 *          If the property name differs from the setter name, specify an array with
 *          the name as first item and the expected value as the second item.
 *          if the expected value is an array, and the property name does not differ,
 *          pass that array as the first item.
 *
 * <default>:
 *          If the getter provides a default value, specify it here, it will be checked before the setter call.
 *          If <default> is a string prefixed with '@', the returned value from the getter will be checked
 *          using "assertInstanceOf()".
 *
 * <default-args>:
 *          If the getter requires arguments, pass them here (for the default value check),
 *
 * <getter-args>:
 *          Getter arguments for the "regular" getter test.
 *
 * <setter-args>:
 *          If the setter requires more arguments than the value, provide them here. The value will be prepended
 *          to this arguments.
 *
 * <setter-value>:
 *          If omitted, a fluent interface implementation is assumed. If the setter returns other value than
 *          the SUT instance, specify it here.
 *
 * 'getter_method':
 *          Specify alternate name for getter method (when it's not "get<property>". A '*' in this spec will be
 *          replaced with the provided property name. (eg. "is*" => "is<property>")
 *
 * 'setter_method':
 *          Specify alternate name for setter method. (same as 'getter_method')
 *
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
 * 'pre' and 'post':
 *          a Closure, a callable or the name of a TestCase method, which is called before ('pre') or after ('post') the tests
 *          for a single property.
 *
 *          If its not a Closure, following specs are possible:
 *          - 'methodName' : a method of the TestCase, which will be called without arguments.
 *          - [ 'methodName', [<arg>, <arg>, ,...] ] : Method of the TestCase which will be called with the
 *                                                     provided Arguments.
 *          - [ Closure, [<arg>, <arg>] ]: Calls the Closure with the provided args.
 *          - [ <callable>, [<arg>, <arg>,. ..] ]: Calls the callable with the provided args.
 *
 *          Closures will be bounded to the test case instance in its scope. (that means you have access
 *          to $this->target in the closure especially).
 *
 *          <arg>: There are special arguments:
 *                  - '###' : Will be substituted with the current spec of the tested property.
 *                  - '@self': Will be subsituted with the TestCase instance.
 *                  - '@target': Will be substituted with the target instance.
 *
 *          if no arguments are provided, the current spec will be passed as the solely argument.
 *
 *
 * @property object $target
 * @property array  $properties
 * @method assertInstanceOf
 * @method assertEquals
 * @method assertSame
 * @method assertAttributeEquals
 * @method assertAttributeSame
 * @method setExpectedException
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.25 (renamed in 0.26)
 */
trait TestSetterGetterTrait
{
    /**
     * Data provider for the setter/getter test.
     *
     * This implementation returns the $properties property of the TestCase,
     * Redefine this method to provide more complex data.
     *
     * @return array
     */
    public function propertiesProvider()
    {
        return property_exists($this, 'properties') ? $this->properties : [];
    }

    /**
     * @testdox      Allows setting and/or getting object property values.
     * @dataProvider propertiesProvider
     */
    public function testSetterAndGetter($name, $spec)
    {
        $errTmpl = __METHOD__ . ': ' . get_class($this);
        if (!property_exists($this, 'target') || (!is_object($this->target) && !isset($spec['target']))) {
            throw new PHPUnitException($errTmpl
                                                   . ' must define the property "target" and the value must be an object.');
        }

        if (!is_array($spec)) {
            $spec = ['value' => $spec];
        }

        if (isset($spec['@value'])) {
            $spec['value'] = InstanceCreator::fromSpec($spec['@value'], InstanceCreator::FORCE_INSTANTIATION);
        } elseif (isset($spec['value']) && is_string($spec['value']) && 0 === strpos($spec['value'], '@')) {
            $spec['value'] = InstanceCreator::newClass($spec['value']);
        } elseif (isset($spec['value@'])) {
            $spec['value'] = '@' . $spec['value@'];
        }

        /* Value could be 'null', so we need to use array_key_exists here. */
        if (!array_key_exists('value', $spec)) {
            if (!array_key_exists('default', $spec) && !array_key_exists('@default', $spec) && !array_key_exists('default@', $spec)) {
                throw new PHPUnitException($errTmpl . ': Specification must contain the key "value" or "default".');
            }

            $spec['value'] = null;
            $spec['ignore_getter'] = true;
            $spec['ignore_setter'] = true;
        }

        if (isset($spec['target'])) {
            $this->target = is_object($spec['target'])
                ? $spec['target']
                : InstanceCreator::fromSpec($spec['target'], InstanceCreator::FORCE_INSTANTIATION);
        }

        if (isset($spec['@default'])) {
            $spec['default'] = InstanceCreator::fromSpec($spec['@default'], InstanceCreator::FORCE_INSTANTIATION);
        } elseif (isset($spec['default@'])) {
            $spec['default'] = '@' . $spec['default@'];
        }



        $this->_setterGetter_triggerHook('pre', $spec);

        $getterMethod = isset($spec['getter_method']) ? str_replace('*', $name, $spec['getter_method']) : "get$name";
        $setterMethod = isset($spec['setter_method']) ? str_replace('*', $name, $spec['setter_method']) : "set$name";
        $getterArgs = isset($spec['getter_args']) ? $spec['getter_args'] : false;
        $setterArgs = isset($spec['setter_args']) ? $spec['setter_args'] : false;

        if (isset($spec['setter_exception'])) {
            $this->_setterGetter_assertSetterGetterException(
                $setterMethod,
                $spec['setter_exception'],
                $spec['value'],
                $setterArgs
            );

            return;
        }


        if (array_key_exists('default', $spec)) {
            $defaultGetterArgs = isset($spec['default_args']) ? $spec['default_args'] : $getterArgs;
            $assert            = isset($spec['default_assert']) ? $spec['default_assert'] : null;
            $this->_setterGetter_assertGetterValue($getterMethod, $spec['default'], $defaultGetterArgs, $assert, true);
        }

        if (!isset($spec['ignore_setter']) || !$spec['ignore_setter']) {
            $assert = isset($spec['setter_assert']) ? $spec['setter_assert'] : null;
            $this->_setterGetter_assertSetterValue(
                $setterMethod,
                $spec['value'],
                $setterArgs,
                $assert,
                array_key_exists('setter_value', $spec)
                                                       ? $spec['setter_value']
                                                       : '__FLUENT_INTERFACE__'
            );
        }

        if (isset($spec['getter_exception'])) {
            $this->_setterGetter_assertSetterGetterException(
                $getterMethod,
                $spec['getter_exception'],
                '__GETTER_EXCEPTION__',
                $getterArgs
            );

            return;
        }

        if (isset($spec['expect_property'])) {
            $assert = isset($spec['property_assert']) ? $spec['property_assert'] : null;
            $this->_setterGetter_assertPropertyValue($name, $spec['expect_property'], $assert);
        } elseif (!isset($spec['ignore_getter']) || !$spec['ignore_getter']) {
            $assert = isset($spec['getter_assert']) ? $spec['getter_assert'] : null;
            $this->_setterGetter_assertGetterValue(
                $getterMethod,
                array_key_exists('expect', $spec) ? $spec['expect'] : $spec['value'],
                $getterArgs,
                $assert
            );
        }

        $this->_setterGetter_triggerHook('post', $spec);
    }

    /**
     * Assert that a getter or setter throws an exception.
     *
     * @param string      $method
     * @param string      $exception Exception class name
     * @param mixed       $value
     * @param array|false $args
     */
    private function _setterGetter_assertSetterGetterException($method, $exception, $value = null, $args = [])
    {
        if (is_array($exception)) {
            $message   = $exception[1];
            $exception = $exception[0];
        } else {
            $message = null;
        }

        $this->expectException($exception);

        if (!is_null($message)) {
            $this->expectExceptionMessage($message);
        }

        if ('__GETTER_EXCEPTION__' != $value) {
            if (false === $args) {
                $args = [$value];
            } else {
                array_unshift($args, $value);
            }
        }

        $this->_setterGetter_callTargetMethod($method, $args);
    }

    /**
     * Assert that the getter returns the correct value.
     *
     * @param string        $getter
     * @param mixed         $value
     * @param array|false   $args
     * @param callable|null $assert
     * @param bool          $isDefaultValue
     */
    private function _setterGetter_assertGetterValue($getter, $value, $args, $assert, $isDefaultValue = false)
    {
        $err    = sprintf(
            '%s: %s: %s for %s::%s is not as expected',
            __TRAIT__,
            get_class($this),
            $isDefaultValue ? 'Default value' : 'Value',
            get_class($this->target),
            $getter
        );

        $returned = $this->_setterGetter_callTargetMethod($getter, $args);

        if ($assert) {
            if ($assert instanceof \Closure) {
                /** @noinspection PhpUndefinedMethodInspection */
                $cb = $assert->bindTo($this, $this);
                $cb($getter, $returned, $value);
            } else {
                call_user_func([$this, $assert], $getter, $returned, $value);
            }

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

    /**
     * Assert that the setter returns the correct value.
     *
     * @param string        $setter
     * @param mixed         $value
     * @param array|false   $args
     * @param callable|null $assert
     * @param mixed|null    $expect
     */
    private function _setterGetter_assertSetterValue($setter, $value, $args, $assert, $expect = null)
    {
        if ('__FLUENT_INTERFACE__' === $expect) {
            $expect = $this->target;
        }

        $err = __TRAIT__ . ': ' . get_class($this) . ': Setter ' . get_class($this->target) . '::' . $setter
               . ($expect === $this->target ? ' breaks fluent interface.' : ' does not return expected value.');


        if (false === $args) {
            $args = [$value];
        } else {
            array_unshift($args, $value);
        }

        $returned = $this->_setterGetter_callTargetMethod($setter, $args);

        if ($assert) {
            if ($assert instanceof \Closure) {
                /** @noinspection PhpUndefinedMethodInspection */
                $cb = $assert->bindTo($this, $this);
                $cb($setter, $returned, $expect);
            } else {
                call_user_func([$this, $assert], $setter, $returned, $expect);
            }

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

    /**
     * Assert that a target property has the correct value
     *
     * @param string        $name
     * @param mixed|array   $value
     * @param callable|null $assert
     */
    private function _setterGetter_assertPropertyValue($name, $value, $assert)
    {
        $err = __TRAIT__ . ': ' . get_class($this) . ': Property ' . $name . ' does not have expected value.';

        $propertyName = $name;

        if (is_array($value)) {
            if (is_array($value[0])) {
                $value = $value[0];
            } else {
                $propertyName = $value[0];
                $value        = $value[1];
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
     * Triggers a pre or post hook.
     *
     * @param string $type
     * @param array $spec
     *
     * @throws \UnexpectedValueException
     */
    private function _setterGetter_triggerHook($type, $spec)
    {
        if (!isset($spec[$type])) {
            return;
        }

        $cb      = $spec[$type];
        $args    = false; /* @var array|false $args */

        if (!is_callable($cb)) {
            if (is_array($cb)) {
                $method = $cb[0];
                $args   = isset($cb[1]) ? $cb[1] : false;
            } else {
                $method = $cb;
            }

            if (!is_callable($method)) {
                $method = [$this, $method];
                if (!is_callable($method)) {
                    throw new \UnexpectedValueException('Invalid callback for "' . $type . '" hook.');
                }
            }
            $cb = $method;
        }

        if ($cb instanceof \Closure) {
            /** @noinspection PhpUndefinedMethodInspection */
            $cb = $cb->bindTo($this, $this);
        }

        if (false === $args) {
            $args = [ $spec ];
        } else {
            $args = array_map(
                function ($item) use ($spec) {
                    if ('###' == $item) {
                        return $spec;
                    }

                    if ('@self' == $item) {
                        return $this;
                    }

                    if ('@target' == $item) {
                        return $this->target;
                    }

                    return $item;
                },
                $args
            );
        }

        call_user_func_array($cb, $args);
    }

    /**
     * Calls a method on the target instance.
     *
     * @param string $method
     * @param array|false $args
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function _setterGetter_callTargetMethod($method, $args)
    {
        $callback = [$this->target, $method];

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(sprintf(
                'Method %s is not callable on %s. ',
                $method,
                get_class($this->target)
                                                ));
        }

        return false === $args ? call_user_func($callback) : call_user_func_array($callback, $args);
    }
}
