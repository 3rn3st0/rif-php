<?php

declare(strict_types=1);

namespace ErnestoCh\Rif\Formatters;

use ErnestoCh\Rif\Rif;

final class RifFormatter
{
    /**
     * Formato estándar oficial: J-12345678-9
     */
    public static function standard(Rif $rif): string
    {
        return sprintf(
            '%s-%s-%s',
            $rif->getType()->value,
            $rif->getNumber(),
            $rif->getCheckDigit()
        );
    }

    /**
     * Formato con espacios para mejor legibilidad: J 12 345 678 9
     */
    public static function spaced(Rif $rif): string
    {
        $number = $rif->getNumber();
        $part1 = substr($number, 0, 2);
        $part2 = substr($number, 2, 3);
        $part3 = substr($number, 5, 3);

        return sprintf(
            '%s %s %s %s %s',
            $rif->getType()->value,
            $part1,
            $part2,
            $part3,
            $rif->getCheckDigit()
        );
    }

    /**
     * Formato con descripción completa: J-12345678-9 (Persona Jurídica)
     */
    public static function withDescription(Rif $rif): string
    {
        return sprintf(
            '%s (%s)',
            self::standard($rif),
            $rif->getType()->getDescription()
        );
    }

    /**
     * Formato compacto (sin guiones): J123456789
     */
    public static function compact(Rif $rif): string
    {
        return (string) $rif;
    }

    /**
     * Formato para bases de datos: elimina guiones y espacios
     */
    public static function database(Rif $rif): string
    {
        return $rif->getRaw();
    }

    /**
     * Formato para mostrar en interfaces de usuario
     * Con puntos para separar miles: J-12.345.678-9
     */
    public static function dotted(Rif $rif): string
    {
        $number = $rif->getNumber();
        $formattedNumber = number_format((int) $number, 0, '', '.');

        return sprintf(
            '%s-%s-%s',
            $rif->getType()->value,
            $formattedNumber,
            $rif->getCheckDigit()
        );
    }

    /**
     * Formato para facturas electrónicas (formato SENIAT)
     */
    public static function invoice(Rif $rif): string
    {
        return self::standard($rif);
    }

    /**
     * Formato para documentos legales
     */
    public static function legal(Rif $rif): string
    {
        return sprintf(
            'R.I.F. %s-%s-%s',
            $rif->getType()->value,
            $rif->getNumber(),
            $rif->getCheckDigit()
        );
    }

    /**
     * Formato personalizado con separadores personalizados
     */
    public static function custom(Rif $rif, string $separator = '-'): string
    {
        return sprintf(
            '%s%s%s%s%s',
            $rif->getType()->value,
            $separator,
            $rif->getNumber(),
            $separator,
            $rif->getCheckDigit()
        );
    }
}
