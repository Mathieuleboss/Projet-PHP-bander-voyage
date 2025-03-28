<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Désactiver les erreurs visibles pour éviter de casser le JSON
error_reporting(0);
ini_set('display_errors', 0);

$db = getDbConnection();
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_messages':
        $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $selected_user_id = isset($_GET['user_id']) && is_numeric($_GET['user_id']) ? (int)$_GET['user_id'] : null;

        if (!$user_id && !$selected_user_id) {
            echo json_encode(['success' => false, 'error' => 'Utilisateur non spécifié']);
            exit();
        }

        $query_user_id = $selected_user_id ?? $user_id;
        $stmt = $db->prepare("
            SELECT cm.*, u.username
            FROM chat_messages cm
            LEFT JOIN users u ON u.user_id = cm.user_id
            WHERE cm.user_id = :user_id
            ORDER BY cm.created_at ASC
        ");
        $stmt->execute([':user_id' => $query_user_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'messages' => $messages]);
        break;

    case 'send_message':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            exit();
        }

        $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $selected_user_id = isset($_POST['user_id']) && is_numeric($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $message_text = cleanInput($_POST['message'] ?? '');
        $is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true ? 1 : 0;
        $admin_id = $is_admin ? $user_id : null;

        error_log("send_message: user_id=$user_id, selected_user_id=$selected_user_id, message=$message_text, is_admin=$is_admin");

        if (empty($message_text)) {
            echo json_encode(['success' => false, 'error' => 'Message vide']);
            exit();
        }

        if (!$user_id && !$selected_user_id && !$is_admin) {
            echo json_encode(['success' => false, 'error' => 'Utilisateur non spécifié']);
            exit();
        }

        $target_user_id = $is_admin ? $selected_user_id : $user_id;
        try {
            $stmt = $db->prepare("
                INSERT INTO chat_messages (user_id, admin_id, message_text, is_admin)
                VALUES (:user_id, :admin_id, :message_text, :is_admin)
            ");
            $stmt->execute([
                ':user_id' => $target_user_id,
                ':admin_id' => $admin_id,
                ':message_text' => $message_text,
                ':is_admin' => $is_admin
            ]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            error_log("Erreur PDO dans chat-api: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Erreur base de données']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Action non valide']);
        break;
}
exit();
?>