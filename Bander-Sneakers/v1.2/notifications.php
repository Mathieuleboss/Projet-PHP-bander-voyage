<?php
// Afficher les messages de réussite ou d'erreur
if (isset($_SESSION['success_message'])) {
    echo '<div class="notification success">';
    echo '<i class="fas fa-check-circle"></i>';
    echo '<span>' . htmlspecialchars($_SESSION['success_message']) . '</span>';
    echo '<button class="close-notification"><i class="fas fa-times"></i></button>';
    echo '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="notification error">';
    echo '<i class="fas fa-exclamation-circle"></i>';
    echo '<span>' . htmlspecialchars($_SESSION['error_message']) . '</span>';
    echo '<button class="close-notification"><i class="fas fa-times"></i></button>';
    echo '</div>';
    unset($_SESSION['error_message']);
}
?>
