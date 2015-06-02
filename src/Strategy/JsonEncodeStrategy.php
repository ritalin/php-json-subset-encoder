<?php

namespace JsonEncoder\Strategy;

interface JsonEncodeStrategy {
    /**
     * @param string field
     * @param JsonEncodeStrategy strategy
     */
    function append($field, JsonEncodeStrategy $strategy);
    
    /**
     * @param mixed
     * @retuen mixed
     */
    function serialize($value);
}
