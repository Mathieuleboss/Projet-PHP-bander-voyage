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

-- Compter le nombre total d'utilisateurs
SELECT COUNT(*) AS total_users FROM users;

-- Sélectionne les utilisateurs triés par date d'inscription (du plus récent au plus ancien)
SELECT username, email, password FROM users ORDER BY created_at DESC;

-- COMMANDE POUR SUPPRIMER UN USER (DANGER)
-- Supprime tous les utilisateurs de la table
-- DELETE FROM users;
-- Supprime un utilisateur en fonction de son username
-- DELETE FROM users WHERE username = 'username';
