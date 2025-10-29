<?php
namespace App\Models\Traits;

trait Timestampable {
    public function touch(): void {
        $this->updated_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
    }
}
