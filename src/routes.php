<?php
// routes.php

declare(strict_types=1);

require './functions.php';

function jsonResponse($message, $code = 0, $httpStatus = 200, $data = null): void {
    Flight::response()->setHeader('Content-Type', 'application/json');
    Flight::json([
        'message' => $message,
        'code' => $code,
        'data' => $data
    ], $httpStatus);
}

// Route für die Startseite
Flight::route('/', function (): void {
    jsonResponse('Hello!');
});

// Route für die Umrechnung zwischen realen Währungen
Flight::route('/v1/convert/real/@from/@to/@amount', function ($amount, string $from, string $to): void {
    try {
        $res = convertCurrency((float)$amount, $from, $to);
        jsonResponse('200: Währung umgerechnet', 0, 200, [
            'request' => [
                'from' => $from,
                'to' => $to,
                'amount' => $amount
            ],
            'response' => [
                'amount' => $res,
            ]
        ]);
    } catch (Exception $e) {
        jsonResponse('400: ' . $e->getMessage(), 1, 400);
    }
});

// Route für simulierte Währungsumrechnung
Flight::route('/v1/convert/v/@amount/@rate_from/@rate_to/@currency_from/@currency_to', function($amount, $rate_from, $rate_to, $currency_from, $currency_to): void {
    try {
        $result = convertSimCurrency((float)$amount, (float)$rate_from, (float)$rate_to, $currency_from, $currency_to);
        jsonResponse('200: Simulierte Währungsumrechnung erfolgreich', 0, 200, [
            'request' => [
                'amount' => $amount,
                'rate_from' => $rate_from,
                'rate_to' => $rate_to,
                'currency_from' => $currency_from,
                'currency_to' => $currency_to,
            ],
            'response' => [
                'converted_amount' => $result,
            ]
        ]);
    } catch (Exception $e) {
        jsonResponse('400: ' . $e->getMessage(), 1, 400);
    }
});

require './database.php'; // Datenbankverbindung einbinden

// Route für das Abrufen der Währungen
Flight::route('/v1/list', function(): void {
    $currencies = getCurrencies();
    jsonResponse('200: Währungen abgerufen', 0, 200, $currencies);
});

// Route für Währungsumrechnung mit Datenbankraten
Flight::route('/v1/convert/m/@amount/@currency_from/@currency_to', function($amount, $currency_from, $currency_to): void {
    try {
        $rate_from = getExchangeRateFromDatabase($currency_from);
        $rate_to = getExchangeRateFromDatabase($currency_to);
        $real_from = getRealFromDatabase($currency_from);
        $real_to = getRealFromDatabase($currency_to);

        if ($rate_from === null || $rate_to === null) {
            throw new Exception("Wechselkurs nicht verfügbar.");
        }

        $result = convertSimCurrency((float)$amount, (float)$rate_from, (float)$rate_to, $real_from, $real_to);
        jsonResponse('200: Währungsumrechnung erfolgreich', 0, 200, [
            'request' => [
                'amount' => $amount,
                'currency_from' => $currency_from,
                'currency_to' => $currency_to,
                'rate_from' => $rate_from,
                'rate_to' => $rate_to,
            ],
            'response' => [
                'converted_amount' => $result,
            ]
        ]);
    } catch (Exception $e) {
        jsonResponse('400: ' . $e->getMessage(), 1, 400);
    }
});

Flight::route('/v1/rates/@currency', function ($currency): void {
    try {
        $exchangeRates = getExchangeRatesForCurrency($currency);
        Flight::json([
            'message' => '200: Wechselkurse erfolgreich abgerufen',
            'code' => 0,
            'currency' => $currency,
            'rates' => $exchangeRates,
        ], 200);
    } catch (Exception $e) {
        Flight::json([
            'message' => '400: ' . $e->getMessage(),
            'code' => 1
        ], 400);
    }
});

Flight::route(
    '/docs', function() {
        Flight::render('docs.php');
    }
);

// Fallback für nicht gefundene Routen
Flight::map('notFound', function (): void {
    jsonResponse('404: Nicht gefunden', 0, 404);
});
