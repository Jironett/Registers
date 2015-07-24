# Jironett\Registers

Registers are an extension for Nette framework. Registers allow sharing custom data between modules or simply where you need.

## Requirements

* PHP 5.4 or higher
* [Nette Framework](http://nette.org/)

## Installation

The best way to install Jironett\Registers is using [Composer](https://getcomposer.com)

```shell
$ composer require jironett/registers:@dev
```

## Usage

### Config

Add Registers to the extension part in config

```php
registers: Jironett\Registers\DI\RegistersExtension
```
Define all automaticaly generated registers common for your application in config.
There are integer, string, array, object, registry or class data types.

**Example:**

```php
registers:
    registers:
        AdminTemplate:
            dataType: registry
        FrontTemplate:
            dataType: class
            class: Your\Path\Class

```

### Extensions

Each extension that implements the IRegistersProvider interface can access to AdminTemplate and FrontTemplate registers from our example.

```php
   public function setRegisters(array $registers)
    {
        $menuRegistry = $registers['AdminTemplate']->menu();
        $pathsRegistry = $registers['AdminTemplate']->paths();

        $menuRegistry->add('whateEverYouWant')->add('whateEverYouWant')->add('whateEverYouWant');
        $pathsRegistry->add(['yourPath'], 'messagesLatte');

        return $registers;
    }
```

### Presenter

```php
/* @var \Jironett\Registers\RegistersManager @inject */
 private $registersManager;

 $adminTemplate = $registersManager->getRegistry('AdminTemlate');
```

### Basic possibilities

Registry can contain data or another registers and for every registry you can define its data type.

* `$reg->menu()` - Return the menu registry and if it does not exist it'll be created.
* `$reg->menu` - Return value of item menu the registry. If you set up another value than a registry and then you try get the value as registry by this way `$reg->menu()` you will get an exception because you won't access to registry!
* `$reg->paths()->setDataType('array')` - Create or reset the paths registry and set up array as data type
* `$reg->menu()->add($value [, $position])` - Add value to the menu registry.
* `$reg->menu()->items` or `$reg->menu()->getItems()` - Return array of all values.
* `$reg->menu()->position` or `$reg->menu()->_5` or `$reg->menu()->getItem($pos)` - Return value by position.