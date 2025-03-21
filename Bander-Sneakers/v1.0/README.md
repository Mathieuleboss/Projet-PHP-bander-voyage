# Bander-Sneakers

Bander-Sneakers est une plateforme e-commerce complète dédiée à la vente de sneakers. Ce projet offre une expérience utilisateur intuitive pour les clients et un système d'administration robuste pour gérer l'ensemble de la boutique en ligne.

## 🚀 Fonctionnalités

### Côté Client
- Catalogue de sneakers avec filtrage avancé (marque, catégorie, prix, etc.)
- Système de recherche performant
- Pages produits détaillées avec galerie d'images
- Système d'avis et de notation des produits
- Panier d'achat interactif avec gestion des quantités
- Liste de souhaits personnalisée
- Processus de paiement sécurisé
- Suivi de commandes en temps réel
- Comptes utilisateurs avec historique des achats
- Sections dédiées pour hommes, femmes et enfants

### Côté Administration
- Tableau de bord analytique avec statistiques des ventes
- Gestion complète du catalogue produits (CRUD)
- Gestion des stocks et des tailles disponibles
- Suivi et mise à jour des commandes
- Administration des comptes utilisateurs
- Chat en direct avec les clients
- Gestion des catégories et des marques
- Système de notifications
- Outils promotionnels et gestion des remises

## 💻 Technologies utilisées

- **Backend**: PHP natif
- **Frontend**: HTML5, CSS3, JavaScript
- **Base de données**: MySQL
- **Outils supplémentaires**:
  - Système de chat en temps réel
  - API de paiement sécurisé
  - Système de notifications

## ⚙️ Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx)

## 📋 Installation

### 1. Configuration de la base de données
1. Créez une base de données MySQL nommée `bander_sneakers`
2. Importez le fichier SQL fourni:
```bash
mysql -u [utilisateur] -p bander_sneakers < database/bander_sneakers.sql
```
3. Configurez les paramètres de connexion dans `includes/config.php`

### 2. Configuration du serveur
- Assurez-vous que votre serveur web pointe vers le répertoire racine du projet
- Configurez les droits d'accès appropriés pour les dossiers d'upload

### 3. Lancement de l'application
- Accédez au site via votre navigateur à l'adresse configurée
- Pour le panneau d'administration, naviguez vers `/admin`

### 4. Compte administrateur par défaut
- Utilisez les identifiants par défaut pour accéder au panneau d'administration
- N'oubliez pas de changer le mot de passe après la première connexion!

## 📁 Structure du projet

```
bander-sneakers/
├── admin/                 # Panneau d'administration
│   ├── assets/            # Ressources admin (CSS, JS)
│   │   ├── css/
│   │   └── js/
│   └── includes/          # Composants admin réutilisables
│       ├── footer.php
│       ├── header.php
│       └── admin-chat.php
├── assets/                # Ressources principales
│   ├── css/               # Styles CSS
│   ├── js/                # Scripts JavaScript
│   └── images/            # Images et médias
├── database/              # Fichiers de base de données
├── includes/              # Composants partagés
│   ├── config.php         # Configuration de la BD et du site
│   ├── functions.php      # Fonctions utilitaires
│   ├── header.php         # En-tête du site
│   └── footer.php         # Pied de page du site
└── Fichiers PHP principaux # Pages du site
    ├── index.php          # Page d'accueil
    ├── sneakers.php       # Catalogue principal
    ├── sneaker.php        # Page détaillée d'un produit
    ├── cart.php           # Panier d'achat
    ├── checkout.php       # Processus de paiement
    ├── login.php          # Connexion utilisateur
    ├── register.php       # Inscription utilisateur
    └── ...
```

## 🔒 Sécurité

- Protection contre les injections SQL
- Hachage sécurisé des mots de passe
- Validation des entrées utilisateur
- Protection contre les attaques CSRF
- Sessions sécurisées

## 📱 Compatibilité

- Design responsive adapté à tous les appareils
- Testé sur les navigateurs modernes (Chrome, Firefox, Safari, Edge)

## 🛠️ Personnalisation

### Thème et apparence
- Modifiez les styles dans `assets/css/style.css`
- Personnalisez les éléments d'interface dans les fichiers PHP correspondants

### Ajout de nouvelles fonctionnalités
1. Développez les fonctions nécessaires dans `includes/functions.php`
2. Créez les pages ou composants requis
3. Mettez à jour la base de données si nécessaire

## 👨‍💻 Développement futur

- Intégration d'un système de paiement en cryptomonnaies
- Application mobile native
- Système de fidélité avec points et récompenses
- Interface multilingue
- Intégration avec des fournisseurs dropshipping

## 📞 Support et contact

Pour toute question ou assistance concernant l'installation ou l'utilisation de Bander-Sneakers, veuillez nous contacter:

- Email: support@bander-sneakers.com
- Site web: www.bander-sneakers.com

## 📄 Licence

Ce projet est protégé par des droits d'auteur. Tous droits réservés.

## 👨‍👩‍👧‍👦 Contributeurs

- Équipe de développement Bander-Sneakers

---

© 2023-2025 Bander-Sneakers. Tous droits réservés.
