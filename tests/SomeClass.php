<?php

namespace Test;

class SomeClass {
    private $id;
    private $text;
    
    public function __construct($id, $text) {
        $this->id = $id;
        $this->text = $text;
    }
    
    public function id() { return $this->id; }
    private function text() { return $this->text; }
    public function getText() { return $this->text; }
}
