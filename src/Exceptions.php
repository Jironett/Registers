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

use Exception;
use RuntimeException;

/**
 * Class DuplicateKeyException
 * @package Jironett\Registers
 * @author Jiří Dušek
 */
class DuplicateKeyException extends Exception {

}

/**
 * Class ItemNotExistException
 * @package Jironett\Registers
 * @author Jiří Dušek
 */
class KeyNotExistsException extends Exception {

}

/**
 * Class ItemNotExistException
 * @package Jironett\Registers
 * @author Jiří Dušek
 */
class RegistryNotExistsException extends Exception {

}

/**
 * Class InvalidArgumentException
 * @package Jironett\Registers
 * @author Jiří Dušek
 */
class InvalidArgumentException extends Exception {

}

/**
 * Class InvalidStateException
 * @package Jironett\Registers
 * @author Jiří Dušek
 */
class InvalidStateException extends RuntimeException {

}