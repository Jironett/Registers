<?php
/**
 * This file is part of the Registers package.
 *
 * (c) Jiří Dušek (http://www.jironett.cz)
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

namespace Jironett\Registers\DI;


use Nette\DI\CompilerExtension;
use Jironett\Registers\Registry;

/**
 * Class RegistersExtension
 * @package Jironett\Registers\DI
 * @author Jiří Dušek
 */
class RegistersExtension extends CompilerExtension
{
    /** @var array */
    private $registers = [];

    public function loadConfiguration()
    {
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('registersManager'))
                ->setClass('Jironett\Registers\RegistersManager');

        foreach($config['registers'] as $registryName => $values) {
            if (array_key_exists($registryName, $this->registers)) {
                throw new DuplicateKeyException("The Registry with name " . $registryName . " already exists");
            }
            $registry = new Registry();
            $dataType = (isset($values['dataType'])) ? $values['dataType'] : null;
            $class = (isset($values['class'])) ? $values['class'] : null;
            $registry->setDataType($dataType, $class);
            $this->registers[$registryName] = $registry;
        }
    }

    public function beforeCompile()
    {
        foreach ($this->compiler->getExtensions() as $extension) {
            if (!$extension instanceof IRegistersProvider) {
                continue;
            }
            $this->registers = $extension->setRegisters($this->registers);
        }
        $builder = $this->getContainerBuilder();
        $registersManagerDefinition = $builder->findByType('Jironett\Registers\RegistersManager')[$this->prefix('registersManager')];
        $registersManagerDefinition->addSetup('injectRegisters', [$this->registers]);
    }

}