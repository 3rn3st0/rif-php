<?php

declare(strict_types=1);

namespace TurecoLabs\Rif\Services;

class RifValidator
{
    /**
     * Valida contra servicio externo si está disponible
     */
    public static function validateWithExternalService(string $rif): bool
    {
        // Aquí puedes integrar con API del SENIAT u otro servicio
        return true; // Placeholder
    }

    /**
     * Valida el formato sin verificar el dígito
     */
    public static function validateFormat(string $rif): bool
    {
        return preg_match('/^[VEJPGC]\d{8}\d$/', strtoupper(trim($rif))) === 1;
    }
}
