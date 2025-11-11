<?php
// === CONFIGURAZIONE DATABASE ===
define('DB_NAME', 'bitnami_wordpress');
define('DB_USER', 'bn_wordpress');
define('DB_PASSWORD', 'sW5sp4spa3u7RLyetrekE4oS');
define('DB_HOST', 'beta-vino-wp-mariadb:3306');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// === HEADER JSON ===
header('Content-Type: application/json; charset=utf-8');

// === VERIFICA PARAMETRO GET ===
if (!isset($_GET['q']) || trim($_GET['q']) === '') {
    http_response_code(400);
    echo json_encode(["error" => "Missing query parameter ?q="]);
    exit;
}

$query = trim($_GET['q']);

// === SICUREZZA: consenti solo SELECT / SHOW / DESCRIBE ===
if (!preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)/i', $query)) {
    http_response_code(403);
    echo json_encode(["error" => "Only read-only queries (SELECT/SHOW/DESCRIBE) are allowed"]);
    exit;
}

try {
    // === CONNESSIONE PDO ===
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // === ESECUZIONE QUERY ===
    $stmt = $pdo->query($query);
    $rows = $stmt->fetchAll();

    // === OUTPUT ===
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>

