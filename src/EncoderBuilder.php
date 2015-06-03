<?php

namespace JsonEncoder;

use JsonEncoder\Strategy;

final class EncoderBuilder {
    public static function ofObject(FilterRule $rule) {
        return new EncoderBuilder(static::createObjectStrategy($rule));
    }
    
    public static function ofPrimitiveArray() {
        return new EncoderBuilder(new Strategy\ArrayEncodeStrategy);
    }
    
    public static function ofObjectArray(FilterRule $rule) {
        return new EncoderBuilder(
            new Strategy\ArrayEncodeStrategy(self::createObjectStrategy($rule))
        );
    }
    
    public static function ofAssocArray(FilterRule $rule) {
        return new EncoderBuilder(static::createObjectStrategy($rule));
    }
    
    private static function createObjectStrategy(FilterRule $rule) {
        if ($rule->isObjectRule()) {
            $strategy = new Strategy\ObjectToArrayStrategy($rule->fields);
        
            if (count($rule->nestedFilters) > 0) {
                $strategy = new Strategy\ObjectSubsetStrategy($strategy);
            }
        }
        else {
            $strategy = new Strategy\AssocArrayEncodeStrategy($rule->fields);
        }
            
        foreach ($rule->nestedFilters as $field => $r) {
            $strategy->append($field, static::createObjectStrategy($r));
        }
        
        return $strategy;
    }
    
    /**
     * @var JsonEncodeStrategy
     */
    private $strategy;
    
    private function __construct(Strategy\JsonEncodeStrategy $strategy) {
        $this->strategy = $strategy;
    }
    
    /**
     * @return JsonEncodeStrategy
     */
    public function strategy() {
        return$this->strategy;
    }
    
    /**
     * @param mixed value
     * @return JsonEncodeSerializer
     */
    public function build($value) {
        return new Serializer\JsonEncodeSerializer($value, $this->strategy);
    }
}
