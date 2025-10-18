<?php

declare(strict_types=1);

namespace TurecoLabs\Rif\Validators;

use TurecoLabs\Rif\Types\RifType;

final class FormatValidator
{
    private const RIF_LENGTH = 10;
    private const VALID_PREFIXES = ['V', 'E', 'J', 'P', 'G', 'C'];

    /**
     * Valida la estructura completa del RIF (longitud, prefijo, cuerpo numérico y dígito verificador numérico)
     * pero no verifica el dígito verificador.
     */
    public static function validateStructure(string $rif): bool
    {
        $rif = self::normalize($rif);

        if (strlen($rif) !== self::RIF_LENGTH) {
            return false;
        }

        $prefix = $rif[0];
        $body = substr($rif, 1, 8);
        $suffix = substr($rif, -1);

        return self::isValidPrefix($prefix) &&
               self::isValidBody($body) &&
               self::isValidSuffix($suffix);
    }

    /**
     * Valida solo el prefijo y la longitud (útil para validaciones parciales)
     */
    public static function validatePartial(string $rif): bool
    {
        $rif = self::normalize($rif);

        if (strlen($rif) !== self::RIF_LENGTH) {
            return false;
        }

        $prefix = $rif[0];
        $body = substr($rif, 1, 8);

        return self::isValidPrefix($prefix) && self::isValidBody($body);
    }

    /**
     * Valida el prefijo del RIF
     */
    public static function isValidPrefix(string $prefix): bool
    {
        return in_array($prefix, self::VALID_PREFIXES, true);
    }

    /**
     * Valida el cuerpo del RIF (8 dígitos)
     */
    public static function isValidBody(string $body): bool
    {
        return ctype_digit($body) && strlen($body) === 8;
    }

    /**
     * Valida el sufijo (dígito verificador) como número
     */
    public static function isValidSuffix(string $suffix): bool
    {
        return ctype_digit($suffix) && strlen($suffix) === 1;
    }

    /**
     * Extrae el tipo de RIF si el formato es válido, de lo contrario null
     */
    public static function extractType(string $rif): ?RifType
    {
        $rif = self::normalize($rif);

        if (!self::validateStructure($rif)) {
            return null;
        }

        $prefix = $rif[0];

        return RifType::tryFrom($prefix);
    }

    /**
     * Obtiene detalles de validación para dar feedback específico al usuario
     */
    public static function getValidationDetails(string $rif): array
    {
        $rif = self::normalize($rif);
        $details = [
            'is_valid' => true,
            'errors' => [],
            'suggestions' => [],
        ];

        // Validar longitud
        if (strlen($rif) !== self::RIF_LENGTH) {
            $details['is_valid'] = false;
            $details['errors'][] = sprintf(
                'La longitud debe ser de %d caracteres, se recibieron %d',
                self::RIF_LENGTH,
                strlen($rif)
            );
        }

        // Validar prefijo
        if (strlen($rif) > 0 && !self::isValidPrefix($rif[0])) {
            $details['is_valid'] = false;
            $details['errors'][] = sprintf(
                'Prefijo inválido: "%s". Los prefijos válidos son: %s',
                $rif[0],
                implode(', ', self::VALID_PREFIXES)
            );
            $details['suggestions'][] = 'Use V, E, J, P, G o C como primer carácter';
        }

        // Validar cuerpo
        if (strlen($rif) > 1) {
            $body = substr($rif, 1, 8);
            if (!self::isValidBody($body)) {
                $details['is_valid'] = false;
                if (!ctype_digit($body)) {
                    $details['errors'][] = 'El cuerpo del RIF debe contener solo números';
                } else {
                    $details['errors'][] = 'El cuerpo del RIF debe tener exactamente 8 dígitos';
                }
            }
        }

        // Validar sufijo
        if (strlen($rif) === self::RIF_LENGTH && !self::isValidSuffix($rif[-1])) {
            $details['is_valid'] = false;
            $details['errors'][] = 'El dígito verificador debe ser un número';
        }

        return $details;
    }

    /**
     * Normaliza el RIF (mayúsculas, elimina espacios)
     */
    private static function normalize(string $rif): string
    {
        return strtoupper(trim($rif));
    }
}
