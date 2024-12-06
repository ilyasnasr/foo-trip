
Create the database:
```bash
php bin/console doctrine:database:create
```

Run migrations:
```bash
php bin/console doctrine:migrations:migrate
```

Load fixtures:
```bash
php bin/console doctrine:fixtures:load
```

Prepare the Database of env Test:  
```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
```

Execute Tests:
```bash
php bin/phpunit
```

## Admin Credentials

- **URL:** `http://127.0.0.1:8000/admin`
- **Username:** `admin@example.com`
- **Password:** `123456`


## Access Points

- **Admin Panel:** `http://127.0.0.1:8000/admin`
- **Front:** `http://127.0.0.1:8000`
- **API:** `http://127.0.0.1:8000/api`

## Custom Command
- **CMD:export-destinations : ** `php bin/console app:export-destinations