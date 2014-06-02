<?php
namespace WebPower\LuaSandbox\Tests;

use WebPower\LuaSandbox\LuaSandbox;
use WebPower\LuaSandbox\LuaGlobals;

class LuaSandboxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LuaSandbox
     */
    public $obj;

    protected function setUp()
    {
        $this->obj = new LuaSandbox();
    }

    /**
     * @expectedException WebPower\LuaSandbox\LuaErrorException
     */
    function testInvalidLuaThrowsException()
    {
        $lua = <<<CODE
callUnExistingFunction();
CODE;
        $this->obj->run($lua);
    }

    function testValidLua()
    {
        $res = $this->obj->run(<<<CODE
return 10 * 2;
CODE
        );
        $this->assertEquals(20, $res);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function testNonExistingFile()
    {
        $this->obj->runFile('NonExistingFile');
    }

    function testValidFile()
    {
        $res = $this->obj->runFile(__DIR__.'/dumpGlobals.lua');
        $this->assertInternalType('array', $res);
    }

    function testUnset()
    {
        $unsetKeys = array(
            'dofile',
            'loadfile',
            'module',
            'require',
            'coroutine',
            'debug',
            'file',
            'io',
            'os',
            'package',
        );
        $this->obj->unsetVar($unsetKeys);

        $globals = $this->obj->run(<<<CODE
local names = {}
for name, val in pairs(_G) do
    table.insert(names, name)
end
table.sort(names)
return names
CODE
        );

        $luaGlobals = LuaGlobals::getGlobals();
        $luaGlobals = array_flip($luaGlobals);
        foreach ($luaGlobals as $key => $i) {
            $luaGlobals[$key] = !in_array($key, $unsetKeys);
        }
        $luaGlobals = array_filter($luaGlobals);
        $luaGlobals = array_keys($luaGlobals);

        $this->assertEquals(
            $luaGlobals,
            array_values($globals)
        );
    }

    /**
     * @expectedException WebPower\LuaSandbox\LuaErrorException
     */
    function testAssertThrowsException()
    {
        $this->setExpectedException('\WebPower\LuaSandbox\LuaErrorException');
        $this->obj->run(<<<CODE
assert(false, 'Assertion failed')
CODE
        );
    }

    /**
     * @expectedException WebPower\LuaSandbox\LuaErrorException
     */
    function testErrorThrowsException()
    {
        $this->obj->run(<<<CODE
error('Some error')
CODE
        );
    }

    function testPhpCallbackFunction()
    {
        $args = false;
        $this->obj->assignCallable('doSomething', function() use(&$args) {
                $args = func_get_args();
            });
        $this->obj->run(<<<CODE
doSomething(1, 3, 3, 7, {1, 3, 3, 7})
CODE
        );
        $this->assertEquals(
            array(
                1, 3, 3, 7,
                array(1=> 1, 3, 3, 7)
            ),
            $args
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function testNonFunctionCallback()
    {
        $this->obj->assignCallable('abc', 123);
    }

    /**
     * @expectedException WebPower\LuaSandbox\Exception
     */
    function testInvalidCallbackName()
    {
        $this->obj->assignCallable('0abc', function() {});
    }

    function testVariables()
    {
        $this->obj->assignVar('valueFromPhp', 1337);
        $val = $this->obj->run(<<<CODE
return valueFromPhp
CODE
        );
        $this->assertEquals(1337, $val);
    }

    /**
     * @expectedException WebPower\LuaSandbox\InvalidVariableNameException
     */
    function testInvalidVariableName()
    {
        $this->setExpectedException('WebPower\LuaSandbox\Exception');
        $this->obj->assignVar('0abc', 1337);
    }

    /**
     * @expectedException WebPower\LuaSandbox\Exception
     */
    function testInvalidVariableValue()
    {
        $this->obj->assignVar('testFunc', function($a, $b) { return $a + $b; });
    }

    /**
     * @expectedException WebPower\LuaSandbox\InvalidVariableNameException
     */
    function testReservedKeywordVariable()
    {
        $this->obj->assignVar('break', 'test');
    }

    function testAssigningObject()
    {
        $obj = new \ArrayObject(array());
        $this->obj->assignObject('myArray', $obj);
        $this->obj->run('myArray.append(10)');
        $this->assertEquals(1, count($obj));
        $this->assertEquals(10, $obj[0]);

        $obj->testProperty = 'hoi';
        $res = $this->obj->run('return myArray.testProperty');
        $this->assertEquals('hoi', $res);

        $this->obj->run('myArray.testProperty = 123');
        $this->assertEquals(123, $obj->testProperty);
    }

    function testCallingLuaFunction()
    {
        $this->obj->run('function testFunc(a, b) return a + b end');
        $res = $this->obj->call('testFunc', array(1, 2));
        $this->assertEquals(3, $res);
    }

    /**
     * @expectedException \WebPower\LuaSandbox\Exception
     */
    function testCallingNonexistingLuaFunction()
    {
        $this->obj->call('nonexistingFunction');
    }

    /**
     * @expectedException \WebPower\LuaSandbox\InvalidVariableNameException
     */
    function testCallingInvalidLuaFunction()
    {
        $this->obj->call('023_asb');
    }
}
