<?php
$host = '127.0.0.1;port=3308';
$db   = 'tripdesk_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

/**
 * Funkce pro automatické logování změn u rezervací
 * Voláme ji vždy, když Admin/Editor něco změní.
 */
function logChange($pdo, $res_id, $user_id, $action, $details) {
    try {
        $stmt = $pdo->prepare("INSERT INTO reservation_history (reservation_id, user_id, action_type, details) VALUES (?, ?, ?, ?)");
        $stmt->execute([$res_id, $user_id, $action, $details]);
    } catch (\PDOException $e) {
        // Logování selhalo - v ostrém provozu bychom to zapsali do error_logu
        error_log("Audit Log Error: " . $e->getMessage());
    }
}
?>