<?php

require_once 'vendor/autoload.php';

use ErnestoChapon\Rif\Rif;
use ErnestoChapon\Rif\Exceptions\RifValidationException;

// Validación simple
if (Rif::isValid('J123456789')) {
    echo "RIF válido!";
}

// Validación con manejo de excepciones
try {
    $rif = Rif::create('J123456789');
    echo "RIF: " . $rif->getRaw();
    echo "Tipo: " . $rif->getType()->getDescription();
} catch (RifValidationException $e) {
    echo "Error: " . $e->getMessage();
}
