<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NF_FU_VENDOR\Monolog\Processor;

use NF_FU_VENDOR\Monolog\Utils;
/**
 * Processes a record's message according to PSR-3 rules
 *
 * It replaces {foo} with the value from $context['foo']
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class PsrLogMessageProcessor implements \NF_FU_VENDOR\Monolog\Processor\ProcessorInterface
{
    /**
     * @param  array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        if (\false === \strpos($record['message'], '{')) {
            return $record;
        }
        $replacements = array();
        foreach ($record['context'] as $key => $val) {
            if (\is_null($val) || \is_scalar($val) || \is_object($val) && \method_exists($val, "__toString")) {
                $replacements['{' . $key . '}'] = $val;
            } elseif (\is_object($val)) {
                $replacements['{' . $key . '}'] = '[object ' . \NF_FU_VENDOR\Monolog\Utils::getClass($val) . ']';
            } else {
                $replacements['{' . $key . '}'] = '[' . \gettype($val) . ']';
            }
        }
        $record['message'] = \strtr($record['message'], $replacements);
        return $record;
    }
}
