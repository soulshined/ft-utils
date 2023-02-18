<?php

namespace FT\Utils;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class Optional {

    private function __construct(private readonly mixed $value = new Absent) { }

    /**
     * @throws InvalidArgumentException if `$value` is null
     */
    public static function of(mixed $value) : Optional {
        if (is_null($value))
            throw new InvalidArgumentException("No value present");

        if ($value instanceof Absent)
            throw new InvalidArgumentException(Absent::class);

        return new Optional($value);
    }

    public static function ofNullable(mixed $value) : Optional {
        if (is_null($value))
            return new Optional;

        return static::of($value);
    }

    /**
     * An Optional with absence of value
     */
    public static function absent() : Optional {
        return new Optional();
    }

    /**
     * @throws RuntimeException if this value is absent. Check for presence of value with `isPresent()`
     */
    public function get() : mixed {
        if (!$this->isPresent())
            throw new RuntimeException();

        return $this->value;
    }

    public function isPresent(): bool
    {
        return !($this->value instanceof Absent);
    }

    public function ifPresent(callable $consumer) : void {
        if ($this->isPresent())
            call_user_func($consumer, $this->value);
    }

    public function ifPresentOrElse(callable $consumer, callable $else) : void {
        if ($this->isPresent())
            call_user_func($consumer, $this->value);
        else call_user_func($else);
    }

    /**
     * @return mixed this value if present otherwise `$else`
     */
    public function orElse(mixed $else): mixed {
        return $this->isPresent() ? $this->value : $else;
    }

    /**
     * @return mixed this value if present otherwise throw
     */
    public function orElseThrow(?Throwable $throwable = null) : mixed {
        if (!$this->isPresent()) {
            if (!is_null($throwable))
                throw $throwable;

            throw new RuntimeException("No value present");
        }

        return $this->value;
    }

    public function map(callable $mapper) : Optional {
        if (!$this->isPresent()) return static::absent();

        return static::ofNullable(call_user_func($mapper, $this->value));
    }

    public function equals($object): bool {
        if (is_null($object)) return false;
        if (!($object instanceof Optional)) return false;

        return $object->isPresent() === $this->isPresent() &&
               ($object->get() <=> $this->value) === 0;
    }

}
