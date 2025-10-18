<?php

declare(strict_types=1);

namespace TurecoLabs\Rif\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TurecoLabs\Rif\Validators\FormatValidator;
use TurecoLabs\Rif\Types\RifType;

class FormatValidatorTest extends TestCase
{
    /** @test */
    public function testValidatesCorrectStructure(): void
    {
        $this->assertTrue(FormatValidator::validateStructure('J123456789'));
        $this->assertTrue(FormatValidator::validateStructure('V113502963'));
        $this->assertTrue(FormatValidator::validateStructure('G200001100'));
    }

    /** @test */
    public function testRejectsIncorrectLength(): void
    {
        $this->assertFalse(FormatValidator::validateStructure('J12345678'));
        $this->assertFalse(FormatValidator::validateStructure('J1234567890'));
    }

    /** @test */
    public function testRejectsInvalidPrefix(): void
    {
        $this->assertFalse(FormatValidator::validateStructure('X123456789'));
    }

    /** @test */
    public function testRejectsNonNumericBody(): void
    {
        $this->assertFalse(FormatValidator::validateStructure('J1234A6789'));
    }

    /** @test */
    public function testRejectsNonNumericSuffix(): void
    {
        $this->assertFalse(FormatValidator::validateStructure('J12345678X'));
    }

    /** @test */
    public function testValidatesPartialCorrectly(): void
    {
        $this->assertTrue(FormatValidator::validatePartial('J123456789'));
        $this->assertTrue(FormatValidator::validatePartial('V113502963'));

        // Debe fallar por longitud
        $this->assertFalse(FormatValidator::validatePartial('J12345678'));
        // Debe fallar por prefijo
        $this->assertFalse(FormatValidator::validatePartial('X123456789'));
        // Debe fallar por cuerpo no numérico
        $this->assertFalse(FormatValidator::validatePartial('J1234A6789'));
    }

    /** @test */
    public function testValidatesPrefix(): void
    {
        $this->assertTrue(FormatValidator::isValidPrefix('V'));
        $this->assertTrue(FormatValidator::isValidPrefix('J'));
        $this->assertFalse(FormatValidator::isValidPrefix('X'));
    }

    /** @test */
    public function testValidatesBody(): void
    {
        $this->assertTrue(FormatValidator::isValidBody('12345678'));
        $this->assertFalse(FormatValidator::isValidBody('1234'));
        $this->assertFalse(FormatValidator::isValidBody('123456789'));
        $this->assertFalse(FormatValidator::isValidBody('1234A678'));
    }

    /** @test */
    public function testValidatesSuffix(): void
    {
        $this->assertTrue(FormatValidator::isValidSuffix('0'));
        $this->assertTrue(FormatValidator::isValidSuffix('9'));
        $this->assertFalse(FormatValidator::isValidSuffix('X'));
        $this->assertFalse(FormatValidator::isValidSuffix('10'));
    }

    /** @test */
    public function testExtractsTypeFromValidRif(): void
    {
        $this->assertEquals(RifType::LEGAL, FormatValidator::extractType('J123456789'));
        $this->assertEquals(RifType::NATURAL, FormatValidator::extractType('V113502963'));
        $this->assertEquals(RifType::GOVERNMENT, FormatValidator::extractType('G200001100'));
    }

    /** @test */
    public function testReturnsNullForInvalidRifWhenExtractingType(): void
    {
        $this->assertNull(FormatValidator::extractType('X123456789'));
        $this->assertNull(FormatValidator::extractType('J1234'));
    }

    /** @test */
    public function testProvidesDetailedValidationFeedback(): void
    {
        // Caso válido
        $details = FormatValidator::getValidationDetails('J123456789');
        $this->assertTrue($details['is_valid']);
        $this->assertEmpty($details['errors']);

        // Caso con múltiples errores
        $details = FormatValidator::getValidationDetails('X12A');
        $this->assertFalse($details['is_valid']);
        $this->assertCount(3, $details['errors']); // Longitud, prefijo y cuerpo
        $this->assertNotEmpty($details['suggestions']);

        // Verificar los tipos específicos de errores
        $errorMessages = implode('|', $details['errors']);
        $this->assertStringContainsString('longitud', strtolower($errorMessages));
        $this->assertStringContainsString('prefijo', strtolower($errorMessages));
        $this->assertStringContainsString('cuerpo', strtolower($errorMessages));
    }

    /** @test */
    public function testNormalizesRifDuringValidation(): void
    {
        $this->assertTrue(FormatValidator::validateStructure('j123456789'));
        $this->assertTrue(FormatValidator::validateStructure('  J123456789  '));
    }

    /** @test */
    public function testHandlesDifferentErrorScenarios(): void
    {
        // Solo error de longitud
        $details = FormatValidator::getValidationDetails('J12345678');
        $this->assertFalse($details['is_valid']);
        $this->assertCount(1, $details['errors']);
        $this->assertStringContainsString('longitud', strtolower($details['errors'][0]));

        // Solo error de prefijo
        $details = FormatValidator::getValidationDetails('X123456789');
        $this->assertFalse($details['is_valid']);
        $this->assertCount(1, $details['errors']);
        $this->assertStringContainsString('prefijo', strtolower($details['errors'][0]));

        // Solo error de cuerpo (caracteres no numéricos)
        $details = FormatValidator::getValidationDetails('J1234A6789');
        $this->assertFalse($details['is_valid']);
        $this->assertCount(1, $details['errors']);
        $this->assertStringContainsString('cuerpo', strtolower($details['errors'][0]));

        // Solo error de sufijo
        $details = FormatValidator::getValidationDetails('J12345678X');
        $this->assertFalse($details['is_valid']);
        $this->assertCount(1, $details['errors']);
        $this->assertStringContainsString('dígito verificador', strtolower($details['errors'][0]));
    }

    /** @test */
    public function testProvidesSpecificErrorMessages(): void
    {
        $details = FormatValidator::getValidationDetails('X12A');

        $this->assertFalse($details['is_valid']);

        // Verificar mensajes específicos
        $expectedErrors = [
            'La longitud debe ser de 10 caracteres, se recibieron 4',
            'Prefijo inválido: "X". Los prefijos válidos son: V, E, J, P, G, C',
            'El cuerpo del RIF debe contener solo números'
        ];

        foreach ($expectedErrors as $expectedError) {
            $this->assertContains($expectedError, $details['errors']);
        }
    }
}
