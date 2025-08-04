<p align="left">
  <img src="public/logo-um.webp" alt="Université de Mbuji-Mayi Logo" width="100" style="margin-right: 15px; vertical-align: middle;">
  <strong style="font-size: 2em; vertical-align: middle;"></strong>
</p>

<p>
  <img src="https://img.shields.io/badge/build-passing-brightgreen" alt="Build Status">
  <img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License">
  <img src="https://img.shields.io/badge/accessibility-designed%20for%20all-blueviolet" alt="Accessibility Badge">
</p>

---

# AlerteUM

---

## 🇬🇧 English

### 🎓 Project Overview

**AlerteUM** is a web application developed for the Université de Mbuji-Mayi (um.ac.cd) to help report and manage incidents related to the university’s website. Users can select problems from a categorized catalog, submit detailed reports, and track them via dedicated dashboards. The platform is designed with accessibility and user experience in mind.

### ✨ Key Features
- Category-based incident reporting
- Real-time tracking of incidents
- Dashboards for users and administrators
- Notifications and internal comments
- Accessible UI
- CSV export

### 🛠️ Tech Stack

- Laravel (PHP)
- Blade + TailwindCSS
- MySQL
- Breeze, Vite, Role Middleware

### 🚀 Installation

#### Requirements
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL

#### Steps

```bash
git clone https://github.com/your-username/alerteum.git
cd alerteum
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run dev
php artisan serve
```

### 🔒 Roles & Permissions

- **Users**: can create, view, edit, and delete their own incidents; track progress; receive notifications; access a full dashboard.
- **Admins**: manage all reported incidents; assign them to other admins; change their status; access a global dashboard and receive notifications for all new reports.

---


## 🔐 Security

If you find a security vulnerability, please contact the maintainer privately.

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

# 🇫🇷 Français

### 🎓 Présentation du projet

**AlerteUM** est une application web développée pour l’Université de Mbuji-Mayi (um.ac.cd). Elle permet de signaler, suivre et gérer les incidents liés au site web de l’université à partir d’un catalogue de catégories. Les utilisateurs accèdent à un tableau de bord dédié. L’interface a été pensée pour être accessible et intuitive.

### ✨ Fonctionnalités principales
- Signalement d’incidents par catégorie
- Suivi en temps réel
- Tableaux de bord utilisateurs et administrateurs
- Notifications et commentaires internes
- Interface accessible
- Exportation CSV

### 🛠️ Stack technique

- Laravel (PHP)
- Blade + TailwindCSS
- MySQL
- Breeze, Vite, middleware de rôles

### 🚀 Installation

#### Prérequis
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL

#### Étapes

```bash
git clone https://github.com/your-username/alerteum.git
cd alerteum
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run dev
php artisan serve
```

### 🔒 Rôles et permissions

- **Utilisateurs** : peuvent créer, consulter, modifier et supprimer leurs propres incidents ; suivre leur évolution ; recevoir des notifications ; accéder à un tableau de bord complet.
- **Administrateurs** : peuvent gérer tous les incidents ; les assigner ; changer leur statut ; accéder à un tableau de bord global et recevoir les notifications des nouveaux signalements.

---


## 🔐 Sécurité

En cas de faille de sécurité, veuillez contacter le mainteneur en privé.

## 📄 Licence

Ce projet est sous licence [MIT](LICENSE).
