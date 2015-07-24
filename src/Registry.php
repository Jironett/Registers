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
 * Class Register
 * @package Jironett\Registers
 * @author Jiří Dušek
 */
class Registry {

    /** @var string */
    private $dataType;

    /** @var string */
    private $class;

    /** @var array */
    private $items = [];

    /**
     * @param string $dataType
     * @param string $class
     */
    public function setDataType($dataType = null, $class = null)
    {
        $this->clear();
        switch($dataType){
            case 'registry' :
            case'integer' :
            case 'string' :
            case 'array' :
            case 'object' :
                $this->dataType = $dataType;
                break;
            case 'class' :
                $this->dataType = $dataType;
                $this->class = $class;
                break;
        }
    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @return string
     */
    public function getTypeClass()
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param int $position
     * @return mixed
     * @throws \Jironett\Registers\KeyNotExistsException
     */
    public function getItem($position)
    {
        if(!array_key_exists($position, $this->items)){
            throw new KeyNotExistsException("Item in this position doesn't exist");
        }
        return $this->items[$position];
    }

    /**
     * @param mixed $value
     * @param int $position
     * @return \Jironett\Registers\Registry $this
     * @throws \Jironett\Registers\InvalidArgumentException
     * @throws \Jironett\Registers\DuplicateKeyException
     */
    public function add($value, $position = null)
    {
        if(!$this->checkDataType($value)){
            throw new InvalidArgumentException('Data type of value is not allowed to add in this registry');
        }
        if(is_numeric($position) || is_string($position)){
            if(!array_key_exists($position, $this->items)){
                $this->items[$position] = $value;
            } else {
                throw new DuplicateKeyException('This registry already contains value with this position');
            }
        } else {
            $this->items[] = $value;
        }
        return $this;
    }

    /**
     * @param array $data
     * @throws \Jironett\Registers\InvalidArgumentException
     * @throws \Jironett\Registers\DuplicateKeyException
     */
    public function fill(array $data = [])
    {
        foreach($data as $position => $value){
            $this->add($value, $position);
        }
    }

    /**
     * @param int|string $position
     * @return \Jironett\Registers\Registry $this
     */
    public function remove($position)
    {
        unset($this->items[$position]);
        return $this;
    }

    public function clear()
    {
        $this->items = [];
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function checkDataType($value)
    {
        if($this->dataType === null){
            return true;
        } elseif($this->dataType === 'registry' && $value instanceof self){
            return true;
        } elseif($this->dataType === 'array' && is_array($value)){
            return true;
        } elseif($this->dataType === 'string' && is_string($value)){
            return true;
        } elseif($this->dataType === 'class' && $value instanceof $this->class){
            return true;
        } elseif($this->dataType === 'object' && is_object($value)){
            return true;
        } elseif($this->dataType === 'integer' && is_numeric($value)){
            return true;
        }
        return false;
    }

    /**
     * @param string $property
     * @return array|null
     */
    public function __get($property) {
        if(array_key_exists($property, $this->items)){
            return $this->items[$property];
        }
        if($property[0] === '_'){
            $num = substr($property, 1);
            if(array_key_exists($num, $this->items)){
                return $this->items[$num];
            }
        }
        if ($property === 'items') {
            return $this->items;
        }
        return null;
    }

    /**
     * @param string $name
     * @param array $args
     * @return \Jironett\Registers\Registry
     * @throws \Jironett\Registers\RegistryNotExistsException
     */
    public function __call($name, $args = [])
    {
        if(array_key_exists($name, $this->items)){
           if($this->items[$name] instanceof self){
                return $this->items[$name];
            }
            throw new RegistryNotExistsException("Registry with name ". $name ." doesn't exist");
        }
        $dataType = (isset($args[0])) ? $args[0] : null;
        $class = (isset($args[1])) ? $args[1] : null;
        return $this->items[$name] = new self($dataType, $class);
    }

}