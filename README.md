# Tavern BO

Application Back-Office du projet Quiz Tavern

## Pré requis
[Php](https://www.php.net/) > 8.2  
[Symfony](https://symfony.com/) = 6.3  
[Bootstrap](https://getbootstrap.com/) = 5.3.2  
Base de données = MySQL | PostgreSQL  

## Installation

Cloner le projet  
```shell
git clone https://github.com/fagathe-dev/tavern-bo.git
```

Copier le fichier d'environnement
```shell
cp .env.template .env
```
Modifier la variable ``DATABASE_URL``  

Installer les dépendances du projet
```shell
composer install
```

Créer la base de données
```php
php bin/console doctrine:database:create
```

Générer les migrations
```php
php bin/console make:migration
php bin/console doctrine:migrations:migrate --no-interaction
```

Vider le cache  
```php 
php bin/console cache:clear --no-warmup
```

Ajouter un utilisateur admin
```php 
php bin/console app:create-admin
```

🚀