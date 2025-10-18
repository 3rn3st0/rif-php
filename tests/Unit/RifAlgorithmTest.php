<?php

declare(strict_types=1);

namespace ErnestoCh\Rif\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ErnestoCh\Rif\Rif;

class RifAlgorithmTest extends TestCase
{
    /**
     * RIFs reales verificados
     */
    public static function validRifProvider(): array
    {
        return [
            ['V113502963', 3],
            ['G200001100', 0],
            ['J000029679', 9],
        ];
    }

    /**
     * @dataProvider validRifProvider
     */
    public function testValidRifs(string $rifString, int $expectedCheckDigit): void
    {
        $rif = Rif::create($rifString);
        $this->assertSame($expectedCheckDigit, $rif->getCheckDigit());
    }

    /**
     * Test para asegurar que el algoritmo rechaza RIFs inválidos
     */
    public function testInvalidRifs(): void
    {
        $invalidRifs = [
            'V113502960', // Dígito incorrecto
            'G200001101', // Dígito incorrecto
            'J000029670', // Dígito incorrecto
        ];

        foreach ($invalidRifs as $invalidRif) {
            $this->assertFalse(Rif::isValid($invalidRif));
        }
    }

    /**
     * Test de verificación manual del algoritmo
     */
    public function testManualAlgorithmVerification(): void
    {
        // Verificación paso a paso del algoritmo con RIFs conocidos válidos
        $knownValidRifs = [
            'V113502963',
            'G200001100',
            'J000029679',
        ];

        foreach ($knownValidRifs as $rif) {
            $this->assertTrue(
                Rif::isValid($rif),
                "El RIF {$rif} debería ser válido"
            );
        }

        // También probamos algunos RIFs inválidos
        $knownInvalidRifs = [
            'V113502960', // Dígito incorrecto
            'V123456789', // Este no es válido según nuestro algoritmo
        ];

        foreach ($knownInvalidRifs as $rif) {
            $this->assertFalse(
                Rif::isValid($rif),
                "El RIF {$rif} debería ser inválido"
            );
        }
    }
}
