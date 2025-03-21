<?php
include('config_db.php'); // Connexion centralisée

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;

// Vérification de la connexion
if (!$pdo) {
    die(json_encode(["error" => "Database connection failed."]));
}

// Requête SQL modifiée pour récupérer les noms d'avatar et le serveur
$stmt = $pdo->prepare("
    SELECT 
        t.UUID, 
        COALESCE(u1.avatar, t.sender) AS sender_name, 
        COALESCE(u2.avatar, t.receiver) AS receiver_name, 
        t.amount, 
        t.senderBalance, 
        t.receiverBalance, 
        t.objectName, 
        t.description, 
        t.time, 
        u1.serverurl AS sender_server, 
        u2.serverurl AS receiver_server
    FROM transactions t
    LEFT JOIN userinfo u1 ON t.sender = u1.user
    LEFT JOIN userinfo u2 ON t.receiver = u2.user
    ORDER BY t.time DESC 
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extraction du nom du serveur depuis serverurl
foreach ($transactions as &$transaction) {
    $transaction['sender_server'] = parse_url($transaction['sender_server'], PHP_URL_HOST) ?? "Local Grid";
    $transaction['receiver_server'] = parse_url($transaction['receiver_server'], PHP_URL_HOST) ?? "Local Grid";
}

header('Content-Type: application/json');
echo json_encode($transactions);
?>
