# test_task_laravel_vovo

Тестовое задание: `GET /api/products` с фильтрацией, сортировкой и пагинацией (Laravel 13).

## Требования

- PHP **8.3+** (как для Laravel 13)
- Composer 2.x
- SQLite / MySQL / PostgreSQL (для разработки подойдёт SQLite)

## Установка

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### Настройка `.env`

Минимально для SQLite:

```env
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite
```

Создайте файл БД:

```bash
touch database/database.sqlite
```

Для MySQL задайте `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

## Миграции и сиды

```bash
php artisan migrate
php artisan db:seed
```

Сиды создают **5 категорий** и **~54 товара** (реальные названия + набор кабелей для пагинации).

## Запуск локально

```bash
php artisan serve
```

API будет доступен по адресу `http://127.0.0.1:8000/api/...`.

## Endpoint: `GET /api/products`

### Фильтры (query)

| Параметр       | Описание |
|----------------|----------|
| `q`            | Подстрока в `name` (`LIKE %...%`, `%`/`_` экранируются) |
| `price_from`   | Минимальная цена (включительно) |
| `price_to`     | Максимальная цена (включительно) |
| `category_id`  | ID категории |
| `in_stock`     | `true` / `false` / `1` / `0` |
| `rating_from`  | Минимальный рейтинг `0`–`5` |

### Сортировка (`sort`)

- `price_asc`
- `price_desc`
- `rating_desc`
- `newest`

**Дефолт**, если `sort` не передан: **`newest`** — сначала новые (`created_at` по убыванию, затем `id` по убыванию для стабильности).

### Пагинация

| Параметр   | Описание |
|------------|----------|
| `page`     | Номер страницы (`1` по умолчанию) |
| `per_page` | Размер страницы (`1`–`100`, по умолчанию `15`) |

### Формат ответа

```json
{
  "data": [ { "id": 1, "name": "...", "price": "129990.00", "...": "..." } ],
  "meta": {
    "current_page": 1,
    "last_page": 4,
    "per_page": 15,
    "total": 54,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "http://127.0.0.1:8000/api/products?page=1",
    "last": "http://127.0.0.1:8000/api/products?page=4",
    "prev": null,
    "next": "http://127.0.0.1:8000/api/products?page=2"
  }
}
```

## Примеры запросов

```http
GET /api/products
GET /api/products?q=MacBook
GET /api/products?price_from=10000&price_to=50000
GET /api/products?category_id=2&in_stock=true
GET /api/products?rating_from=4.5
GET /api/products?sort=price_asc
GET /api/products?sort=rating_desc&per_page=10&page=2
```

## Тесты

```bash
php artisan test
# или
./vendor/bin/phpunit
```

Используется SQLite in-memory (`phpunit.xml`).

## Архитектура (кратко)

- **Миграции**: `categories`, `products` + FK + индексы (`name`, `price`, `rating`, составной `category_id + in_stock`).
- **Модели**: `Product` с query scopes для фильтров и сортировки; `Category` с `hasMany`.
- **HTTP**: `ProductController@index` + `IndexProductsRequest` для валидации query.
- **Маршрут**: `routes/api.php` → префикс `/api` подключается через `bootstrap/app.php` (`api:`).

Если в вашем проекте уже был `bootstrap/app.php`, добавьте строку `api: __DIR__.'/../routes/api.php',` в `withRouting(...)`, если её ещё нет.
