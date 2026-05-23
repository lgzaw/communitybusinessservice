<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // don't leak HTML errors

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Use POST.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['database']) || !isset($input['index'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON payload. Missing database or index.']);
    exit;
}

$db = $input['database'];
$idx = $input['index'];

// Try to write files
$dbFile = @fopen('database.json', 'w');
$idxFile = @fopen('index.json', 'w');

if (!$dbFile || !$idxFile) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Cannot open files for writing. Check permissions.']);
    exit;
}

fwrite($dbFile, json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
fwrite($idxFile, json_encode($idx, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
fclose($dbFile);
fclose($idxFile);

echo json_encode(['success' => true]);
?>
