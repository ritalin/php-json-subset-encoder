<?php

namespace Test;

class PublicClass {
    public $id;
    public $text;
    
    public function __construct($id, $text) {
        $this->id = $id;
        $this->text = $text;
    }
}
