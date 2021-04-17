<?php

namespace NF_FU_VENDOR\JmesPath;

/**
 * Returns data from the input array that matches a JMESPath expression.
 *
 * @param string $expression Expression to search.
 * @param mixed $data Data to search.
 *
 * @return mixed|null
 */
if (!\function_exists(__NAMESPACE__ . '\\search')) {
    function search($expression, $data)
    {
        return \NF_FU_VENDOR\JmesPath\Env::search($expression, $data);
    }
}
