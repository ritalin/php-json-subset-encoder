<?php

namespace JsonEncoder;

use JsonEncoder\Strategy;
use JsonEncoder\Formatter;

final class EncoderBuilder {
    public static function ofObject(FilterRule $rule) {
        return new EncoderBuilder(static::createFieldStrategy($rule));
    }
    
    public static function ofPrimitiveArray() {
        return new EncoderBuilder(new Strategy\ArrayEncodeStrategy);
    }
    
    public static function ofObjectArray(FilterRule $rule) {
        return new EncoderBuilder(
            new Strategy\ArrayEncodeStrategy(self::createFieldStrategy($rule))
        );
    }
    
    public static function ofAssocArray(FilterRule $rule) {
        return new EncoderBuilder(static::createFieldStrategy($rule));
    }
    
    private static function createFieldStrategy(FilterRule $rule) {
        if ($rule->isObjectRule()) {
            $strategy = new Strategy\ObjectToArrayStrategy($rule);
        
            if (count($rule->nestedFilters) > 0) {
                $strategy = new Strategy\ObjectSubsetStrategy($strategy);
            }
        }
        else {
            $strategy = new Strategy\AssocArrayEncodeStrategy($rule);
        }
            
        foreach ($rule->nestedFilters as $field => $r) {
            $strategy->append($field, static::createFieldStrategy($r));
        }
        
        return $strategy;
    }
    
    /**
     * @var JsonEncodeStrategy
     */
    private $strategy;
    
    /**
     * Formatters\ObjectFormattable[]
     */
    private $formatters;
    
    private function __construct(Strategy\JsonEncodeStrategy $strategy) {
        $this->strategy = $strategy;
        $this->formatters = [
            \DateTime::class => new \JsonEncoder\Formatter\DateTimeFormatter
        ];
    }
    
    public function formatWith(Formatters\ObjectFormattable $formatter) {
        $this->formatters[$formatter->type()] = $formatter;
    }
    
    /**
     * @return JsonEncodeStrategy
     */
    public function strategy() {
        return $this->strategy;
    }
    
    public function formatters() {
        return $this->formatters;
    }
    
    /**
     * @param mixed value
     * @return JsonEncodeSerializer
     */
    public function build($value) {
        return new Serializer\JsonEncodeSerializer($value, $this->strategy, $this->formatters);
    }
}
