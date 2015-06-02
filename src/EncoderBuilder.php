<?php

namespace JsonEncoder;

use JsonEncoder\Strategy;

final class EncoderBuilder {
    public static function AsObject(ObjectMeta $meta) {
        return new EncoderBuilder(static::createObjectStrategy($meta));
    }
    
    public static function AsPrimitiveArray() {
        return new EncoderBuilder(new Strategy\ArrayEncodeStrategy);
    }
    
    public static function AsObjectArray(ObjectMeta $meta) {
        return new EncoderBuilder(
            new Strategy\ArrayEncodeStrategy(self::createObjectStrategy($meta))
        );
    }
    
    public static function AsAssocArray(ObjectMeta $meta) {
        return new EncoderBuilder(static::createObjectStrategy($meta));
    }
    
    private static function createObjectStrategy(ObjectMeta $meta) {
        if ($meta->class !== '') {
            $strategy = new Strategy\ObjectToArrayStrategy($meta->fields);
        
            if (count($meta->nestedMetas) > 0) {
                $strategy = new Strategy\ObjectSubsetStrategy($strategy);
            }
        }
        else {
            $strategy = new Strategy\AssocArrayEncodeStrategy($meta->fields);
        }
            
        foreach ($meta->nestedMetas as $field => $m) {
            $strategy->append($field, static::createObjectStrategy($m));
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
