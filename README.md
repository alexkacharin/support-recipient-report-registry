Перед настройкой установите:
```
php 7.3
composer
mysql
```

Первоначальная настройка:
```
git clone https://gitlab.com/nethammer/support-recipient-report-registry.git
composer install
php init
```

Изменить CURRENT_DOMAIN в backend/web/index.php, frontend/web/index.php на свой:
```
defined('CURRENT_DOMAIN') or define('CURRENT_DOMAIN', 'srrr.dvizh.net');
```

Настроить подключение к базе в файле common/config/main-local.php
Выполнить миграции:
```
php yii migrate/up --migrationPath=@yii/rbac/migrations
php yii migrate/up --migrationPath=@dektrium/user/migrations
php yii migrate/up --migrationPath=@Matodor/Common/migrations
php yii migrate/up --migrationPath=@Matodor/RegistryConstructor/migrations
php yii migrate
```

Данные от админа:
```
superadmin:admintws
```

Обновление системы:
```
git pull
composer install
php yii migrate/up --migrationPath=@Matodor/Common/migrations
php yii migrate/up --migrationPath=@Matodor/RegistryConstructor/migrations
php yii migrate
```
