# Bander-Sneakers

Bander-Sneakers est une plateforme e-commerce spécialisée dans la vente de sneakers. Ce projet propose une solution complète avec une interface client moderne et un panneau d'administration pour gérer les produits, les commandes et les utilisateurs.

![Bannière Bander-Sneakers](https://ext.same-assets.com/668012345/3542577725.jpeg)

## 🚀 Fonctionnalités

### Côté Client
- Catalogue complet de sneakers avec filtrage (par marque, catégorie, prix)
- Système de recherche avancé
- Fiches produits détaillées avec galerie d'images
- Système d'avis et de notation
- Panier d'achat avec gestion des quantités
- Système de favoris/liste de souhaits
- Processus de paiement sécurisé
- Suivi de commandes
- Compte utilisateur personnalisé

### Côté Administration
- Tableau de bord avec statistiques
- Gestion complète des produits (ajout, modification, suppression)
- Gestion des stocks et des tailles
- Gestion des commandes et des statuts
- Gestion des utilisateurs
- Rapports de ventes
- Gestion des catégories et marques

## 📋 Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx)
- Gestionnaire de dépendances Composer (recommandé)

## ⚙️ Installation

### 1. Cloner le dépôt
```bash
git clone https://github.com/votre-utilisateur/bander-sneakers.git
cd bander-sneakers
```

### 2. Configuration de la base de données
1. Créez une base de données MySQL nommée `bander_sneakers`
2. Importez le fichier SQL de structure et données initiales :
```bash
mysql -u votre_utilisateur -p bander_sneakers < bdd/bander_sneakers.sql
```
3. Configurez les paramètres de connexion dans `includes/config.php` :
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
define('DB_NAME', 'bander_sneakers');
```

### 3. Configuration du serveur web
Assurez-vous que le serveur web (Apache/Nginx) est configuré pour pointer vers le répertoire racine du projet.

#### Configuration Apache (exemple)
```apache
<VirtualHost *:80>
    ServerName bander-sneakers.local
    DocumentRoot /chemin/vers/bander-sneakers

    <Directory /chemin/vers/bander-sneakers>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/bander-sneakers-error.log
    CustomLog ${APACHE_LOG_DIR}/bander-sneakers-access.log combined
</VirtualHost>
```

### 4. Création des répertoires d'upload
Assurez-vous que les répertoires d'upload existent et sont accessibles en écriture :
```bash
mkdir -p assets/images/uploads
mkdir -p assets/images/sneakers
mkdir -p assets/images/brands
chmod -R 755 assets/images
```

### 5. Compte administrateur par défaut
- Email: admin@bander-sneakers.com
- Mot de passe: admin123

## 📁 Structure du projet

```
bander-sneakers/
├── admin/              # Panneau d'administration
│   ├── assets/         # Ressources CSS/JS pour l'admin
│   ├── includes/       # Fichiers d'inclusion admin
│   └── ...             # Pages d'administration
├── assets/             # Ressources statiques
│   ├── css/            # Fichiers CSS
│   ├── js/             # Fichiers JavaScript
│   └── images/         # Images
│       ├── brands/     # Logos des marques
│       ├── sneakers/   # Images des produits
│       └── uploads/    # Uploads utilisateurs
├── includes/           # Fichiers d'inclusion principaux
│   ├── config.php      # Configuration
│   ├── functions.php   # Fonctions utilitaires
│   ├── header.php      # En-tête du site
│   └── footer.php      # Pied de page du site
├── bdd/                # Fichiers de base de données
├── cart.php            # Panier d'achat
├── checkout.php        # Processus de paiement
├── index.php           # Page d'accueil
├── sneaker.php         # Page de détail produit
└── ...                 # Autres pages
```

## 🔧 Personnalisation

### Personnalisation du thème
Vous pouvez modifier les couleurs et le style du site en éditant les variables CSS dans `assets/css/style.css` :

```css
:root {
    --primary-color: #ff3e3e;
    --secondary-color: #252525;
    /* ... autres variables ... */
}
```

### Ajouter des produits
1. Connectez-vous au panneau d'administration (`/admin`)
2. Naviguez vers "Produits" > "Ajouter un produit"
3. Remplissez le formulaire avec les détails du produit
4. Ajoutez des images et sélectionnez les tailles disponibles
5. Cliquez sur "Enregistrer"

## 🛒 Processus de paiement

Le système de paiement est configuré pour fonctionner avec deux méthodes :
1. **Carte bancaire** : Intégration simulée (en mode test)
2. **PayPal** : Intégration simulée (en mode test)

Pour configurer un vrai système de paiement en production :
1. Modifiez le fichier `checkout.php` pour intégrer une API de paiement réelle
2. Ajoutez les clés API dans `includes/config.php` :
```php
define('PAYMENT_API_KEY', 'votre_clé_api');
define('PAYMENT_SECRET_KEY', 'votre_clé_secrète');
```

## 🔐 Sécurité

- Toutes les requêtes SQL utilisent des requêtes préparées pour éviter les injections SQL
- Les mots de passe sont hachés avec la fonction `password_hash()` de PHP
- Validation des données côté serveur pour tous les formulaires
- Protection CSRF sur les formulaires importants
- Filtrage et nettoyage des données utilisateur

## 📱 Responsive Design

Le site est entièrement responsive et s'adapte à tous les appareils :
- Mobile (< 576px)
- Tablette (576px - 768px)
- Ordinateur portable (768px - 992px)
- Écran large (> 992px)

## 📝 Développement

### Ajouter une nouvelle page
1. Créez un nouveau fichier PHP à la racine du projet
2. Incluez les fichiers nécessaires :
```php
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Titre et description de la page
$page_title = "Titre de la page";
$page_description = "Description de la page";

// Inclure l'en-tête
include 'includes/header.php';
?>

<!-- Contenu de la page -->

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
```

### Ajouter une nouvelle fonctionnalité
1. Développez les fonctions nécessaires dans `includes/functions.php`
2. Créez les tables de base de données si nécessaire
3. Implémentez la logique dans les pages concernées

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus d'informations.

## 👥 Auteurs

- Développé par l'équipe Bander-Sneakers

## 🙏 Remerciements

- [Font Awesome](https://fontawesome.com/) pour les icônes
- [Unsplash](https://unsplash.com/) pour certaines images
- [Nike](https://www.nike.com/) et [Adidas](https://www.adidas.com/) pour l'inspiration du design
