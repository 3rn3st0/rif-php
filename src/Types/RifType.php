<?php

declare(strict_types=1);

namespace TurecoLabs\Rif\Types;

enum RifType: string
{
    case NATURAL = 'V';
    case FOREIGN_ID = 'E';
    case LEGAL = 'J';
    case FOREIGN_PASSPORT = 'P';
    case GOVERNMENT = 'G';
    case COMMUNAL = 'C';

    /**
     * Valores numéricos según tu código original
     */
    public function getNumericValue(): int
    {
        return match ($this) {
            self::NATURAL => 1,
            self::FOREIGN_ID => 2,
            self::LEGAL => 3,
            self::FOREIGN_PASSPORT => 4,
            self::GOVERNMENT => 5,
            self::COMMUNAL => 3,
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::NATURAL => 'Persona Natural',
            self::FOREIGN_ID => 'Extranjero con Cédula',
            self::LEGAL => 'Persona Jurídica',
            self::FOREIGN_PASSPORT => 'Extranjero con Pasaporte',
            self::GOVERNMENT => 'Gobierno',
            self::COMMUNAL => 'Consejo Comunal',
        };
    }

    public static function fromPrefix(string $prefix): self
    {
        return self::tryFrom($prefix)
            ?? throw new \InvalidArgumentException("Prefijo RIF inválido: {$prefix}");
    }
}
