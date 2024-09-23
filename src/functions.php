<?php
// functions.php

declare(strict_types=1);

function getExchangeRate(string $from, string $to): float {
    $url = "https://api.frankfurter.app/latest?from=$from&to=$to";
    $json = @file_get_contents($url); // @ unterdrückt Warnungen

    if ($json === false) {
        throw new Exception("Fehler beim Abrufen des Wechselkurses von $from zu $to.");
    }

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
    $amountInBase = $amount / $rateFrom;

    if ($currencyFrom !== $currencyTo) {
        $convertedAmount = convertCurrency($amountInBase, $currencyFrom, $currencyTo);
    } else {
        $convertedAmount = $amountInBase;
    }

    $finalAmount = $convertedAmount * $rateTo;

    return number_format($finalAmount, 4);
}
