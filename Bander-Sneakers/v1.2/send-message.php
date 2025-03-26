<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = "Vous devez être connecté pour envoyer un message.";
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = "Méthode non autorisée.";
    echo json_encode($response);
    exit;
}

$conversation_id = isset($_POST['conversation_id']) ? (int)$_POST['conversation_id'] : 0;
$message_text = trim($_POST['message_text'] ?? '');
$user_id = (int)$_SESSION['user_id'];

if ($conversation_id <= 0 || empty($message_text)) {
    $response['message'] = "Données invalides.";
    echo json_encode($response);
    exit;
}

try {
    $db = getDbConnection();

    // Vérifier que l'utilisateur fait partie de la conversation avec placeholders uniques
    $query = "SELECT * FROM conversations 
              WHERE conversation_id = :conversation_id 
              AND (user1_id = :user_id1 OR user2_id = :user_id2)";
    error_log("Requête SQL dans send-message.php (vérification) : $query"); // Log ajouté
    $stmt = $db->prepare($query);
    $stmt->bindParam(':conversation_id', $conversation_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id1', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id2', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$conversation) {
        $response['message'] = "Conversation non trouvée ou accès non autorisé.";
        echo json_encode($response);
        exit;
    }

    // Insérer le message
    $query = "INSERT INTO messages (conversation_id, sender_id, message_text) 
              VALUES (:conversation_id, :sender_id, :message_text)";
    error_log("Requête SQL dans send-message.php (insertion) : $query"); // Log ajouté
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':conversation_id' => $conversation_id,
        ':sender_id' => $user_id,
        ':message_text' => $message_text
    ]);

    // Récupérer l'ID et la date réelle du message inséré
    $message_id = $db->lastInsertId();
    $query = "SELECT sent_at FROM messages WHERE message_id = :message_id";
    $stmt = $db->prepare($query);
    $stmt->execute([':message_id' => $message_id]);
    $sent_at = $stmt->fetchColumn();

    // Mettre à jour la date de mise à jour de la conversation
    $query = "UPDATE conversations SET updated_at = NOW() 
              WHERE conversation_id = :conversation_id";
    error_log("Requête SQL dans send-message.php (mise à jour) : $query"); // Log ajouté
    $stmt = $db->prepare($query);
    $stmt->execute([':conversation_id' => $conversation_id]);

    $response['success'] = true;
    $response['message'] = [
        'message_text' => htmlspecialchars($message_text),
        'sent_at' => $sent_at ?: date('c') // Utilise la date réelle ou une approximation
    ];

} catch (PDOException $e) {
    error_log("Erreur PDO dans send-message.php : " . $e->getMessage());
    $response['message'] = "Erreur lors de l'envoi du message : " . $e->getMessage();
}

echo json_encode($response);
exit;
?>