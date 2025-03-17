-- Vérifie si la base de données existe avant de la créer
CREATE DATABASE IF NOT EXISTS bander_travel;

-- Sélectionne la base de données
USE bander_travel;

-- Création de la table users avec des améliorations
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'assistant') DEFAULT 'user', -- Ajout du rôle
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table avis_utilisateurs avec rating et reply
CREATE TABLE IF NOT EXISTS avis_utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    comment TEXT NOT NULL,
    rating INT DEFAULT NULL,
    reply TEXT DEFAULT NULL,
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Création de la table des réponses aux avis
CREATE TABLE IF NOT EXISTS reponses_avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    avis_id INT NOT NULL,
    user_id INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    reponse TEXT NOT NULL,
    date_reponse TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (avis_id) REFERENCES avis_utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Création de la table recherches
CREATE TABLE IF NOT EXISTS recherches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    terme_recherche VARCHAR(255) NOT NULL,
    date_recherche TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_depart DATE,
    date_retour DATE,
    type_voyage VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Création de la table des messages pour l'assistant
CREATE TABLE IF NOT EXISTS messages_assistant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    reponse TEXT,
    date_message TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Ajout manuel des colonnes si elles n'existent pas déjà
-- Vérifie si la colonne rating existe avant de l'ajouter
ALTER TABLE avis_utilisateurs ADD COLUMN IF NOT EXISTS rating INT DEFAULT NULL;

-- Vérifie si la colonne reply existe avant de l'ajouter
ALTER TABLE avis_utilisateurs ADD COLUMN IF NOT EXISTS reply TEXT DEFAULT NULL;

-- Ajout du rôle assistant si nécessaire
INSERT INTO users (username, email, password, role)
SELECT 'Bander Assistant', 'assistant@gmail.com', '123', 'assistant'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE role = 'assistant'
);

-- Compter le nombre total d'utilisateurs
SELECT COUNT(*) AS total_users FROM users;

-- Sélection des utilisateurs triés par date d'inscription (du plus récent au plus ancien)
SELECT username, email, created_at FROM users ORDER BY created_at DESC;

-- Sélection des avis avec tri par date ou notation
SELECT *, COALESCE(rating, 0) AS rating, COALESCE(reply, '') AS reply 
FROM avis_utilisateurs 
ORDER BY date_posted DESC;

-- Afficher la structure de la table recherches
DESCRIBE recherches;