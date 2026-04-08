# Max Messenger — Бот-визитка компании

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://php.net)
[![Max Messenger](https://img.shields.io/badge/Max%20Messenger-bot-orange)](https://max.ru)
[![License](https://img.shields.io/badge/license-Restricted%20Commercial-red)](LICENSE)

**Бот-визитка компании** для мессенджера **Max** — готовое решение для представления бизнеса в одном из крупнейших российских мессенджеров. Позволяет рассказать о компании, услугах, портфолио, контактах и принимать заявки от потенциальных клиентов. Весь контент управляется через единый YAML-файл без изменения кода.

---

## Возможности

- `/start` — приветствие + интерактивное меню с inline-кнопками
- `/help` — справка и контакты автора бота
- **О компании** — описание, год основания, численность команды
- **Услуги** — список услуг с описаниями и ценами
- **Портфолио** — реализованные проекты со ссылками
- **Контакты** — телефон, email, сайт, адрес, соцсети
- **Оставить заявку** — приём обращений от пользователей с опциональной пересылкой менеджеру
- Весь контент редактируется в одном файле `config/content.yaml` — без изменения кода
- Запуск в Docker одной командой

---

## Отображение диалогов у пользователя

![Пример главного экрана](https://github.com/GrayHoax/php-max-messanger-company-vcard/raw/master/assets/demo1.png)

![Пример раздела Справка](https://github.com/GrayHoax/php-max-messanger-company-vcard/raw/master/assets/demo2.png)

![Пример подачи заявки](https://github.com/GrayHoax/php-max-messanger-company-vcard/raw/master/assets/demo3.png)

![Пример раздела Услуг](https://github.com/GrayHoax/php-max-messanger-company-vcard/raw/master/assets/demo4.png)

![Пример раздела Контакты](https://github.com/GrayHoax/php-max-messanger-company-vcard/raw/master/assets/demo5.png)

## Быстрый старт

### Требования

- Docker & Docker Compose **или** PHP 7.4+ и Composer

### 1. Клонировать репозиторий

```bash
git clone https://github.com/GrayHoax/php-max-messanger-company-vcard
cd max-vcard-bot
```

### 2. Настроить окружение

```bash
cp .env.example .env
```

Откройте `.env` и укажите токен бота:

```env
BOT_TOKEN=your_bot_token_here
```

> Получить токен можно у бота **@MaxBotFather** в мессенджере Max.

### 3. Заполнить контент компании

Откройте `config/content.yaml` и заполните все разделы своими данными:

```yaml
company:
  name: "ООО «Ваша Компания»"
  tagline: "Слоган вашей компании"
  about: |
    Описание вашей компании...
```

Подробнее о структуре файла — в разделе [Конфигурация контента](#конфигурация-контента).

### 4. Запустить бота

**С Docker (рекомендуется):**

```bash
docker compose up -d
```

**Без Docker:**

```bash
composer install
php bot.php
```

---

## Конфигурация контента

Весь контент хранится в `config/content.yaml`. Файл разделён на секции:

| Секция | Описание |
|--------|----------|
| `company` | Название, слоган, описание, год основания |
| `services` | Список услуг с описаниями и ценами |
| `portfolio` | Реализованные проекты |
| `contacts` | Телефон, email, сайт, адрес, соцсети |
| `request` | Настройки раздела «Оставить заявку» |
| `bot` | Тексты бота и контакты автора |

### Пересылка заявок менеджеру

Укажите `chat_id` менеджера в секции `request`:

```yaml
request:
  manager_chat_id: "123456789"
```

Узнать `chat_id` можно, написав боту любое сообщение и проверив логи,
или воспользовавшись ботом `@MaxIDBot`.

---

## Структура проекта

```
max-vcard-bot/
├── config/
│   └── content.yaml       # Весь контент компании
├── src/
│   ├── Bot/
│   │   └── VCardBot.php   # Основной класс бота
│   ├── Config/
│   │   └── ContentLoader.php  # Загрузчик YAML-конфигурации
│   └── Formatter/
│       └── MessageFormatter.php  # Форматирование сообщений
├── bot.php                # Точка входа
├── composer.json
├── Dockerfile
├── docker-compose.yml
├── .env.example
└── README.md
```

---

## Технологии

- **PHP 7.4+** — основной язык
- **[grayhoax/phpmaxbot](https://github.com/GrayHoax/php-max-bot)** — фреймворк для работы с Max Bot API
- **symfony/yaml** — парсинг YAML-конфигурации
- **vlucas/phpdotenv** — управление переменными окружения
- **Docker** — контейнеризация для простого деплоя

---

## Деплой на сервер

Для production-запуска на VPS:

```bash
# Клонировать репозиторий
git clone https://github.com/GrayHoax/php-max-messanger-company-vcard
cd max-vcard-bot

# Настроить окружение и контент
cp .env.example .env && nano .env
nano config/content.yaml

# Запустить
docker compose up -d

# Просмотр логов
docker compose logs -f
```

---

## Обновление контента

После изменения `config/content.yaml` перезапуск бота **не требуется** — файл смонтирован как volume и читается при каждом обращении пользователя.

---

## Лицензия

Данный проект распространяется на условиях [Source Available License with Restricted Commercial Use](LICENSE).

- ✅ Некоммерческое использование — разрешено
- ✅ Изучение и модификация — разрешено
- ❌ Коммерческое использование — **требует письменного согласования с автором**

По вопросам коммерческого использования обращайтесь: [github.com/GrayHoax](https://github.com/GrayHoax)

---

## Автор

**GrayHoax** — [github.com/GrayHoax](https://github.com/GrayHoax)

Если бот помог вашему бизнесу — поставьте ⭐ репозиторию!
