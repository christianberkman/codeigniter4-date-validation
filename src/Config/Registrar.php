<?php

declare(strict_types=1);

/**
 * CodeIgniter4 Date Validation
 * Registar file
 */

namespace ChristianBerkman\DateValidation\Config;

use ChristianBerkman\DateValidation\DateValidation;

class Registrar
{
    /**
     * Register the DateValidation class in CodeIgniter's Validation Rulesets
     */
    public static function Validation(): array
    {
        return [
            'ruleSets' => [
                DateValidation::class,
            ],
        ];
    }
}
