<?php
/**
 * This file is part of the Registers package.
 *
 * (c) Jiří Dušek (http://www.jironett.cz)
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 *
 * @testCase
 */

namespace Jironett\Registers\Tests;

use Tester\Assert;
use Jironett\Registers\Registry;
use Jironett\Registers\RegistersManager;
use Datetime;

require __DIR__ . '/../bootstrap.php';

/**
 * Class RegistersManagerTest
 * @package Jironett\Registers\Tests
 * @author Jiří Dušek
 */
class RegistersManagerTest extends BaseTestCase
{

    /** @var Jironett\Registers\RegistersManager */
    private $registersManager;

    protected function setUp()
    {
        parent::setUp();
        $this->registersManager = new RegistersManager();
    }

    public function testAddAndGet()
    {
        Assert::same([], $this->registersManager->getRegisters());
        $this->registersManager->addRegistry('adminTemplate', ['dataType' => 'registry']);

        Assert::same(null, $this->registersManager->getRegistry('anotherTemplate'));
        Assert::type('Jironett\Registers\Registry', $this->registersManager->getRegistry('adminTemplate'));

        $this->registersManager->addRegistry('frontTemplate', ['dataType' => 'registry']);
        Assert::count(2, $this->registersManager->getRegisters());
        Assert::type('array', $this->registersManager->getRegisters());

        Assert::exception(function(){
            $this->registersManager->addRegistry('frontTemplate');
        }, 'Jironett\Registers\DuplicateKeyException');

        $this->registersManager->addRegistry('regClass', ['dataType' => 'class', 'class' => 'Namespace\Class']);
        $regClass = $this->registersManager->getRegistry('regClass');
        Assert::same('class', $regClass->getDataType());
        Assert::same('Namespace\Class', $regClass->getTypeClass());
    }

    public function testClear()
    {
        $this->registersManager->addRegistry('paths', ['dataType' => 'array']);
        $this->registersManager->clear();
        Assert::same([], $this->registersManager->getRegisters());
    }

    public function testRemove()
    {
        $this->registersManager->addRegistry('macros');
        $this->registersManager->removeRegistry('macros');
        Assert::same(null, $this->registersManager->getRegistry('macros'));
    }

    public function testInjectRegisters()
    {
        $arr = ['first' => new Registry(), 'second' => new Registry()];
        $regManager = new RegistersManager();
        $regManager->addRegistry('fruits');
        Assert::exception(function() use ($regManager, $arr){
            $regManager->injectRegisters($arr);
        }, 'Jironett\Registers\InvalidStateException');
        $regManager->clear();
        $regManager->injectRegisters($arr);
        Assert::same($arr, $regManager->getRegisters());

        $regManager->clear();
        $arr = ['first' => new Registry(), 'second' => new Datetime()];
        Assert::exception(function() use ($regManager, $arr){
            $regManager->injectRegisters($arr);
        }, 'Jironett\Registers\InvalidArgumentException');
    }

    public function testCreateRegistersManager()
    {
        $arr = [ 'first' => ['dataType' => 'registry'], 'second' => ['dataType' => 'string']];
        $regManager = new RegistersManager($arr);
        Assert::count(2, $regManager->getRegisters());
        Assert::same('string', $regManager->getRegistry('second')->getDataType());
    }

}

test(new RegistersManagerTest());