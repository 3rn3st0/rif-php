# RIF PHP

[![PHP Version](https://img.shields.io/badge/php-8.3%2B-blue.svg)](https://packagist.org/packages/ernestoch/rif-php)
[![Tests](https://github.com/3rn3st0/rif-php/actions/workflows/tests.yml/badge.svg)](https://github.com/3rn3st0/rif-php/actions/workflows/tests.yml)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Una librería PHP profesional para validar, formatear y generar números RIF (Registro de Información Fiscal) de Venezuela.

## ✨ Características

- ✅ **Validación completa** de números RIF según algoritmo oficial
- ✅ **Cálculo del dígito verificador** con algoritmo verificados
- ✅ **Soporte para todos los tipos** de RIF (V, E, J, P, G, C)
- ✅ **Formateo profesional** para presentación
- ✅ **Generación de RIFs válidos** para testing
- ✅ **100% type-hinted** y compatible con PHP 8.3+
- ✅ **Cobertura completa de tests**
- ✅ **PSR-4 y estándares modernos** de PHP

## 📦 Instalación

```bash
composer require ernestoch/rif-php
```

### Probar instalación

```bash
php -r "require 'vendor/autoload.php'; echo ErnestoCh\Rif\Rif::isValid('V113502963') ? '✅ Instalación exitosa!' : '❌ Error';"
```

## 🚀 Uso Rápido

```php
<?php

require_once 'vendor/autoload.php';

use TurecoLabs\Rif\Rif;

// Validación simple
if (Rif::isValid('V113502963')) {
    echo "RIF válido!";
}

// Validación con manejo de excepciones
try {
    $rif = Rif::create('J000029679');
    echo "RIF: " . $rif->getRaw();
    echo "Tipo: " . $rif->getType()->getDescription();
    echo "Número: " . $rif->getNumber();
    echo "Dígito verificador: " . $rif->getCheckDigit();
} catch (TurecoLabs\Rif\Exceptions\RifValidationException $e) {
    echo "Error: " . $e->getMessage();
}

// Validación de RIFs conocidos
$knownRifs = [
    'V113502963', // RIF personal
    'G200001100', // Banco Central de Venezuela
    'J000029679', // Banco Provincial
];

foreach ($knownRifs as $rifString) {
    if (Rif::isValid($rifString)) {
        echo "$rifString es válido\n";
    }
}
```

## 🎨 Formateadores

La librería incluye múltiples formateadores para diferentes contextos:

```php
<?php

use TurecoLabs\Rif\Rif;
use TurecoLabs\Rif\Formatters\RifFormatter;

$rif = Rif::create('J000029679');

// Diferentes formatos disponibles
echo RifFormatter::standard($rif);        // J-00002967-9
echo RifFormatter::spaced($rif);          // J 00 002 967 9
echo RifFormatter::withDescription($rif); // J-00002967-9 (Persona Jurídica)
echo RifFormatter::dotted($rif);          // J-2.967-9
echo RifFormatter::legal($rif);           // R.I.F. J-00002967-9

// Método de conveniencia
echo $rif->format('standard');            // J-00002967-9
echo $rif->format('spaced');              // J 00 002 967 9
```

### Casos de uso comunes:

    Interfaces de usuario: spaced o dotted para mejor legibilidad

    Bases de datos: compact o database para almacenamiento

    Facturas electrónicas: invoice (formato SENIAT)

    Documentos legales: legal para contratos y documentos formales

    Mostrar información completa: withDescription para interfaces administrativas

## 🎲 Generador de RIFs Válidos

Genera RIFs válidos para testing y desarrollo:

```php
<?php

use TurecoLabs\Rif\Rif;
use TurecoLabs\Rif\Types\RifType;

// Generar un RIF aleatorio
$rif = Rif::generate();
echo $rif->getRaw(); // Ej: V123456789

// Generar un tipo específico
$rif = Rif::generate(RifType::LEGAL);
echo $rif->getRaw(); // Ej: J987654321

// Generar múltiples RIFs
$rifs = Rif::generateMultiple(5);
foreach ($rifs as $rif) {
    echo $rif->format() . "\n";
}

// Generar RIF secuencial (útil para testing)
$rif = Rif::generateSequential(42, RifType::NATURAL);
echo $rif->getRaw(); // V00000042X

// Usar el generador directamente
use TurecoLabs\Rif\Services\RifGenerator;

$rif = RifGenerator::generateOneOfEachType();
foreach ($rif as $type => $rifInstance) {
    echo "{$type}: {$rifInstance->format()}\n";
}
```

### Casos de uso del generador:

    Testing: Generar datos de prueba para tus tests unitarios

    Desarrollo: Rellenar bases de datos de desarrollo

    Demostraciones: Crear ejemplos para documentación o presentaciones

    Prototipos: Probar interfaces sin necesidad de RIFs reales

## 🔍 Validador de Formato

Valida la estructura de un RIF sin verificar el dígito verificador:

```php
<?php

use TurecoLabs\Rif\Rif;
use TurecoLabs\Rif\Validators\FormatValidator;

// Validar estructura completa (pero sin dígito verificador)
if (Rif::isValidFormat('J123456789')) {
    echo "Formato válido";
}

// Validar usando el validador directamente
if (FormatValidator::validateStructure('V113502963')) {
    echo "Estructura válida";
}

// Validación parcial (prefijo y cuerpo)
if (FormatValidator::validatePartial('J123456789')) {
    echo "Prefijo y cuerpo válidos";
}

// Validar componentes individuales
if (FormatValidator::isValidPrefix('V')) {
    echo "Prefijo válido";
}

if (FormatValidator::isValidBody('12345678')) {
    echo "Cuerpo válido";
}

// Extraer el tipo de RIF
$type = FormatValidator::extractType('J123456789');
if ($type) {
    echo "Tipo: " . $type->getDescription();
}

// Obtener detalles de validación para feedback al usuario
$details = Rif::validateFormat('X12A');
if (!$details['is_valid']) {
    foreach ($details['errors'] as $error) {
        echo "Error: $error\n";
    }
    foreach ($details['suggestions'] as $suggestion) {
        echo "Sugerencia: $suggestion\n";
    }
}
```

### Casos de uso del validador de formato:

    Validación en tiempo real: En formularios, validar mientras el usuario escribe

    Feedback inmediato: Indicar errores de formato sin esperar a la validación completa

    Limpieza de datos: Verificar datos antes de procesarlos

    Clasificación: Identificar el tipo de RIF antes de validar completamente

## 🧪 Ejecución de Tests

```bash
# Ejecutar tests
composer test

# Análisis estático de código
composer analyse

# Verificación de estándares de código
composer lint
```

## 📚 Documentación

Consulta la [documentación completa](README.md) para más ejemplos y API reference.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

    Fork el proyecto

    Crea una rama para tu feature (git checkout -b feature/AmazingFeature)

    Commit tus cambios (git commit -m 'Add some AmazingFeature')

    Push a la rama (git push origin feature/AmazingFeature)

    Abre un Pull Request

## 📄 Licencia

Este proyecto está licenciado bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para detalles.

## 🏢 Uso en Producción
Esta librería está siendo utilizada en producción y ha sido verificada con RIFs reales del sistema venezolano.
