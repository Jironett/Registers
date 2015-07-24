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

require __DIR__ . '/../bootstrap.php';

/**
 * Class RegistryTest
 * @package Jironett\Registers\Tests
 * @author Jiří Dušek
 */
class RegistryTest extends BaseTestCase {

    /** @var Jironett\Registers\Registry */
    private $regAll;

    /** @var Jironett\Registers\Registry */
    private $regString;

    /** @var Jironett\Registers\Registry */
    private $regInteger;

    /** @var Jironett\Registers\Registry */
    private $regArray;

    /** @var Jironett\Registers\Registry */
    private $regObject;

    /** @var Jironett\Registers\Registry */
    private $regClass;

    protected function setUp()
    {
        parent::setUp();
        $this->regAll = new Registry();
        $this->regString = new Registry();
        $this->regInteger = new Registry();
        $this->regArray = new Registry();
        $this->regObject = new Registry();
        $this->regClass = new Registry();
        $this->regRegistry = new Registry();

        $this->regString->setDataType('string');
        $this->regInteger->setDataType('integer', 'SomeClassName');
        $this->regArray->setDataType('array', [336]);
        $this->regObject->setDataType('object');
        $this->regClass->setDataType('class', 'DateTime');
        $this->regRegistry->setDataType('registry');
    }

    public function testTypeClassAndDataType()
    {
        Assert::same(null, $this->regAll->getDataType());
        Assert::same(null, $this->regAll->getTypeClass());

        Assert::same('string', $this->regString->getDataType());
        Assert::same(null, $this->regString->getTypeClass());

        Assert::same('integer', $this->regInteger->getDataType());
        Assert::same(null, $this->regInteger->getTypeClass());

        Assert::same('array', $this->regArray->getDataType());
        Assert::same(null, $this->regArray->getTypeClass());

        Assert::same('object', $this->regObject->getDataType());
        Assert::same(null, $this->regObject->getTypeClass());

        Assert::same('class', $this->regClass->getDataType());
        Assert::same('DateTime', $this->regClass->getTypeClass());

        Assert::same('registry', $this->regRegistry->getDataType());
        Assert::same(null, $this->regRegistry->getTypeClass());
    }

    public function testAdd()
    {
        $dateTime = new \DateTime();
        $values = [0 => 'value', 17 => 859, 18 => 'thirdValue', 19 => [0 => 'myValues..'], 20 => $dateTime];
        $this->regAll->add('value');
        $this->regAll->add(859, 17);
        $this->regAll->add('thirdValue')->add(['myValues..'])->add($dateTime);
        Assert::same($values, $this->regAll->items);

        $values['special'] = 'specialValue';
        $this->regAll->add('specialValue', 'special');
        Assert::same($values, $this->regAll->items);

        Assert::exception(function(){
            $this->regAll->add('somethingAnother', 'special');
        }, 'Jironett\Registers\DuplicateKeyException');
        Assert::exception(function(){
            $this->regAll->add('somethingAnother', 17);
        }, 'Jironett\Registers\DuplicateKeyException');

        $this->regString->add('myString', 5);
        Assert::same([5 => 'myString'],  $this->regString->items);

        $this->regString->add('These se items..', 'items');
        Assert::same([5 => 'myString', 'items' => 'These se items..'],  $this->regString->getItems());
        Assert::exception(function(){
            $this->regString->add([404]);
        }, 'Jironett\Registers\InvalidArgumentException');

        $this->regInteger->add(225);
        Assert::same([0 => 225], $this->regInteger->items);
        Assert::exception(function(){
            $this->regInteger->add('somethingAnother');
        }, 'Jironett\Registers\InvalidArgumentException');

        $this->regObject->add($dateTime);
        Assert::same([0 => $dateTime], $this->regObject->items);
        Assert::exception(function(){
            $this->regObject->add('somethingAnother');
        }, 'Jironett\Registers\InvalidArgumentException');

        $this->regClass->add($dateTime);
        $newReg = new Registry();
        Assert::same([0 => $dateTime], $this->regClass->items);
        Assert::exception(function() use ($newReg){
            $this->regClass->add($newReg);
        }, 'Jironett\Registers\InvalidArgumentException');

        $this->regArray->add([12, 24]);
        Assert::same([0 => [12, 24]], $this->regArray->items);
        $this->regArray->add([]);
        Assert::same([0 => [12, 24], 1 => []], $this->regArray->items);
        Assert::exception(function() use ($newReg){
            $this->regArray->add($newReg);
        }, 'Jironett\Registers\InvalidArgumentException');
    }

    public function testClear()
    {
        $this->regInteger->add(2);
        $this->regInteger->clear();
        Assert::same([], $this->regInteger->items);
    }

    public function testGetItems()
    {
        $this->regInteger->clear();
        $this->regInteger->add(100, 99);
        Assert::same(100, $this->regInteger->_99);
        Assert::same(100, $this->regInteger->getItem(99));
        Assert::same([99 => 100], $this->regInteger->items);
        $this->regInteger->add(1000, 'items');
        Assert::same(1000, $this->regInteger->items);
        Assert::same([99 => 100, 'items' => 1000], $this->regInteger->getItems());

        $this->regInteger->clear();
        $this->regInteger->add(1000);
        Assert::same(['0' => 1000], $this->regInteger->items);
    }

    public function testRemove()
    {
        $this->regInteger->clear();
        $this->regInteger->add(1948, 21);
        $this->regInteger->remove(21);
        Assert::same([], $this->regInteger->items);
    }

    public function testMagicAccessToRegistry()
    {
        $this->regAll->clear();
        Assert::same([], $this->regAll->items);
        Assert::same(null, $this->regAll->path);
        Assert::type('Jironett\Registers\Registry', $this->regAll->path());

        $this->regAll->add('values..', 'template');
        Assert::exception(function(){
            $this->regAll->template();
        }, 'Jironett\Registers\RegistryNotExistsException');
    }

}

test(new RegistryTest());