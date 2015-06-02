<?php

namespace JsonEncoder\Serializer;

use JsonEncoder\Strategy\JsonEncodeStrategy;

class JsonEncodeSerializer implements \JsonSerializable {
    /**
     * @var mixed
     */
    private $value;
    
    /**
     * @var JsonEncodeStrategy
     */
    private $strategy;
    
    public function __construct($value, JsonEncodeStrategy $strategy) {
        $this->value = $value;
        $this->strategy = $strategy;
    }
    
    /**
     * @return mixed
     */
    public function jsonSerialize() {
        return $this->strategy->serialize($this->value);
    }
}
