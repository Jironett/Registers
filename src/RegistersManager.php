<?php
/**
 * This file is part of the Registers package.
 *
 * (c) Jiří Dušek (http://www.jironett.cz)
 *
 * For the full copyright and license information, please view the license.md
 * file that was distributed with this source code.
 */

namespace Jironett\Registers;


/**
 * Class RegistersManager
 * @package Jironett\Registers
 * @author Jiří Dušek
 */
class RegistersManager {

    /** @var array */
    private $registers = [];

    /**
     * @param array $data
     * @throws \Jironett\Registers\DuplicateKeyException
     */
    public function __construct(array $data = [])
    {
       foreach($data as $registryName => $values){
           $this->addRegistry($registryName, $values);
       }
    }

    /**
     * @param array $data
     * @throws \Jironett\Registers\InvalidStateException
     * @throws \Jironett\Registers\InvalidArgumentException
     */
    public function injectRegisters(array $data)
    {
        if ($this->registers) {
            throw new InvalidStateException('Registers have already been set up');
        }
        foreach($data as $registryName => $registry){
            if(!$registry instanceof Registry){
                throw new InvalidArgumentException('Registry has to contain only instance of Jironett\Registers\Registry');
            }
        }
        $this->registers = $data;
    }

    /**
     * @param int|string $name
     * @return \Jironett\Registers\Registry|null
     */
    public function getRegistry($name)
    {
        if(array_key_exists($name, $this->registers)) {
            return $this->registers[$name];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getRegisters()
    {
        return $this->registers;
    }

    /**
     * @param $name
     * @param array $params
     * @throws \Jironett\Registers\DuplicateKeyException
     */
    public function addRegistry($name, $params = [])
    {
        if(array_key_exists($name, $this->registers)){
            throw new DuplicateKeyException("The registry with name ". $name ." already exists");
        }
        $registry = new Registry();
        $dataType = (isset($params['dataType'])) ? $params['dataType'] : null;
        $class = (isset($params['class'])) ? $params['class'] : null;
        $registry->setDataType($dataType, $class);
        $this->registers[$name] = $registry;
    }

    /**
     * @param string $name
     */
    public function removeRegistry($name)
    {
        if(array_key_exists($name, $this->registers)) {
            unset($this->registers[$name]);
        }
    }

    public function clear()
    {
        $this->registers = [];
    }

}