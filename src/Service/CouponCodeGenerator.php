<?php

// src/Service/CouponCodeGenerator.php
namespace App\Service;

final class CouponCodeGenerator
{
    public function generate(array $parts = []): string
    {
        // mélange d’infos + random → 16 alphanum
        $raw = implode('|', $parts). '|'. microtime(true). '|'. bin2hex(random_bytes(8));
        $hash = strtoupper(substr(preg_replace('/[^A-Z0-9]/i','', hash('sha256', $raw)), 0, 16));
        return $hash; // ex: C9FD28A1B2C3D4E5
    }
}
