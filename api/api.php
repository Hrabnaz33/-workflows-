<?php
header('Content-Type: application/json');

// === CONFIG ===
$jwtSecret = 'Gab08051011riel#335'; // Bitte durch einen starken Wert ersetzen
$logFile = __DIR__ . '/api.log';

// === AUTHENTIFIZIERUNG ===
// Token kann über Header (POST) oder GET-Parameter kommen
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$getToken = $_GET['token'] ?? '';

$token = '';
if (!empty($authHeader) && strpos($authHeader, 'Bearer ') === 0) {
    $token = substr($authHeader, 7);
} elseif (!empty($getToken)) {
    $token = $getToken;
}

if ($token !== $jwtSecret) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid or missing token']);
    exit;
}

// === ENDPOINTS ===
$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true);

// Logging-Funktion
function logAction($message, $logFile) {
    $entry = date('Y-m-d H:i:s') . ' | ' . $message . PHP_EOL;
    file_put_contents($logFile, $entry, FILE_APPEND);
}

if ($action === 'dry-run') {
    logAction('Dry-Run gestartet: ' . json_encode($data), $logFile);
    echo json_encode([
        'status' => 'Dry-Run OK',
        'changes' => $data ?: $_GET,
        'message' => 'Simulation erfolgreich'
    ]);
} elseif ($action === 'deploy') {
    // Hier würdest du die Änderungen anwenden (z. B. Datei schreiben oder CMS-API aufrufen)
    logAction('Deployment durchgeführt: ' . json_encode($data), $logFile);
    echo json_encode([
        'status' => 'Deployment erfolgreich',
        'changes' => $data ?: $_GET,
        'message' => 'Änderungen live übernommen'
    ]);
} else {
    echo json_encode(['error' => 'Unknown action']);
}
?>