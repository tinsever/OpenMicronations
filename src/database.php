<?php
// database.php

$config = parse_ini_file('../config.ini', true);

// Datenbankverbindung
$host = $config['database']['host'];
$port = $config['database']['port'];
$dbname = $config['database']['dbname'];
$username = $config['database']['username'];
$password = $config['database']['password'];

function getExchangeRatesForCurrency(string $currency): array {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT short FROM currencies WHERE short != :currency");
    $stmt->bindParam(':currency', $currency);
    $stmt->execute();

    $rates = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rate_from = getExchangeRateFromDatabase($currency);
        $rate_to = getExchangeRateFromDatabase($row['short']);
        $real_from = getRealFromDatabase($currency);
        $real_to = getRealFromDatabase($row['short']);
        try {
            $result = convertSimCurrency(1, (float)$rate_from, (float)$rate_to, $real_from, $real_to);
            $rate = $result;
            $rates[$row['short']] = (float)$rate;
        } catch (Exception $e) {
            // Log error or handle it appropriately
            error_log("Fehler beim Konvertieren von $currency zu {$row['short']}: " . $e->getMessage());
            continue; // Continue to the next currency
        }
    }

    if (empty($rates)) {
        throw new Exception("Keine Wechselkurse für die Währung $currency gefunden.");
    }

    return $rates;
}


function getDatabaseConnection() {
    global $host, $port, $dbname, $username, $password;

    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception('Verbindung fehlgeschlagen: ' . $e->getMessage());
    }
}

function getCurrencies() {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT name, short, country, breakdown, exchange_rate, COALESCE(`real`, 'EUR') AS forex FROM currencies");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getExchangeRateFromDatabase($currency) {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT exchange_rate FROM currencies WHERE short = :currency");
    $stmt->bindParam(':currency', $currency);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        return $result['exchange_rate'];
    } else {
        throw new Exception("Wechselkurs für die Währung $currency nicht gefunden.");
    }
}

function getRealFromDatabase($currency) {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT `real` FROM currencies WHERE short = :currency");
    $stmt->bindParam(':currency', $currency);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['real'] ?? "EUR"; // Standardwert ist EUR
    } else {
        throw new Exception("Wechselkurs für die Währung $currency nicht gefunden.");
    }
}
