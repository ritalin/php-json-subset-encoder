<?php

namespace Test;

class JsonArrayEncoder implements \JsonSerializable {
    private $values;
    private $fields;

    public function __construct(array $values, array $fields) {
        $this->values = $values;
        $this->fields = $fields;
    }
    
    /**
     * {inheritdoc}
     */
    public function jsonSerialize() {
        if (is_object(current($this->values))) {
            return array_map(
                function ($obj) {
                    return new JsonSubsetObjectEncoder($obj, $this->fields);
                },
                $this->values
            );
        }
        else {
            return $this->values;
        }
    }
}
