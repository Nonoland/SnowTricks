# Snowtricks

Snowtricks est une application web développée en Symfony 6.3 et PHP 8.1. 


## Prérequis

- PHP 8.1+
- Symfony 6.3+
- MySQL

## Installation

1. Clonez le dépôt dans le répertoire de votre choix :

```bash
git clone https://github.com/username/Snowtrick.git
```

2. Installez les dépendances avec composer :

```bash
cd Snowtrick
composer install
```

3. Configurez votre fichier .env pour y ajouter les informations de connexion à votre base de données. Vous pouvez copier le fichier .env.dist et le renommer en .env :

```bash
cp .env.dist .env
```

4. Ouvrez le fichier .env et modifiez la ligne `DATABASE_URL` avec vos informations de connexion à la base de données.

5. Créez la base de données :

```bash
php bin/console doctrine:database:create
```

6. Exécutez les migrations pour créer les tables dans votre base de données :

```bash
php bin/console doctrine:migrations:migrate
```

7. Importez le jeu de données dans votre base de données. Le fichier SQL se trouve à la racine du projet :

```bash
mysql -u username -p database_name < data.sql
```

8. Lancez le serveur Symfony :

```bash
symfony server:start
```

9. Vous pouvez maintenant accéder à l'application à l'adresse http://localhost:8000