<?php

function getExampleRequest($routeId) {
    switch ($routeId) {
        case 'home':
            return 'GET /';
        case 'convert-real':
            return 'GET /v1/convert/real/USD/EUR/100';
        case 'convert-sim':
            return 'GET /v1/convert/v/100/1/0.85/USD/EUR';
        case 'list-currencies':
            return 'GET /v1/list';
        case 'convert-db':
            return 'GET /v1/convert/m/100/VYR/ELD';
        case 'get-rates':
            return 'GET /v1/rates/VYR';
        default:
            return 'No example request available.';
    }
}

function getExampleResponse($routeId) {
    switch ($routeId) {
        case 'home':
            return [
                'message' => 'Hallo!',
                'code' => 0,
                'data' => null
            ];
        case 'convert-real':
            return [
                'message' => '200: Währung umgerechnet',
                'code' => 0,
                'data' => [
                    'request' => [
                        'from' => 'USD',
                        'to' => 'EUR',
                        'amount' => '100'
                    ],
                    'response' => [
                        'amount' => 85
                    ]
                ]
            ];
        case 'convert-sim':
            return [
                'message' => '200: Simulierte Währungsumrechnung erfolgreich',
                'code' => 0,
                'data' => [
                    'request' => [
                        'amount' => '100',
                        'rate_from' => '1',
                        'rate_to' => '0.85',
                        'currency_from' => 'USD',
                        'currency_to' => 'EUR'
                    ],
                    'response' => [
                        'converted_amount' => 85
                    ]
                ]
            ];
        case 'list-currencies':
            return [
                'message' => '200: Währungen abgerufen',
                'code' => 0,
                'data' => [
                    [
                        'name' => 'Vyrth',
                        'short' => 'VYR',
                        'country' => 'Gurkistan',
                        'breakdown' => '1 Vyrth = 25 Korvyrth',
                        'exchange_rate' => '0.6700',
                        'forex' => 'EUR',
                    ],
                    [
                        'name' => 'Eldländische Krone',
                        'short' => 'ELD',
                        'country' => 'Eldeyja',
                        'breakdown' => null,
                        'exchange_rate' => '0.0133',
                        'forex' => 'ISK',
                    ],
                ]
            ];
        case 'convert-db':
            return [
                'message' => '200: Währungsumrechnung erfolgreich',
                'code' => 0,
                'data' => [
                    'request' => [
                        'amount' => '100',
                        'currency_from' => 'VYR',
                        'currency_to' => 'ELD',
                        'rate_from' => '0.6700',
                        'rate_to' => '0.0133'
                    ],
                    'response' => [
                        'converted_amount' => '302.3269'
                    ]
                ]
            ];
        case 'get-rates':
            return [
                'message' => '200: Wechselkurse erfolgreich abgerufen',
                'code' => 0,
                'currency' => 'VYR',
                'rates' => [
                    'EUR' => 1.4925,
                    'ELD' => 3.0233
                ]
            ];
        default:
            return [];
    }
}

function getRouteDescription($routeId) {
    switch ($routeId) {
        case 'home':
            return 'Gibt eine einfache Begrüßungsnachricht zurück.';
        case 'convert-real':
            return 'Konvertiert einen Betrag von einer realen Währung in eine andere.';
        case 'convert-sim':
            return 'Führt eine simulierte Währungsumrechnung basierend auf den angegebenen Raten durch.';
        case 'list-currencies':
            return 'Ruft eine Liste der verfügbaren Sim-on-Währungen ab.';
        case 'convert-db':
            return 'Konvertiert einen Betrag mit Sim-on-Währungen und Wechselkursen, die in der Datenbank gespeichert sind.';
        case 'get-rates':
            return 'Ruft die Wechselkurse für eine bestimmte Sim-on-Währung ab.';
        default:
            return 'Keine Beschreibung verfügbar.';
    }
}

// Determine the route to display based on query parameters
$routeId = isset($_GET['route']) ? $_GET['route'] : 'list-currencies';

$request = getExampleRequest($routeId);
$response = json_encode(getExampleResponse($routeId), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$description = getRouteDescription($routeId);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Dokumentation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <style>
        .hidden { display: none; }
    </style>
</head>
<body class="min-h-screen bg-gray-900 text-gray-100">
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-gray-800 p-4 overflow-y-auto md:min-h-screen">
            <nav>
                <ul class="space-y-2">
                    <li><a href="?route=home" class="block p-2 rounded hover:bg-gray-700">Startseite</a></li>
                    <li><a href="?route=convert-real" class="block p-2 rounded hover:bg-gray-700">Reale Währung umrechnen</a></li>
                    <li><a href="?route=convert-sim" class="block p-2 rounded hover:bg-gray-700">Simulierte Währungsumrechnung</a></li>
                    <li><a href="?route=list-currencies" class="block p-2 rounded hover:bg-gray-700">Währungen auflisten</a></li>
                    <li><a href="?route=convert-db" class="block p-2 rounded hover:bg-gray-700">Mit Datenbankraten umrechnen</a></li>
                    <li><a href="?route=get-rates" class="block p-2 rounded hover:bg-gray-700">Wechselkurse abrufen</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main content -->
        <main id="content" class="flex-1 p-4">
            <h1 class="text-3xl font-bold">FOREX-Börse API</h1>
            <p class="mb-6 text-xl font-bold">URL: https://api.mn-netz.de</p>

            <section id="home" class="mb-8 <?php echo $routeId === 'home' ? '' : 'hidden'; ?>">
                <h2 class="text-2xl font-semibold mb-4">Startseite</h2>
                <p>Willkommen zur API Dokumentation!</p>
            </section>

            <section id="convert-real" class="mb-8 <?php echo $routeId === 'convert-real' ? '' : 'hidden'; ?>">
                <h2 class="text-2xl font-semibold mb-4">Reale Währung umrechnen</h2>
                <div class="bg-gray-800 p-4 rounded">
                    <h3 class="text-lg font-medium mb-2">Endpunkt</h3>
                    <code class="bg-gray-700 p-2 rounded"><?php echo $request; ?></code>

                    <h3 class="text-lg font-medium mt-4 mb-2">Antwort</h3>
                    <pre class="bg-gray-700 p-2 rounded overflow-x-auto">
<code><?php echo $response; ?></code>
                    </pre>

                    <h3 class="text-lg font-medium mt-4 mb-2">Beschreibung</h3>
                    <p><?php echo $description; ?></p>
                </div>
            </section>

            <section id="convert-sim" class="mb-8 <?php echo $routeId === 'convert-sim' ? '' : 'hidden'; ?>">
                <h2 class="text-2xl font-semibold mb-4">Simulierte Währungsumrechnung</h2>
                <div class="bg-gray-800 p-4 rounded">
                    <h3 class="text-lg font-medium mb-2">Endpunkt</h3>
                    <code class="bg-gray-700 p-2 rounded"><?php echo $request; ?></code>

                    <h3 class="text-lg font-medium mt-4 mb-2">Antwort</h3>
                    <pre class="bg-gray-700 p-2 rounded overflow-x-auto">
<code><?php echo $response; ?></code>
                    </pre>

                    <h3 class="text-lg font-medium mt-4 mb-2">Beschreibung</h3>
                    <p><?php echo $description; ?></p>
                </div>
            </section>

            <section id="list-currencies" class="mb-8 <?php echo $routeId === 'list-currencies' ? '' : 'hidden'; ?>">
                <h2 class="text-2xl font-semibold mb-4">Währungen auflisten</h2>
                <div class="bg-gray-800 p-4 rounded">
                    <h3 class="text-lg font-medium mb-2">Endpunkt</h3>
                    <code class="bg-gray-700 p-2 rounded"><?php echo $request; ?></code>

                    <h3 class="text-lg font-medium mt-4 mb-2">Antwort</h3>
                    <pre class="bg-gray-700 p-2 rounded overflow-x-auto">
<code><?php echo $response; ?></code>
                    </pre>

                    <h3 class="text-lg font-medium mt-4 mb-2">Beschreibung</h3>
                    <p><?php echo $description; ?></p>
                </div>
            </section>

            <section id="convert-db" class="mb-8 <?php echo $routeId === 'convert-db' ? '' : 'hidden'; ?>">
                <h2 class="text-2xl font-semibold mb-4">Mit Datenbankraten umrechnen</h2>
                <div class="bg-gray-800 p-4 rounded">
                    <h3 class="text-lg font-medium mb-2">Endpunkt</h3>
                    <code class="bg-gray-700 p-2 rounded"><?php echo $request; ?></code>

                    <h3 class="text-lg font-medium mt-4 mb-2">Antwort</h3>
                    <pre class="bg-gray-700 p-2 rounded overflow-x-auto">
<code><?php echo $response; ?></code>
                    </pre>

                    <h3 class="text-lg font-medium mt-4 mb-2">Beschreibung</h3>
                    <p><?php echo $description; ?></p>
                </div>
            </section>

            <section id="get-rates" class="mb-8 <?php echo $routeId === 'get-rates' ? '' : 'hidden'; ?>">
                <h2 class="text-2xl font-semibold mb-4">Wechselkurse abrufen</h2>
                <div class="bg-gray-800 p-4 rounded">
                    <h3 class="text-lg font-medium mb-2">Endpunkt</h3>
                    <code class="bg-gray-700 p-2 rounded"><?php echo $request; ?></code>

                    <h3 class="text-lg font-medium mt-4 mb-2">Antwort</h3>
                    <pre class="bg-gray-700 p-2 rounded overflow-x-auto">
<code><?php echo $response; ?></code>
                    </pre>

                    <h3 class="text-lg font-medium mt-4 mb-2">Beschreibung</h3>
                    <p><?php echo $description; ?></p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
