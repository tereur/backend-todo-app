# Backend Todo App

Une application backend de gestion des tâches développée avec Laravel.

##  Prérequis

- PHP 8.2 ou supérieur  
- Composer  
- MySQL ou une autre base de données compatible   

---

##  Installation

### 1. Cloner le projet

```bash
git clone https://github.com/tereur/backend-todo-app.git
cd backend-todo-app
```

### 2. Installer les dépendances backend

```bash
composer install
```

### 3. Configuration de l'environnement

Créer un fichier `.env` à partir du modèle :

```bash
cp .env.example .env
```

Modifier les informations de connexion à la base de données dans le fichier `.env` :  

```
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backend_todo_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Générer la clé de l'application

```bash
php artisan key:generate
```

### 5. Migrer la base de données

```bash
php artisan migrate
```

### 6. Lancer le serveur

```bash
php artisan serve
```

L'application sera accessible à [http://localhost:8000](http://localhost:8000).

---

##  Tests

Pour exécuter les tests :

```bash
php artisan test
```

---

##  API Documentation

L'application expose une documentation Swagger si disponible. Pour accéder à la documentation des API :

```bash
http://localhost:8000/docs
```
