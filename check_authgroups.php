<?php

require_once 'vendor/autoload.php';

try {
    $reflection = new ReflectionClass('CodeIgniter\Shield\Config\AuthGroups');
    $props = $reflection->getProperties();

    echo "Propriedades da classe AuthGroups:\n";
    foreach($props as $prop) {
        echo "- {$prop->getName()}: ";
        if($prop->hasType()) {
            echo $prop->getType()->getName();
        } else {
            echo "sem tipo definido";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}