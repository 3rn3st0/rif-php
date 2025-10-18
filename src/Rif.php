<?php

declare(strict_types=1);

namespace TurecoLabs\Rif;

use TurecoLabs\Rif\Exceptions\RifValidationException;
use TurecoLabs\Rif\Formatters\RifFormatter;
use TurecoLabs\Rif\Services\RifGenerator;
use TurecoLabs\Rif\Types\RifType;
use TurecoLabs\Rif\Validators\FormatValidator;

final class Rif
{
    private const RIF_LENGTH = 10;
    private const MODULO = 11;

    private const WEIGHTS = [2, 3, 4, 5, 6, 7, 2, 3];

    private function __construct(
        private readonly string $rawRif,
        private readonly RifType $type,
        private readonly string $number,
        private readonly int $checkDigit
    ) {
    }

    public static function create(string $rif): self
    {
        $normalizedRif = self::normalize($rif);

        self::validateStructure($normalizedRif);

        $type = RifType::fromPrefix($normalizedRif[0]);
        $number = substr($normalizedRif, 1, 8);
        $checkDigit = (int) substr($normalizedRif, -1);

        self::validateCheckDigit($type, $number, $checkDigit);

        return new self($normalizedRif, $type, $number, $checkDigit);
    }

    public static function isValid(string $rif): bool
    {
        try {
            self::create($rif);
            return true;
        } catch (RifValidationException) {
            return false;
        }
    }

    private static function normalize(string $rif): string
    {
        return strtoupper(trim($rif));
    }

    private static function validateStructure(string $rif): void
    {
        if (strlen($rif) !== self::RIF_LENGTH) {
            throw RifValidationException::invalidLength($rif);
        }

        if (!preg_match('/^[VEJPGC]\d{8}\d$/', $rif)) {
            throw RifValidationException::invalidFormat($rif);
        }
    }

    private static function validateCheckDigit(RifType $type, string $number, int $checkDigit): void
    {
        $calculatedDigit = self::calculateCheckDigit($type, $number);

        if ($calculatedDigit !== $checkDigit) {
            throw RifValidationException::invalidCheckDigit($checkDigit, $calculatedDigit);
        }
    }

    private static function calculateCheckDigit(RifType $type, string $number): int
    {
        // Revertir el número
        $reversedDigits = array_reverse(str_split($number));
        $sum = 0;

        // Aplicar pesos a los dígitos invertidos
        foreach ($reversedDigits as $index => $digit) {
            $sum += (int) $digit * self::WEIGHTS[$index];
        }

        // Agregar el tipo × 4 (como en tu código original)
        $sum += $type->getNumericValue() * 4;

        $modulo = $sum % self::MODULO;
        $checkDigit = self::MODULO - $modulo;

        // Reglas especiales del algoritmo
        return match ($checkDigit) {
            10, 11 => 0,
            default => $checkDigit,
        };
    }

    public function format(string $format = 'standard'): string
    {
        return match ($format) {
            'standard' => RifFormatter::standard($this),
            'spaced' => RifFormatter::spaced($this),
            'withDescription' => RifFormatter::withDescription($this),
            'compact' => RifFormatter::compact($this),
            'database' => RifFormatter::database($this),
            'dotted' => RifFormatter::dotted($this),
            'invoice' => RifFormatter::invoice($this),
            'legal' => RifFormatter::legal($this),
            default => RifFormatter::standard($this),
        };
    }

    public static function generate(RifType $type = null): self
    {
        return RifGenerator::generate($type);
    }

    public static function generateMultiple(int $count, RifType $type = null): array
    {
        return RifGenerator::generateMultiple($count, $type);
    }

    public static function generateSequential(int $sequence, RifType $type = null): self
    {
        return RifGenerator::generateSequential($sequence, $type);
    }

    public static function isValidFormat(string $rif): bool
    {
        return FormatValidator::validateStructure($rif);
    }

    public static function validateFormat(string $rif): array
    {
        return FormatValidator::getValidationDetails($rif);
    }

    public static function extractTypeFromString(string $rif): ?RifType
    {
        return FormatValidator::extractType($rif);
    }

    // Getters
    public function getRaw(): string
    {
        return $this->rawRif;
    }
    public function getType(): RifType
    {
        return $this->type;
    }
    public function getNumber(): string
    {
        return $this->number;
    }
    public function getCheckDigit(): int
    {
        return $this->checkDigit;
    }
    public function __toString(): string
    {
        return $this->rawRif;
    }
}
