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


/**
 * Interface IRegistersProvider
 * @package Jironett\Registers\DI
 * @author Jiří Dušek
 */
interface IRegistersProvider
{

    /**
     * Return array of registers with data, which will be added to RegistersManager service
     * @param array
     * @return array
     */
    function setRegisters(array $registers);

}