<?php

namespace App\Enums;
enum PostitColors: string
{
    case ROSE = '#ff7eb9';
    case ROSE_CLAIR = '#ff65a3';
    case CYAN = '#7afcff';
    case JAUNE = '#feff9c';
    case JAUNE_CLAIR = '#fff740';

    public static function getColors(): array
    {
        return [
            'Rose' => self::ROSE->value,
            'Rose clair' => self::ROSE_CLAIR->value,
            'Cyan' => self::CYAN->value,
            'Jaune' => self::JAUNE->value,
            'Jaune clair' => self::JAUNE_CLAIR->value,
        ];
    }
}
