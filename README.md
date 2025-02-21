![amoJo PHP Client](.github/logo.png?raw=true)
<p align="center">
  <h1 align="center">amoJo PHP Client</h1>
  <p align="center">🚀 PHP клиент для интеграции с сервисом API чатов amoCRM</p>

  <p align="center">
    <a href="https://packagist.org/packages/fcritic/amojo-api-client">
      <img src="https://img.shields.io/packagist/v/fcritic/amojo-api-client.svg?style=flat-square" alt="Версия">
    </a>
    <a href="https://packagist.org/packages/fcritic/amojo-api-client">
      <img src="https://img.shields.io/packagist/dt/fcritic/amojo-api-client.svg?style=flat-square" alt="Загрузки">
    </a>
    <a href="https://php.net">
      <img src="https://img.shields.io/badge/PHP-7.4%2B-blue.svg?style=flat-square" alt="Версия PHP">
    </a>
    <a href="LICENSE">
      <img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="License">
    </a>
  </p>
</p>

## Содержание
- [✨ Возможности](#-возможности)
- [📦 Установка](#-установка)
- [🚀 Быстрый старт](#-быстрый-старт)
  - [Управление каналами](#управление-каналами)
  - [Работа с чатами](#работа-с-чатами)
  - [Работа с сообщениями](#работа-с-сообщениями)
  - [Дополнительные функции](#дополнительные-функции)
- [🔌 Кастомные middleware](#-кастомные-middleware)
- [📢 Обработка ошибок](#-обработка-ошибок)
- [🔐 Валидация WebHooks](#-валидация-webhooks)
- [📝 Документация](#-документация)
- [📄 Лицензия](#-лицензия)

---

## ✨ Возможности

- Полное покрытие API чатов amoCRM
- Строгая типизация данных через DTO
- Гибкая система Middleware
- Валидация WebHooks
- Поддержка сегментов .ru и .com
- Подробная обработка ошибок
- Инструменты разработчика
  - Строгая типизация (strict_types)
  - PSR-12 совместимый код
  - Полная документация PHPDoc
  - Поддержка Guzzle и PSR-18

---

## 📦 Установка

Установка через Composer:

```bash
composer require fcritic/amojo-php-client
```
### Требования:
- PHP 7.4+

---

## 🚀 Быстрый старт
### Управление каналами
##### 1. Подключение канала
```php
use AmoJo\Client\AmoJoClient;
use AmoJo\Models\Channel;

$channel = new Channel(uid: 'channel-uid', secretKey: 'secret-key');
$client = new AmoJoClient(
    channel: $channel,
    additionalMiddleware: [],
    segment: 'ru'
);

$response = $client->connect(
    accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d',
    title: 'Мой канал',
    hookVersion: 'v2'
);

echo 'Scope ID: ' . $response->getScopeId();
```

##### 2. Отключение канала
```php
$response = $client->disconnect(accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d');
if ($response->getDisconnect()) {
    echo 'Канал успешно отключен';
}
```

---

### Работа с чатами
##### 1. Создание чата
```php
use AmoJo\Models\Conversation;
use AmoJo\Models\Users\Sender;

$conversation = (new Conversation())->setId('chat-123');
$contact = (new Sender())
    ->setId('user-123')
    ->setName('Иван Иванов')
    ->setAvatar('https://picsum.photos/300/300')
    ->setProfile((new UserProfile())->setPhone('+1464874556719'));

$response = $client->createChat(
    accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d',
    conversation: $conversation,
    contact: $contact
);

echo 'ID чата в API чатов: ' . $response->getConversationRefId();
```


### Работа с сообщениями
##### 1. Входящие текстовое сообщение
```php
use AmoJo\Models\Payload;
use AmoJo\Models\Messages\TextMessage;

$message = (new TextMessage())->setUid('MSG_100')->setText('Hello');

$response = $client->sendMessage(
    accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d',
    payload: (new Payload())
        ->setConversation($conversation)
        ->setSender($contact)
        ->setMessage($message),
    externalId: 'Источник'
);

echo 'ID чата в API чатов: ' . $response->getReceiverRefId();
```

##### 2. Исходящие текстовое сообщение
```php
use AmoJo\Models\Users\Receiver;

// amojo_id пользователя amoCRM
$sender = (new Sender())->setRefId('113de373-a2d3-4eb7-a67c-04660332df07');
$message = (new TextMessage())->setUid('MSG_101')->setText('Hello');

$response = $client->sendMessage(
    accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d',
    payload: (new Payload())
        ->setConversation($conversation)
        ->setSender($sender)
        ->setReceiver($contact)
        ->setMessage($message),
    externalId: 'Источник'
);
```

##### 3. Редактирование сообщения
```php
$message = (new TextMessage())->setUid('MSG_101')->setText('Hello, Richard');

$response = $client->editMessage(
    accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d',
    (new Payload())
        ->setConversation($conversation)
        ->setMessage($message)
);
```

##### 4. Ответ на сообщения
```php
use AmoJo\Models\Messages\ReplyTo;

$message = (new TextMessage())->setUid('MSG_102')->setText('I want to place an order');

$response = $client->sendMessage(
    accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d',
    payload: (new Payload())
        ->setConversation($conversation)
        ->setSender($contact)
        ->setMessage($message)
        ->setReplyTo((new ReplyTo())->setReplyUid('MSG_101'))
);
```

---

### Дополнительные функции
##### 1. История чата
```php
$response = $client->getHistoryChat(
    accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d',
    conversationRefId: $conversation->getRefId()
);

foreach ($response->getMessages() as $message) {
    echo 'Текст сообщения: ' . $message->getMessage()->getText();
}
```

##### 2. Обновление статуса доставки
```php
use AmoJo\Enum\DeliveryStatus;
use AmoJo\Enum\ErrorCode;
use AmoJo\Models\Deliver;

$response = $client->deliverStatus(
    accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d',
    messageUid: $message->getRefUid(),
    deliver: (new Deliver(DeliveryStatus::ERROR))
        ->setErrorCode(ErrorCode::WITH_DESCRIPTION)
        ->setMessageError('User deleted')
);

if ($response->getDelivery()) {
    echo 'Статус установлен';
}
```

##### 3. Отправка или снятие реакции
```php
$response = $client->react(
    accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d',
    conversation: $conversation,
    sender: $contact,
    message: $message,
    emoji: '👍'
);

if ($response->getReact()) {
    echo 'Реакция установлена';
}
```

##### 4. Информации о печатание
```php
$response = $client->typing(
    accountUid: 'f36b8c48-ed97-4866-8aba-d55d429da86d',
    conversation: $conversation,
    sender: $contact,
);

if ($response->getTyping()) {
    echo 'Информация доставлена';
}
```

---

## 🔌 Кастомные middleware
##### Пример middleware для логирования запросов:
```php
use AmoJo\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Closure;

final class LoggingMiddleware implements MiddlewareInterface
{
    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            error_log('Request: ' . $request->getMethod() . ' ' . $request->getUri());
            return $handler($request, $options);
        };
    }
}
```

##### Подключение middleware:
```php
$client = new AmoJoClient(
    channel: $channel,
    additionalMiddleware: [LoggingMiddleware::class]
);
```

---

## 📢 Обработка ошибок
Клиент выбрасывает исключения при ошибках:

- ```AmoJoException``` - Базовое исключение
- ```EmptyMessageErrorException``` - Не передано сообщение об ошибки при 905 коде ошибке deliverStatus()
- ```InvalidRequestWebHookException``` - не валидном вебхуке об исходящим сообщении
- ```InvalidResponseException``` - Некорректный ответ сервера
- ```NotFountException``` - некорректный URL запроса
- ```RequiredParametersMissingException``` - Отсутствуют обязательные параметры
- ```SenderException``` - Не был передан ID внутреннего пользователя при исходящим сообщение

Пример обработки:

```php
try {
    // Вызов API метода
} catch (RequiredParametersMissingException $e) {
    echo "Ошибка: " . $e->getMessage();
} catch (AmoJoException $e) {
    var_dump([
        'type'    => $e->getType(),
        'message' => $e->getMessage(),
        'code'    => $e->getCode(),
        'context' => $e->getContext(),
        'file'    => $e->getFile(),
    ]);
}
```

---

## 🔐 Валидация WebHooks

```php
use AmoJo\Helpers\ValidatorWebHooks;

if (! ValidatorWebHooks::isValid(request: $request, secretKey: '465c28d756f...')) {
    // Обработка не валидного вебхука
}
```

---

## 📝 Документация
Официальная документация API чатов amoCRM:
- <a href="https://www.amocrm.ru/developers/content/chats/chat-start">Начало работы</a>
- <a href="https://www.amocrm.ru/developers/content/chats/chat-api-reference">Методы API чатов</a>
- <a href="https://www.amocrm.ru/developers/content/chats/chat-webhooks">Webhooks документация</a>

---

## 📄 Лицензия
Проект распространяется под лицензией MIT - подробности в файле <a href="LICENSE">LICENSE</a>