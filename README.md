# Formulaire de Contact - Symfony

## 📝 Explications

Ce projet est un exercice technique consistant à créer un formulaire de contact en HTML et à traiter les données en PHP via le framework **Symfony**. Il comporte 4 étapes de difficulté croissante, avec comme objectif d’aller le plus loin possible tout en appliquant de bonnes pratiques de développement.

---

## 📋 Consignes

- La recherche sur internet est autorisée.
- L'utilisation de **tout outil ou framework** est autorisée (hors CMS comme WordPress, Drupal, etc.).
- L’accent est mis sur la **qualité du code** et les **bonnes pratiques**.
- Le code doit être **livré avec les instructions d’installation**.

---

## 🚀 Fonctionnalités par étape

### ✅ Étape 1 : Formulaire de contact

Création d’un formulaire HTML contenant les champs suivants :

- Nom
- Prénom
- Email

### ✅ Étape 2 : Enregistrement en base de données

Les informations soumises via le formulaire sont :

- Validées via Symfony Forms
- Persistées dans une base de données MySQL

### ✅ Étape 3 : Envoi d’un e-mail de confirmation

À chaque soumission du formulaire :

- Un e-mail est envoyé à avec les données soumises
- Configuration du service mail via `MailerInterface` de Symfony

### ✅ Étape 4 : Upload de fichier

Ajout d’un champ :

- Upload de fichier
- Le fichier est stocké dans un dossier local ( `/public/uploads`)
- Il est **envoyé en pièce jointe** dans l’e-mail
- Le nom du fichier est enregistré en base de données

---

## ⚙️ Installation du projet

### Prérequis

- PHP ≥ 8.1
- Composer
- Symfony CLI (optionnel mais recommandé)
- Base de données MySQL ou compatible (WAMP, XAMP, ...)

### Étapes

1. **Cloner le projet**
   
git clone -b etpae_4 https://github.com/LoricWorms/testVigicorp.git

cd testVigipro

2. **Installer les dépendances**

composer install

3. **Créer la base de données et le schéma**

php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate

4. **Lancer le serveur de développement**

symfony server:start (gardez votre terminal d'ouvert)

## 📁  Utilisation

1. Connectez ou créez un compte Mailtrap
   
2. Récupérez le paramètres SMTP de la boîte mail et remplacez la ligne 40. **MAILER_DSN="smtp://9b6f1ff8be293a:79e90ffa31d8a5@sandbox.smtp.mailtrap.io:2525"** dans le fichier .env

3. Dans le terminal : php bin/console messenger:consume async

4. Remplissez les champs du formulaire puis cliquez sur **envoyer**

5. Vérifiez la bonne récéption du mail, l'enregistrement des données dans la base de données et la présence du fichier dans le dossier  ( `/public/uploads`)
