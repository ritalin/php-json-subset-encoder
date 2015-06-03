<?php

namespace JsonEncoder\Formatter;

final class DateTimeFormatter implements ObjectFormatable {
    /**
     * {inheritdoc}
     */
    function type() {
        return \DateTime::class;
    }

    /**
     * {inheritdoc}
     */
    public function format($value) {
        return $value->format(\DateTime::ISO8601);
    }
}
