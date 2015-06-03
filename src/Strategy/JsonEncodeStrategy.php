<?php

namespace JsonEncoder\Strategy;

use JsonEncoder\Formatter\ObjectFormatable;

interface JsonEncodeStrategy {
    /**
     * @param string field
     * @param JsonEncodeStrategy strategy
     */
    function append($field, JsonEncodeStrategy $strategy);
    
    /**
     * @param mixed
     * @param ObjectFormatable[]
     * @retuen mixed
     */
    function serialize($value, array $formatters);
}
