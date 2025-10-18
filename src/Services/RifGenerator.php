<?php

declare(strict_types=1);

namespace TurecoLabs\Rif\Services;

use TurecoLabs\Rif\Rif;
use TurecoLabs\Rif\Types\RifType;
use TurecoLabs\Rif\Exceptions\RifValidationException;

final class RifGenerator
{
    /**
     * Genera un RIF válido aleatorio del tipo especificado
     */
    public static function generate(RifType $type = null): Rif
    {
        $type = $type ?? self::randomType();

        do {
            $number = self::generateRandomNumber();
            $rifString = self::buildRifString($type, $number);
        } while (!Rif::isValid($rifString));

        return Rif::create($rifString);
    }

    /**
     * Genera un RIF válido basado en un número específico
     */
    public static function fromNumber(string $number, RifType $type = null): Rif
    {
        $type = $type ?? RifType::LEGAL;

        if (!self::isValidNumber($number)) {
            throw new \InvalidArgumentException('El número debe tener exactamente 8 dígitos');
        }

        $rifString = self::buildRifString($type, $number);

        if (!Rif::isValid($rifString)) {
            throw new \RuntimeException('No se pudo generar un RIF válido con el número proporcionado');
        }

        return Rif::create($rifString);
    }

    /**
     * Genera múltiples RIFs válidos
     */
    public static function generateMultiple(int $count, RifType $type = null): array
    {
        if ($count < 1 || $count > 1000) {
            throw new \InvalidArgumentException('El conteo debe estar entre 1 y 1000');
        }

        $rifs = [];
        for ($i = 0; $i < $count; $i++) {
            $rifs[] = self::generate($type);
        }

        return $rifs;
    }

    /**
     * Genera un RIF válido para cada tipo disponible
     */
    public static function generateOneOfEachType(): array
    {
        $rifs = [];
        foreach (RifType::cases() as $type) {
            $rifs[$type->value] = self::generate($type);
        }

        return $rifs;
    }

    /**
     * Genera un RIF válido secuencial (útil para testing)
     */
    public static function generateSequential(int $sequence, RifType $type = null): Rif
    {
        $type = $type ?? RifType::LEGAL;

        if ($sequence < 1 || $sequence > 99_999_999) {
            throw new \InvalidArgumentException('La secuencia debe estar entre 1 y 99999999');
        }

        $number = str_pad((string) $sequence, 8, '0', STR_PAD_LEFT);

        return self::fromNumber($number, $type);
    }

    /**
     * Verifica si un número es válido para generar RIF
     */
    private static function isValidNumber(string $number): bool
    {
        return strlen($number) === 8 && ctype_digit($number) && (int) $number >= 1;
    }

    /**
     * Genera un número aleatorio de 8 dígitos
     */
    private static function generateRandomNumber(): string
    {
        return str_pad((string) random_int(1, 99_999_999), 8, '0', STR_PAD_LEFT);
    }

    /**
     * Selecciona un tipo de RIF aleatorio
     */
    private static function randomType(): RifType
    {
        $types = RifType::cases();
        return $types[array_rand($types)];
    }

    /**
     * Construye el string RIF completo con dígito verificador
     */
    private static function buildRifString(RifType $type, string $number): string
    {
        // Primero calculamos el dígito verificador
        $checkDigit = self::calculateCheckDigit($type, $number);

        return $type->value . $number . $checkDigit;
    }

    /**
     * Calcula el dígito verificador (mismo algoritmo que en la clase Rif)
     */
    private static function calculateCheckDigit(RifType $type, string $number): int
    {
        $reversedDigits = array_reverse(str_split($number));
        $sum = 0;
        $weights = [2, 3, 4, 5, 6, 7, 2, 3];

        foreach ($reversedDigits as $index => $digit) {
            $sum += (int) $digit * $weights[$index];
        }

        $sum += $type->getNumericValue() * 4;
        $modulo = $sum % 11;
        $checkDigit = 11 - $modulo;

        return match ($checkDigit) {
            10, 11 => 0,
            default => $checkDigit,
        };
    }
}
