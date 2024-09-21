<?php

/*
PHP-Skript zur Umrechnung von Währungen für Mikronationen, die an reale Devisenmärkte gekoppelt werden können.
Es nutzt die "Frankfurter-API", um aktuelle Wechselkurse abzufragen.
Verwende die Funktion `convertSimCurrency`, um eine Währung in eine andere umzurechnen.

Beispiel:
Währung A ist im Verhältnis 1:1 an den USD gekoppelt, 
Währung B hat einen Wechselkurs von 0,67 zum EURO (1€ = 0,67 B).
Nun möchte man 5 Einheiten von Währung A in Währung B umwandeln.

convertSimCurrency(5, 1, 0.67, 'USD', 'EUR');
*/

function getExchangeRate(string $from, string $to): float {
    $url = "https://api.frankfurter.app/latest?from=$from&to=$to";
    $json = file_get_contents($url);
    $data = json_decode($json, true);

    if (!isset($data['rates'][$to])) {
        throw new Exception("Wechselkurs von $from zu $to konnte nicht abgerufen werden.");
    }

    return (float)$data['rates'][$to];
}

function convertCurrency(float $amount, string $from, string $to): float { 
    $rate = getExchangeRate($from, $to);
    return $amount * $rate;
}

function convertSimCurrency(float $amount, float $rateFrom, float $rateTo, string $currencyFrom, string $currencyTo): string {
    // Berechnet den Betrag der Ausgangswährung in Bezug auf den Standardwert (z.B. 1 USD)
    $amountInBase = $amount / $rateFrom;

    // Falls die Währungen identisch sind, erfolgt keine Konvertierung
    if ($currencyFrom === $currencyTo) {
        $convertedAmount = $amountInBase;
    } else {
        // Umrechnung zwischen den beiden echten Währungen
        $convertedAmount = convertCurrency($amountInBase, $currencyFrom, $currencyTo);
    }

    // Umrechnung in die Zielwährung des simulierten Systems
    $finalAmount = $convertedAmount * $rateTo;

    // Ausgabe des Endbetrags mit 4 Dezimalstellen
    return number_format($finalAmount, 4);
}
