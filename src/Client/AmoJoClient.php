<?php

declare(strict_types=1);

namespace AmoJo\Client;

use AmoJo\DTO\Response\ConnectResponse;
use AmoJo\DTO\Response\CreateChatResponse;
use AmoJo\DTO\Response\EmptyResponse;
use AmoJo\DTO\Response\HistoryChatResponse;
use AmoJo\DTO\Response\MessageResponse;
use AmoJo\DTO\ResponseFactory;
use AmoJo\Enum\ActionType;
use AmoJo\Enum\EventType;
use AmoJo\Exception\AmoJoException;
use AmoJo\Exception\RequiredParametersMissingException;
use AmoJo\Models\Channel;
use AmoJo\Models\Conversation;
use AmoJo\Models\Deliver;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Interfaces\SenderInterface;
use AmoJo\Models\Interfaces\UserInterface;
use AmoJo\Models\Payload;

/**
 * AmoJoClient клиент для сервиса чатов amoCRM
 */
class AmoJoClient
{
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    private Channel $channel;
    private AmoJoHttpClient $httpClient;

    /**
     * @param Channel $channel
     * @param array $additionalMiddleware массив с кастомными middleware implements AmoJo\Middleware\MiddlewareInterface
     * @param string $segment ru | com
     */
    public function __construct(Channel $channel, array $additionalMiddleware = [], string $segment = 'ru')
    {
        $this->channel = $channel;
        $this->httpClient = new AmoJoHttpClient($additionalMiddleware, $segment);
    }

    /**
     * `Метод подключение канала чатов к аккаунту.`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Подключение-канала-чата-в-аккаунте
     *
     * @param string $accountUuid Получаем из запроса getCurrent(AccountModel::getAvailableWith())->getAmojoId()
     * | GET /api/v4/account?with=amojo_id
     * @param string|null $title Если свойство не передано, то тянется название канала указанное при регистрации
     * @param string $hookVersion На текущий момент актуальная версия v2
     * @return ConnectResponse
     */
    public function connect(string $accountUuid, ?string $title = null, string $hookVersion = 'v2'): ConnectResponse
    {
        $this->validateUuid($accountUuid);

        $response = $this->httpClient->post(sprintf('%s/%s', $this->getChannel()->getUuid(), ActionType::CONNECT), [
            'secret_key' => $this->getChannel()->getSecretKey(),
            'json' => [
                'account_id' => $accountUuid,
                'title' => $title,
                'hook_api_version' => $hookVersion,
            ],
        ]);

        /** @var ConnectResponse */
        return ResponseFactory::create($response, ActionType::CONNECT);
    }

    /**
     * `Метод отключение канала чатов от аккаунта.`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Отключение-канала-чата-в-аккаунте
     *
     * @param string $accountUuid Получаем из запроса getCurrent(AccountModel::getAvailableWith())->getAmojoId()
     * | GET /api/v4/account?with=amojo_id
     * @return EmptyResponse
     */
    public function disconnect(string $accountUuid): EmptyResponse
    {
        $this->validateUuid($accountUuid);

        $response = $this->httpClient->delete(
            sprintf('%s/%s', $this->getChannel()->getUuid(), ActionType::DISCONNECT),
            [
                'secret_key' => $this->getChannel()->getSecretKey(),
                'json' => [
                    'account_id' => $accountUuid,
                ],
            ]
        );

        /** @var EmptyResponse */
        return ResponseFactory::create($response);
    }

    /**
     * `Метод создание чата`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Создание-нового-чата
     *
     * @param string $accountUuid GET /api/v4/account?with=amojo_id
     * @param Conversation $conversation Передается ID чата на стороне интеграции
     * @param UserInterface $contact Модель контакта со свойствами на стороне интеграции
     * @param string|null $externalId Источник https://www.amocrm.ru/developers/content/crm_platform/sources-api
     * - Важно: источник должен иметь параметр origin_code со значением кода канала чатов
     * - Важно: источник должен быть создан от интеграции к которой привязан канал чатов
     * @return CreateChatResponse
     */
    public function createChat(
        string $accountUuid,
        Conversation $conversation,
        UserInterface $contact,
        ?string $externalId = null
    ): CreateChatResponse {

        if ($conversation->getId() === null) {
            throw new RequiredParametersMissingException(
                'To create a chat, you need the chat ID on the integration side.'
            );
        }

        /** Социальный профиль контакта $options['json'] */
        $options = [
            'secret_key' => $this->getChannel()->getSecretKey(),
            'json' => [
                'conversation_id' => $conversation->getId(),
                'source' => ['external_id' => $externalId],
                'user' => $contact->toPayload(),
            ],
        ];

        if ($externalId === null) {
            unset($options['json']['source']);
        }

        $response = $this->httpClient->post(
            sprintf('%s/%s', $this->getScopeId($accountUuid), ActionType::CHAT),
            $options
        );

        /** @var CreateChatResponse */
        return ResponseFactory::create($response, ActionType::CHAT);
    }

    /**
     * `Метод импорта сообщения`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Отправка-редактирование-или-импорт-сообщения
     *
     * @param string $accountUuid GET /api/v4/account?with=amojo_id
     * @param Payload $payload Запрос модели
     * @param string|null $externalId Источник https://www.amocrm.ru/developers/content/crm_platform/sources-api
     * - Важно: источник должен иметь параметр origin_code со значением кода канала чатов
     * - Важно: источник должен быть создан от интеграции к которой привязан канал чатов
     * @return MessageResponse
     */
    public function sendMessage(string $accountUuid, Payload $payload, ?string $externalId = null): MessageResponse
    {
        $message = [
            'secret_key' => $this->getChannel()->getSecretKey(),
            'json' => [
                'event_type' => EventType::NEW_MESSAGE,
                'payload' => $payload->toApi(),
            ],
        ];

        if ($externalId !== null) {
            $source = ['source' => ['external_id' => $externalId]];
            $message['json']['payload'] = array_merge($source, $message['json']['payload']);
        }

        $response = $this->httpClient->post($this->getScopeId($accountUuid), $message);

        /** @var MessageResponse */
        return ResponseFactory::create($response, ActionType::MESSAGE);
    }

    /**
     * `Метод редактирования сообщения`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Отправка-редактирование-или-импорт-сообщения
     *
     * @param string $accountUuid GET /api/v4/account?with=amojo_id
     * @param Payload $payload Запрос модели
     * @return MessageResponse
     */
    public function editMessage(string $accountUuid, Payload $payload): MessageResponse
    {
        $response = $this->httpClient->post($this->getScopeId($accountUuid), [
            'secret_key' => $this->getChannel()->getSecretKey(),
            'json' => [
                'event_type' => EventType::EDIT_MESSAGE,
                'payload' => $payload->toApi(true),
            ],
        ]);

        /** @var MessageResponse */
        return ResponseFactory::create($response, ActionType::MESSAGE);
    }

    /**
     * `Метод обновления статуса доставки сообщения`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Обновление-статуса-доставки-сообщения
     *
     * @param string $accountUuid GET /api/v4/account?with=amojo_id
     * @param string $messageUuid Идентификатор сообщения на стороне API чатов.
     * Получаешь в вебхуке при отправленном исходящим сообщение.
     * Должно совпадать с msgid в URL
     * @param Deliver $deliver
     * @return EmptyResponse
     */
    public function deliverStatus(string $accountUuid, string $messageUuid, Deliver $deliver): EmptyResponse
    {
        $response = $this->httpClient->post(
            sprintf('%s/%s/%s', $this->getScopeId($accountUuid), $messageUuid, ActionType::DELIVERY_STATUS),
            [
                'secret_key' => $this->getChannel()->getSecretKey(),
                'json' => $deliver->toApi($messageUuid),
            ],
        );

        /** @var EmptyResponse */
        return ResponseFactory::create($response);
    }

    /**
     * `Метод получения истории сообщений по чату`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Получение-истории-сообщений-по-чату
     *
     * На текущий момент поддерживается только два GET-параметра:
     *
     * (int) `offset` - Оффсет выборки сообщений (сколько записей от начала выборки пропускаем)
     * (int) `limit` - Количество возвращаемых сущностей за один запрос (Максимум – 50)
     *
     * @param string $accountUuid GET /api/v4/account?with=amojo_id
     * @param string $conversationRefId ID чата в API чатов
     * @param array $query GET параметры
     * @return HistoryChatResponse
     */
    public function getHistoryChat(
        string $accountUuid,
        string $conversationRefId,
        array $query = []
    ): HistoryChatResponse {
        $uri = sprintf(
            '%s/%s/%s/%s',
            $this->getScopeId($accountUuid),
            ActionType::CHAT,
            $conversationRefId,
            ActionType::GET_HISTORY
        );

        $response = $this->httpClient->get($uri, [
            'secret_key' => $this->getChannel()->getSecretKey(),
            'query' => $query,
        ]);

        /** @var HistoryChatResponse */
        return ResponseFactory::create($response, ActionType::GET_HISTORY);
    }

    /**
     * `Метод передачи информации о печатание`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Передача-информации-о-печатание
     *
     * @param string $accountUuid GET /api/v4/account?with=amojo_id
     * @param Conversation $conversation ID чата на стороне интеграции
     * @param SenderInterface $sender ID пользователя на стороне интеграции
     * @return EmptyResponse
     */
    public function typing(string $accountUuid, Conversation $conversation, SenderInterface $sender): EmptyResponse
    {
        $response = $this->httpClient->post(sprintf('%s/%s', $this->getScopeId($accountUuid), ActionType::TYPING), [
            'secret_key' => $this->getChannel()->getSecretKey(),
            'json' => [
                'conversation_id' => $conversation->getId(),
                'sender' => $sender->toTyping(),
            ],
        ]);

        /** @var EmptyResponse */
        return ResponseFactory::create($response);
    }

    /**
     * `Метод отправки или снятия реакции`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Отправка-или-снятие-реакции
     *
     * @param string $accountUuid GET /api/v4/account?with=amojo_id
     * @param Conversation $conversation ID чата на стороне интеграции
     * @param SenderInterface $sender ID пользователя на стороне интеграции/в API чатов
     * @param MessageInterface $message ID сообщения на стороне интеграции/в API чатов
     * @param bool $type true = react | false = unreact
     * @param string|null $emoji Реакция
     * @return EmptyResponse
     */
    public function react(
        string $accountUuid,
        Conversation $conversation,
        SenderInterface $sender,
        MessageInterface $message,
        string $emoji = null,
        bool $type = true
    ): EmptyResponse {
        $response = $this->httpClient->post(
            sprintf('%s/%s', $this->getScopeId($accountUuid), ActionType::REACT),
            array_filter([
                'secret_key' => $this->getChannel()->getSecretKey(),
                'json' => array_filter([
                    'conversation_id' => $conversation->getId(),
                    'id' => $message->getRefUuid(),
                    'msgid' => $message->getUuid(),
                    'user' => $sender->toReact(),
                    'type' => $type ? 'react' : 'unreact',
                    'emoji' => $emoji,
                ])
            ])
        );

        /** @var EmptyResponse */
        return ResponseFactory::create($response);
    }

    /**
     * Возвращает объект канала чатов
     */
    public function getChannel(): Channel
    {
        return $this->channel;
    }

    private function getScopeId(string $accountUuid): string
    {
        $this->validateUuid($accountUuid);

        return sprintf('%s_%s', $this->getChannel()->getUuid(), $accountUuid);
    }

    private function validateUuid(string $value): void
    {
        if (!preg_match(self::UUID_PATTERN, $value)) {
            throw new AmoJoException(sprintf('Invalid UUID format for accountUuid: %s', $value));
        }
    }
}
