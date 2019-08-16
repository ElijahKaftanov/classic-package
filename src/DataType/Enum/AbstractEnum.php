<?php

namespace Classic\Package\Support\DataType\Enum;

abstract class AbstractEnum
{
    /** @var array Reflection cache */
    private static $map = [];
    /** @var string */
    private $key;
    /** @var mixed */
    private $value;

    /**
     * Creates new XG_ENUM instance
     * @param mixed $value
     * @throws \OutOfRangeException
     */
    public function __construct($value)
    {
        if (is_object($value) && $value instanceof static) {
            $value = $value->value;
        }

        if (!static::isValid($value)) {
            throw new \OutOfBoundsException(
                'Unable to create enum ' . static::class . ' with '
                . (is_scalar($value) ? 'value ' . $value : 'not scalar value')
            );
        }

        $keysMap = static::keysMap();

        $this->value = $value;
        $this->key = $keysMap[$value];
    }

    /**
     * Reads constants list using reflection
     * @throws \Exception
     * @return void
     */
    private static function init()
    {
        $cls = static::class;
        $ref = new \ReflectionClass($cls);

        self::$map[$cls] = [
            $ref->getConstants(),
            array_flip($ref->getConstants())
        ];

        if (!is_array(self::$map[$cls][0]) || count(self::$map[$cls][0]) === 0) {
            throw new \Exception('Unable to build XG_ENUM without values for ' . $cls);
        }
    }

    /**
     * Returns linear list
     * @return array
     */
    public static function values()
    {
        return array_values(static::valuesMap());
    }

    /**
     * Returns associative array, where keys are constant names and values are constant values
     * @return array
     */
    protected static function valuesMap()
    {
        $class = static::class;
        if (!isset(self::$map[$class])) {
            static::init();
        }

        return self::$map[$class][0];
    }

    /**
     * Returns associative array, where keys are constant values and values are constant names
     * @return array
     */
    protected static function keysMap()
    {
        $class = static::class;
        if (!isset(self::$map[$class])) {
            static::init();
        }

        return self::$map[$class][1];
    }

    /**
     * Returns true if provided value is value XG_ENUM constant
     * @param mixed $value
     * @return bool
     */
    public static function isValid($value)
    {
        return in_array($value, static::valuesMap(), true);
    }

    /**
     * Returns constant name
     * @return string
     */
    protected function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns true if $to contains same constant value or if $to contains same type XG_ENUM with same value
     * @param mixed $to
     * @return bool
     */
    public function isEqual($to)
    {
        if ($to === null) {
            return false;
        }

        if (is_object($to) && $to instanceof static) {
            return $this->value === $to->value;
        }

        return $this->value === $to;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this) . '::' . $this->getKey() . '=' . $this->getValue();
    }

    /**
     * @param string|static $value
     * @return static
     */
    public static function instance($value)
    {
        if ($value instanceof static) {
            return $value;
        }

        return new static($value);
    }

    /**
     * @param $name
     * @param $arguments
     * @return static
     */
    public static function __callStatic($name, $arguments)
    {
        return new static(constant("static::$name"));
    }
}
