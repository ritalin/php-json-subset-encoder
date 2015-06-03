<?php

namespace JsonEncoder\Formatter;

interface ObjectFormatable {
    /**
     * @param mixed value
     * @return string
     */
    function format($value);
    
    /**
     * @return string
     */
    function type();
}
