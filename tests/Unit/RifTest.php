<?php

declare(strict_types=1);

namespace TurecoLabs\Rif\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TurecoLabs\Rif\Rif;
use TurecoLabs\Rif\Exceptions\RifValidationException;

class RifTest extends TestCase
{
    /**
     * RIFs REALES verificados
     */
    public function testCreatesValidRif(): void
    {
        $validRifs = [
            'V113502963' => ['number' => '11350296', 'checkDigit' => 3],
            'G200001100' => ['number' => '20000110', 'checkDigit' => 0],
            'J000029679' => ['number' => '00002967', 'checkDigit' => 9],
        ];

        foreach ($validRifs as $rifString => $expected) {
            $rif = Rif::create($rifString);

            $this->assertInstanceOf(Rif::class, $rif);
            $this->assertSame($rifString, $rif->getRaw());
            $this->assertSame($expected['number'], $rif->getNumber());
            $this->assertSame($expected['checkDigit'], $rif->getCheckDigit());
        }
    }

    /**
     * Test de validación con RIFs reales
     */
    public function testValidatesCorrectRifs(): void
    {
        $this->assertTrue(Rif::isValid('V113502963'));
        $this->assertTrue(Rif::isValid('G200001100'));
        $this->assertTrue(Rif::isValid('J000029679'));
    }

    /**
     * Test de cálculo manual para verificar el algoritmo
     */
    public function testCheckDigitCalculation(): void
    {
        // Verificación manual del algoritmo para V113502963
        $this->assertTrue(Rif::isValid('V113502963'));

        // Verificación de que RIFs inválidos son rechazados
        $this->assertFalse(Rif::isValid('V113502960')); // Dígito incorrecto
        $this->assertFalse(Rif::isValid('V113502961')); // Dígito incorrecto
        $this->assertFalse(Rif::isValid('V113502962')); // Dígito incorrecto
    }

    public function testRejectsInvalidLength(): void
    {
        $this->expectException(RifValidationException::class);
        $this->expectExceptionMessage('Longitud de RIF inválida');

        Rif::create('J12345678');
    }

    public function testRejectsInvalidPrefix(): void
    {
        $this->expectException(RifValidationException::class);
        $this->expectExceptionMessage('Formato de RIF inválido');

        Rif::create('X123456789');
    }

    public function testRejectsNonNumericBody(): void
    {
        $this->expectException(RifValidationException::class);
        $this->expectExceptionMessage('Formato de RIF inválido');

        Rif::create('J123ABC789');
    }

    public function testRejectsInvalidCheckDigit(): void
    {
        $this->expectException(RifValidationException::class);
        $this->expectExceptionMessage('Dígito verificador inválido');

        // V113502963 es válido, V113502960 no
        Rif::create('V113502960');
    }

    /** @test */
    public function testFormatsRifUsingConvenienceMethod(): void
    {
        $rif = Rif::create('J000029679');

        $this->assertSame('J-00002967-9', $rif->format('standard'));
        $this->assertSame('J 00 002 967 9', $rif->format('spaced'));
        $this->assertSame('J-00002967-9 (Persona Jurídica)', $rif->format('withDescription'));
        $this->assertSame('J000029679', $rif->format('compact'));
    }

    /** @test */
    public function testUsesStandardFormatByDefault(): void
    {
        $rif = Rif::create('J000029679');
        $this->assertSame('J-00002967-9', $rif->format());
    }
}
