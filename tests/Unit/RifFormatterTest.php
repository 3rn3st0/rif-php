<?php

declare(strict_types=1);

namespace ErnestoCh\Rif\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ErnestoCh\Rif\Rif;
use ErnestoCh\Rif\Formatters\RifFormatter;

class RifFormatterTest extends TestCase
{
    private Rif $rif;

    protected function setUp(): void
    {
        // Usamos un RIF conocido para las pruebas
        $this->rif = Rif::create('J000029679');
    }

    /** @test */
    public function testFormatsStandard(): void
    {
        $this->assertSame('J-00002967-9', RifFormatter::standard($this->rif));
    }

    /** @test */
    public function testFormatsSpaced(): void
    {
        $this->assertSame('J 00 002 967 9', RifFormatter::spaced($this->rif));
    }

    /** @test */
    public function testFormatsWithDescription(): void
    {
        $this->assertSame(
            'J-00002967-9 (Persona Jurídica)',
            RifFormatter::withDescription($this->rif)
        );
    }

    /** @test */
    public function testFormatsCompact(): void
    {
        $this->assertSame('J000029679', RifFormatter::compact($this->rif));
    }

    /** @test */
    public function testFormatsDatabase(): void
    {
        $this->assertSame('J000029679', RifFormatter::database($this->rif));
    }

    /** @test */
    public function testFormatsDotted(): void
    {
        $this->assertSame('J-2.967-9', RifFormatter::dotted($this->rif));
    }

    /** @test */
    public function testFormatsInvoice(): void
    {
        $this->assertSame('J-00002967-9', RifFormatter::invoice($this->rif));
    }

    /** @test */
    public function testFormatsLegal(): void
    {
        $this->assertSame('R.I.F. J-00002967-9', RifFormatter::legal($this->rif));
    }

    /** @test */
    public function testFormatsCustom(): void
    {
        $this->assertSame('J/00002967/9', RifFormatter::custom($this->rif, '/'));
        $this->assertSame('J 00002967 9', RifFormatter::custom($this->rif, ' '));
    }

    /** @test */
    public function testHandlesDifferentRifTypes(): void
    {
        $naturalRif = Rif::create('V113502963');
        $governmentRif = Rif::create('G200001100');

        $this->assertSame('V-11350296-3', RifFormatter::standard($naturalRif));
        $this->assertSame('G-20000110-0', RifFormatter::standard($governmentRif));
    }
}
