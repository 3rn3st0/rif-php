<?php

require_once 'vendor/autoload.php';

use ErnestoCh\Rif\Rif;

// Validación simple
if (Rif::isValid('J123456789')) {
    echo "RIF válido!";
}

// Validación con manejo de excepciones
try {
    $rif = Rif::create('J123456789');
    echo "RIF: " . $rif->getRaw();
    echo "Tipo: " . $rif->getType()->getDescription();
} catch (ErnestoCh\Rif\Exceptions\RifValidationException $e) {
    echo "Error: " . $e->getMessage();
}
