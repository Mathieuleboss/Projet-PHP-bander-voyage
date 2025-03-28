<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/config.php';
require_once 'includes/functions.php';

$db = getDbConnection();
$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Vérifier si l'utilisateur a déjà tourné aujourd'hui (sauf pour user_id = 2)
$has_spun = false;
if ($user_id != 2) { // Exception pour l'utilisateur 2
    $today = date('Y-m-d');
    $stmt = $db->prepare("SELECT COUNT(*) FROM spin_logs WHERE user_id = :user_id AND DATE(spin_date) = :today");
    $stmt->execute([':user_id' => $user_id, ':today' => $today]);
    $has_spun = $stmt->fetchColumn() > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$has_spun) {
    $rewards = [0, 5, 10, 0, 25, 5, 0, 50]; // 8 segments avec les 0 points non adjacents
    $points_won = $rewards[array_rand($rewards)];

    try {
        $stmt = $db->prepare("INSERT INTO loyalty_points (user_id, points) VALUES (:user_id, :points)");
        $stmt->execute([':user_id' => $user_id, ':points' => $points_won]);

        $stmt = $db->prepare("INSERT INTO spin_logs (user_id, points_won) VALUES (:user_id, :points_won)");
        $stmt->execute([':user_id' => $user_id, ':points_won' => $points_won]);

        $success_message = "Félicitations ! Vous avez gagné $points_won points.";
    } catch (PDOException $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}

// Récupérer le total des points
$stmt = $db->prepare("SELECT COALESCE(SUM(points), 0) as total_points FROM loyalty_points WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$total_points = $stmt->fetchColumn();

$page_title = "Roulette du Hasard - Bander-Sneakers";
include 'includes/header.php';
?>
<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <ul class="breadcrumb-list">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="compte.php">Mon Compte</a></li>
            <li><a href="spin.php">Roulette à points</a></li>
        </ul>
    </div>
</div>

<div class="spin-container">
    <h1>Roulette à points</h1>
    <p>Tournez la roulette une fois par jour pour gagner des points !</p>
    <p>Vos points actuels : <strong><?= $total_points ?></strong></p>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-error"><?= $error_message ?></div>
    <?php endif; ?>

    <div class="content-wrapper">
        <!-- Roulette à gauche -->
        <div class="roulette-section">
            <div class="roulette-wrapper">
                <div id="roulette" class="roulette"></div>
                <div class="arrow"></div>
            </div>
            <button id="spin-button" class="btn btn-primary" <?= $has_spun ? 'disabled' : '' ?>>
                <?= $has_spun ? 'Déjà tourné aujourd\'hui' : 'Tourner la roulette' ?>
            </button>
        </div>

        <!-- Légende à droite -->
        <div class="legend-section">
            <div class="legend">
                <h3>Légende des récompenses</h3>
                <ul>
                    <li><span class="color-box" style="background: #252525;"></span> Noir : <strong>‎ 0 point</strong></li>
                    <li><span class="color-box" style="background: #ff3e3e;"></span> Rouge : <strong>‎ 5 points</strong></li>
                    <li><span class="color-box" style="background: #ff7700;"></span> Orange : <strong>‎ 10 points</strong></li>
                    <li><span class="color-box" style="background: #ffffff;"></span> Blanc : <strong>‎ 25 points</strong></li>
                    <li><span class="color-box" style="background: #ffc107;"></span> Jaune : <strong>‎ 50 points</strong></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Toast pour le résultat -->
    <div id="result-toast" class="toast hidden">
        <p id="result-text"></p>
    </div>

    <!-- Ajout de l'audio pour l'effet sonore -->
    <audio id="spin-sound" src="sounds/spin.mp3" preload="auto"></audio>
</div>

<script>
    const rewards = [0, 5, 10, 0, 25, 5, 0, 50]; // 8 segments avec les 0 points non adjacents
    const segments = rewards.length;
    let isSpinning = false;

    // Associer une couleur et un nom à chaque nombre de points
    const pointColors = {
        0: { color: '#252525', name: 'Noir' },  // Noir
        5: { color: '#ff3e3e', name: 'Rouge' },  // Rouge
        10: { color: '#ff7700', name: 'Orange' }, // Orange
        25: { color: '#ffffff', name: 'Blanc' },  // Blanc
        50: { color: '#ffc107', name: 'Jaune' }  // Jaune
    };

    // Dessiner la roulette
    function drawRoulette() {
        const roulette = document.getElementById('roulette');
        roulette.innerHTML = '';
        const segmentAngle = 360 / segments;

        rewards.forEach((reward, index) => {
            const angle = segmentAngle * index;
            const div = document.createElement('div');
            div.className = 'segment';
            div.style.transform = `rotate(${angle}deg)`; // Rotation pour chaque segment
            div.style.background = pointColors[reward].color; // Couleur en fonction du nombre de points
            // Ajouter une bordure pour les segments blanc et noir
            if (pointColors[reward].color === '#ffffff') {
                div.style.borderRight = '1px solid #000000'; // Bordure noire pour le blanc
            } else if (pointColors[reward].color === '#252525') {
                div.style.borderRight = '1px solid #ffffff'; // Bordure blanche pour le noir
            }
            // Ajouter une étiquette avec le nombre de points
            const label = document.createElement('div');
            label.className = 'segment-label';
            label.textContent = reward;
            div.appendChild(label);
            roulette.appendChild(div);
        });
    }

    function spinRoulette() {
        if (isSpinning || <?= json_encode($has_spun) ?>) return;
        isSpinning = true;
        document.getElementById('spin-button').disabled = true;

        // Jouer le son
        const spinSound = document.getElementById('spin-sound');
        spinSound.play();

        // Ajouter un flou pendant l'animation
        const roulette = document.getElementById('roulette');
        roulette.style.filter = 'blur(5px)';

        const spins = 5 + Math.random() * 5; // 5 à 10 tours
        const randomAngle = Math.floor(Math.random() * 360) + spins * 360;

        // Animation avec effet de rebond
        roulette.style.transition = 'transform 6s cubic-bezier(0.25, 0.1, 0.25, 1.1)'; // Effet de rebond
        roulette.style.transform = `rotate(${randomAngle}deg)`;

        setTimeout(() => {
            // Retirer le flou à la fin
            roulette.style.filter = 'blur(0px)';

            const finalAngle = randomAngle % 360;
            const segmentAngle = 360 / segments;
            const winningIndex = Math.floor((360 - finalAngle) / segmentAngle) % segments;
            const pointsWon = rewards[winningIndex];
            const colorName = pointColors[pointsWon].name;

            fetch('spin.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'spin=true'
            }).then(response => response.text())
              .then(() => {
                  // Afficher le toast
                  const toast = document.getElementById('result-toast');
                  const resultText = document.getElementById('result-text');
                  resultText.textContent = `Vous avez gagné ${pointsWon} !`;
                  toast.classList.remove('hidden');
                  // Cacher le toast après 3 secondes et recharger la page
                  setTimeout(() => {
                      toast.classList.add('hidden');
                      location.reload();
                  }, 3000);
              });

            isSpinning = false;
        }, 6000); // Correspond à la durée de l'animation
    }

    document.getElementById('spin-button').addEventListener('click', spinRoulette);
    window.onload = drawRoulette;
</script>

<style>
    .spin-container {
        text-align: center;
        padding: 20px;
    }
    .content-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 40px; /* Espace entre la roulette et la légende */
        flex-wrap: wrap; /* Permet de passer en colonne sur petits écrans */
        margin-top: 20px;
    }
    .roulette-section {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .roulette-wrapper {
        position: relative;
        width: 350px;
        height: 350px;
    }
    .roulette {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        position: relative;
        overflow: hidden;
        border: 8px solid #2c3e50;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }
    .segment {
        position: absolute;
        width: 100%;
        height: 100%;
        clip-path: polygon(50% 50%, 100% 0, 50% 0); /* Triangle partant du centre */
        transform-origin: center;
        border-right: 1px solid rgba(255, 255, 255, 0.3); /* Séparation entre segments */
    }
    .segment::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: inherit;
        clip-path: polygon(50% 50%, 100% 0, 50% 0); /* Même forme que le segment */
        border-top-right-radius: 100%; /* Bord arrondi pour suivre le cercle */
    }
    .segment-label {
        position: absolute;
        top: 20px;
        right: 20px;
        color: #000;
        font-size: 18px;
        font-weight: bold;
        transform: rotate(45deg); /* Rotation pour aligner avec le segment */
        text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.5); /* Ombre pour lisibilité */
    }
    .segment-label[style*="background: #252525"], 
    .segment-label[style*="background: #ff7700"] {
        color: #fff; /* Texte blanc pour les segments foncés */
    }
    .arrow {
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 15px solid transparent;
        border-right: 15px solid transparent;
        border-top: 30px solid #e74c3c;
        z-index: 10;
    }
    .btn-primary {
        padding: 12px 25px;
        font-size: 18px;
        background-color: #3498db;
        border: none;
        border-radius: 5px;
        color: white;
        cursor: pointer;
        margin-top: 20px;
    }
    .btn-primary:disabled {
        background: #95a5a6;
        cursor: not-allowed;
    }
    .btn-primary:hover:not(:disabled) {
        background-color: #2980b9;
    }
    .legend-section {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .legend {
        text-align: left;
        max-width: 300px;
    }
    .legend h3 {
        font-size: 18px;
        margin-bottom: 10px;
        text-align: center;
    }
    .legend ul {
        list-style: none;
        padding: 0;
    }
    .legend li {
        font-size: 16px;
        margin: 5px 0;
        display: flex;
        align-items: center;
    }
    .color-box {
        display: inline-block;
        width: 20px;
        height: 20px;
        margin-right: 10px;
        border: 1px solid #333;
    }
    .toast {
        position: fixed;
        bottom: 20px; /* Position en bas */
        left: 50%; /* Centré horizontalement */
        transform: translateX(-50%) translateY(20px); /* Décalage initial pour l'animation */
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    .toast:not(.hidden) {
        opacity: 1;
        transform: translateX(-50%) translateY(0); /* Centré et visible */
    }
    .toast.hidden {
        display: block; /* Toujours visible pour l'animation de sortie */
        opacity: 0;
        transform: translateX(-50%) translateY(20px); /* Remonte en bas pour disparaître */
    }
    .toast p {
        margin: 0;
        font-size: 16px;
    }
    .hidden {
        display: none;
    }
</style>

<?php include 'includes/footer.php'; ?>