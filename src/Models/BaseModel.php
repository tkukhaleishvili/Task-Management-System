<?php
namespace App\Models;

abstract class BaseModel implements \JsonSerializable {
    protected array $attributes = [];

    public function __construct(array $attributes = []) {
        $this->fill($attributes);
    }

    public function __get(string $key) {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, $value): void {
        $this->attributes[$key] = $value;
    }

    public function fill(array $attributes): void {
        foreach ($attributes as $k => $v) {
            $this->attributes[$k] = $v;
        }
    }

    public function toArray(): array {
        return $this->attributes;
    }

    public function jsonSerialize(): array {
        return $this->toArray();
    }
}
