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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table avis_utilisateurs avec rating et reply
DROP TABLE avis_utilisateurs;

CREATE TABLE avis_utilisateurs (
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




-- Ajout manuel des colonnes si elles n'existent pas déjà
-- Vérifie si la colonne rating existe avant de l'ajouter
ALTER TABLE avis_utilisateurs ADD COLUMN rating INT DEFAULT NULL;

-- Vérifie si la colonne reply existe avant de l'ajouter
ALTER TABLE avis_utilisateurs ADD COLUMN reply TEXT DEFAULT NULL;

-- Compter le nombre total d'utilisateurs
SELECT COUNT(*) AS total_users FROM users;

-- Sélection des utilisateurs triés par date d'inscription (du plus récent au plus ancien)
SELECT username, email, password FROM users ORDER BY created_at DESC;

-- Sélection des avis avec tri par date ou notation
SELECT *, COALESCE(rating, 0) AS rating, COALESCE(reply, '') AS reply 
FROM avis_utilisateurs 
ORDER BY date_posted DESC;


