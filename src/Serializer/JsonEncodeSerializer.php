<?php

namespace JsonEncoder\Serializer;

use JsonEncoder\Strategy\JsonEncodeStrategy;
use JsonEncoder\Formatter\ObjectFormatable;

class JsonEncodeSerializer implements \JsonSerializable {
    /**
     * @var mixed
     */
    private $value;
    
    /**
     * @var JsonEncodeStrategy
     */
    private $strategy;
    
    /**
     * @var ObjectFormatable[]
     */
    private $formatters;
    
    public function __construct($value, JsonEncodeStrategy $strategy, array $formatters) {
        $this->value = $value;
        $this->strategy = $strategy;
        $this->formatters = $formatters;
    }
    
    /**
     * @return mixed
     */
    public function jsonSerialize() {
        return $this->strategy->serialize($this->value, $this->formatters);
    }
}
