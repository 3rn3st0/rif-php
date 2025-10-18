<?php

declare(strict_types=1);

namespace TurecoLabs\Rif\Tests\Unit\Types;

use PHPUnit\Framework\TestCase;
use TurecoLabs\Rif\Types\RifType;

class RifTypeTest extends TestCase
{
    /** @test */
    public function testReturnsCorrectNumericValues(): void
    {
        $this->assertSame(1, RifType::NATURAL->getNumericValue());
        $this->assertSame(2, RifType::FOREIGN_ID->getNumericValue());
        $this->assertSame(3, RifType::LEGAL->getNumericValue());
        $this->assertSame(4, RifType::FOREIGN_PASSPORT->getNumericValue());
        $this->assertSame(5, RifType::GOVERNMENT->getNumericValue());
        $this->assertSame(3, RifType::COMMUNAL->getNumericValue()); // Nota: mismo valor que LEGAL
    }

    /** @test */
    public function testReturnsCorrectDescriptions(): void
    {
        $this->assertSame('Persona Natural', RifType::NATURAL->getDescription());
        $this->assertSame('Extranjero con Cédula', RifType::FOREIGN_ID->getDescription());
        $this->assertSame('Persona Jurídica', RifType::LEGAL->getDescription());
        $this->assertSame('Extranjero con Pasaporte', RifType::FOREIGN_PASSPORT->getDescription());
        $this->assertSame('Gobierno', RifType::GOVERNMENT->getDescription());
        $this->assertSame('Consejo Comunal', RifType::COMMUNAL->getDescription());
    }

    /** @test */
    public function testCreatesFromValidPrefix(): void
    {
        $this->assertSame(RifType::NATURAL, RifType::fromPrefix('V'));
        $this->assertSame(RifType::LEGAL, RifType::fromPrefix('J'));
    }

    /** @test */
    public function testThrowsExceptionForInvalidPrefix(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        RifType::fromPrefix('X');
    }
}
