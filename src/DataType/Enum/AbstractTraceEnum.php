<?php declare(strict_types=1);

namespace Classic\Package\Support\DataType\Enum;


abstract class AbstractTraceEnum extends AbstractEnum
{
    /**
     * @var array
     */
    private static $storage = [];

    /**
     * TraceEnum constructor.
     * @internal
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct($value);
    }

    /**
     * @inheritdoc
     */
    public static function instance($value)
    {
        if ($value instanceof static) {
            return $value;
        }

        return static::trace($value);
    }

    /**
     * @inheritdoc
     */
    public static function __callStatic($name, $arguments)
    {
        return static::trace(constant("static::$name"));
    }

    /**
     * @param string $value
     * @return static
     */
    private static function trace(string $value): self
    {
        if (!isset(self::$storage[__CLASS__][$value])) {
            return self::$storage[__CLASS__][$value] = new static($value);
        }

        return self::$storage[__CLASS__][$value];
    }
}