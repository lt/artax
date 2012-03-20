<?php

/**
 * Artax PcntlInterruptException File
 * 
 * PHP version 5.4
 * 
 * @category   Artax
 * @package    Exceptions
 * @author     Daniel Lowrey <rdlowrey@gmail.com>
 */

namespace Artax\Exceptions;

/**
 * Exception thrown when termination is requested via PCNTL
 * 
 * @category   Artax
 * @package    Exceptions
 * @author     Daniel Lowrey <rdlowrey@gmail.com>
 */
class PcntlInterruptException extends \RuntimeException
{
}
