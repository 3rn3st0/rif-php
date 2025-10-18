<?php

declare(strict_types=1);

namespace TurecoLabs\Rif\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TurecoLabs\Rif\Rif;
use TurecoLabs\Rif\Services\RifGenerator;
use TurecoLabs\Rif\Types\RifType;

class RifGeneratorTest extends TestCase
{
    /** @test */
    public function testGeneratesValidRif(): void
    {
        $rif = RifGenerator::generate();

        $this->assertInstanceOf(Rif::class, $rif);
        $this->assertTrue(Rif::isValid($rif->getRaw()));
    }

    /** @test */
    public function testGeneratesSpecificType(): void
    {
        $rif = RifGenerator::generate(RifType::NATURAL);

        $this->assertSame(RifType::NATURAL, $rif->getType());
        $this->assertTrue(Rif::isValid($rif->getRaw()));
    }

    /** @test */
    public function testGeneratesFromSpecificNumber(): void
    {
        $rif = RifGenerator::fromNumber('12345678', RifType::LEGAL);

        $this->assertSame('12345678', $rif->getNumber());
        $this->assertSame(RifType::LEGAL, $rif->getType());
        $this->assertTrue(Rif::isValid($rif->getRaw()));
    }

    /** @test */
    public function testGeneratesMultipleRifs(): void
    {
        $rifs = RifGenerator::generateMultiple(5);

        $this->assertCount(5, $rifs);

        foreach ($rifs as $rif) {
            $this->assertInstanceOf(Rif::class, $rif);
            $this->assertTrue(Rif::isValid($rif->getRaw()));
        }
    }

    /** @test */
    public function testGeneratesOneOfEachType(): void
    {
        $rifs = RifGenerator::generateOneOfEachType();

        $this->assertCount(count(RifType::cases()), $rifs);

        foreach ($rifs as $typeChar => $rif) {
            $this->assertInstanceOf(Rif::class, $rif);
            $this->assertSame($typeChar, $rif->getType()->value);
            $this->assertTrue(Rif::isValid($rif->getRaw()));
        }
    }

    /** @test */
    public function testGeneratesSequentialRifs(): void
    {
        $rif = RifGenerator::generateSequential(123, RifType::GOVERNMENT);

        $this->assertSame('00000123', $rif->getNumber());
        $this->assertSame(RifType::GOVERNMENT, $rif->getType());
        $this->assertTrue(Rif::isValid($rif->getRaw()));
    }

    /** @test */
    public function testThrowsExceptionForInvalidNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        RifGenerator::fromNumber('123', RifType::LEGAL);
    }

    /** @test */
    public function testThrowsExceptionForInvalidSequence(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        RifGenerator::generateSequential(100000000, RifType::LEGAL);
    }

    /** @test */
    public function testThrowsExceptionForInvalidCount(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        RifGenerator::generateMultiple(0);

        $this->expectException(\InvalidArgumentException::class);
        RifGenerator::generateMultiple(1001);
    }

    /** @test */
    public function generatedRifsHaveCorrectStructure(): void
    {
        $rif = RifGenerator::generate();
        $rawRif = $rif->getRaw();

        $this->assertMatchesRegularExpression('/^[VEJPGC]\d{8}\d$/', $rawRif);
        $this->assertSame(10, strlen($rawRif));
    }

    /** @test */
    public function testCanGenerateLargeQuantities(): void
    {
        $rifs = RifGenerator::generateMultiple(100);

        $this->assertCount(100, $rifs);

        // Verificar que todos son únicos (muy probablemente)
        $uniqueRifs = array_unique(array_map(fn($rif) => $rif->getRaw(), $rifs));
        $this->assertGreaterThan(95, count($uniqueRifs)); // Permitir algunos duplicados por azar
    }
}
