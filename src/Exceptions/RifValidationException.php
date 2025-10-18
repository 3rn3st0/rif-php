<?php

declare(strict_types=1);

namespace ErnestoCh\Rif\Exceptions;

class RifValidationException extends \DomainException
{
    public static function invalidLength(string $rif): self
    {
        $length = strlen($rif);
        return new self(
            "Longitud de RIF inválida: {$length} caracteres. Se esperaban 10 caracteres."
        );
    }

    public static function invalidFormat(string $rif): self
    {
        return new self(
            "Formato de RIF inválido: {$rif}. Formato esperado: L999999999"
        );
    }

    public static function invalidCheckDigit(int $received, int $expected): self
    {
        return new self(
            "Dígito verificador inválido. Se recibió: {$received}, se esperaba: {$expected}"
        );
    }
}
