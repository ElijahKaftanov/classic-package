<?php declare(strict_types=1);

namespace Classic\Package\Support\DataType\Enum;


abstract class AbstractConstantCollection
{
    private static $memory = [];

    final public static function has($value): bool
    {
        if (!isset(self::$memory[static::class])) {
            self::init();
        }

        if (!is_scalar($value)) {
            throw new UnexpectedValueException('Argument 0 should be scalar!');
        }

        return isset(self::$memory[static::class][1][$value]);
    }

    private static function init()
    {
        $cls = static::class;
        $ref = new \ReflectionClass($cls);

        self::$memory[$cls] = [
            $c = $ref->getConstants(),
            array_flip($c)
        ];
    }
}