<?php

declare(strict_types=1);

namespace AmoJo\Client;

use AmoJo\DTO\AbstractResponse;
use AmoJo\DTO\ConnectResponse;
use AmoJo\DTO\CreateChatResponse;
use AmoJo\DTO\DeliveryResponse;
use AmoJo\DTO\DisconnectResponse;
use AmoJo\DTO\HistoryChatResponse;
use AmoJo\DTO\MessageResponse;
use AmoJo\DTO\ReactResponse;
use AmoJo\DTO\ResponseFactory;
use AmoJo\DTO\TypingResponse;
use AmoJo\Enum\ActionsType;
use AmoJo\Enum\EventType;
use AmoJo\Exception\AmoJoException;
use AmoJo\Exception\RequiredParametersMissingException;
use AmoJo\Models\Channel;
use AmoJo\Models\Conversation;
use AmoJo\Models\Deliver;
use AmoJo\Models\Payload;
use AmoJo\Models\Interfaces\MessageInterface;
use AmoJo\Models\Interfaces\SenderInterface;
use AmoJo\Models\Interfaces\UserInterface;

/**
 * AmoJoClient клиент для сервиса чатов amoCRM
 */
class AmoJoClient
{
    /** @var Channel */
    private Channel $channel;

    /** @var ApiGatewayInterface */
    private ApiGatewayInterface $gateway;

    /**
     * @param Channel $channel
     * @param array $additionalMiddleware массив с кастомными middleware implements \Middleware\MiddlewareInterface
     * @param string $segment ru | com
     */
    public function __construct(Channel $channel, array $additionalMiddleware = [], string $segment = 'ru')
    {
        $this->channel = $channel;
        $this->gateway = new AmoJoGateway($channel, $additionalMiddleware, $segment);
    }

    /**
     * `Метод подключение канала чатов к аккаунту.`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Подключение-канала-чата-в-аккаунте
     *
     * @param string $accountUid Получаем из запроса getCurrent(AccountModel::getAvailableWith())->getAmojoId()
     * | GET /api/v4/account?with=amojo_id
     * @param string|null $title Если свойство не передано, то тянется название канала указанное при регистрации
     * @param string $hookVersion На текущий момент актуальная версия v2
     * @return ConnectResponse
     */
    public function connect(string $accountUid, ?string $title = null, string $hookVersion = 'v2'): AbstractResponse
    {
        $this->validateUuid($accountUid, 'accountUid');

        $response = $this->gateway->request('POST', $this->channel->getUid() . ActionsType::CONNECT, [
            'account_id'       => $accountUid,
            'title'            => $title,
            'hook_api_version' => $hookVersion,
        ]);

        return ResponseFactory::create(ActionsType::CONNECT, $response);
    }

    /**
     * `Метод отключение канала чатов от аккаунта.`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Отключение-канала-чата-в-аккаунте
     *
     * @param string $accountUid Получаем из запроса getCurrent(AccountModel::getAvailableWith())->getAmojoId()
     * | GET /api/v4/account?with=amojo_id
     * @return DisconnectResponse
     */
    public function disconnect(string $accountUid): AbstractResponse
    {
        $this->validateUuid($accountUid, 'accountUid');

        $response = $this->gateway->request('DELETE', $this->channel->getUid() . ActionsType::DISCONNECT, [
            'account_id' => $accountUid,
        ]);

        return ResponseFactory::create(ActionsType::DISCONNECT, $response);
    }

    /**
     * `Метод создание чата`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Создание-нового-чата
     *
     * @param string $scopeId Строится из ChannelUid + AccountUid.
     * Возвращается при подключении канала ConnectResponse->getScopeId()
     * @param Conversation $conversation Передается ID чата на стороне интеграции
     * @param UserInterface $contact Модель контакта со свойствами на стороне интеграции
     * @param string|null $externalId Источник https://www.amocrm.ru/developers/content/crm_platform/sources-api
     * - Важно: источник должен иметь параметр origin_code со значением кода канала чатов
     * - Важно: источник должен быть создан от интеграции к которой привязан канал чатов
     * @return CreateChatResponse
     */
    public function createChat(
        string $scopeId,
        Conversation $conversation,
        UserInterface $contact,
        ?string $externalId = null
    ): AbstractResponse {

        if ($conversation->getId() === null) {
            throw new RequiredParametersMissingException(
                'To create a chat, you need the chat ID on the integration side.'
            );
        }

        $socialProfile = [
            'conversation_id' => $conversation->getId(),
            'source'          => ['external_id' => $externalId],
            'user'            => $contact->toPayload(),
        ];

        if ($externalId === null) {
            unset($socialProfile['source']);
        }

        $response = $this->gateway->request('POST', $scopeId . ActionsType::CHAT, $socialProfile);
        return ResponseFactory::create(ActionsType::CHAT, $response);
    }

    /**
     * `Метод импорта сообщения`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Отправка-редактирование-или-импорт-сообщения
     *
     * @param string $scopeId Строится из ChannelUid + AccountUid.
     *  Возвращается при подключении канала ConnectResponse->getScopeId()
     * @param Payload $payload Запрос модели
     * @param string|null $externalId Источник https://www.amocrm.ru/developers/content/crm_platform/sources-api
     * - Важно: источник должен иметь параметр origin_code со значением кода канала чатов
     * - Важно: источник должен быть создан от интеграции к которой привязан канал чатов
     * @return MessageResponse
     */
    public function sendMessage(string $scopeId, Payload $payload, ?string $externalId = null): AbstractResponse
    {
        $message = [
            'event_type' => EventType::NEW_MESSAGE,
            'payload'    => $payload->toApi(),
        ];

        if ($externalId !== null) {
            $source = ['source' => ['external_id' => $externalId]];
            $message['payload'] = array_merge($source, $message['payload']);
        }

        $response = $this->gateway->request('POST', $scopeId, $message);

        return ResponseFactory::create(ActionsType::MESSAGE, $response);
    }

    /**
     * `Метод редактирования сообщения`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Отправка-редактирование-или-импорт-сообщения
     *
     * @param string $scopeId Строится из ChannelUid + AccountUid.
     *  Возвращается при подключении канала ConnectResponse->getScopeId()
     * @param Payload $payload Запрос модели
     * @return MessageResponse
     */
    public function editMessage(string $scopeId, Payload $payload): AbstractResponse
    {
        $response = $this->gateway->request('POST', $scopeId, [
            'event_type' => EventType::EDIT_MESSAGE,
            'payload'    => $payload->toApi(true),
        ]);

        return ResponseFactory::create(ActionsType::MESSAGE, $response);
    }

    /**
     * `Метод обновления статуса доставки сообщения`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Обновление-статуса-доставки-сообщения
     *
     * @param string $scopeId Строится из ChannelUid + AccountUid.
     *  Возвращается при подключении канала ConnectResponse->getScopeId()
     * @param string $messageUid Идентификатор сообщения на стороне API чатов.
     * Получаешь в вебхуке при отправленном исходящим сообщение.
     * Должно совпадать с msgid в URL
     * @param Deliver $deliver
     * @return DeliveryResponse
     */
    public function deliverStatus(string $scopeId, string $messageUid, Deliver $deliver): AbstractResponse
    {
        $uri = $scopeId . '/' . $messageUid . ActionsType::DELIVERY_STATUS;
        $response = $this->gateway->request('POST', $uri, $deliver->toApi($messageUid));

        return ResponseFactory::create(ActionsType::DELIVERY_STATUS, $response);
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
     * @param string $scopeId Строится из ChannelUid + AccountUid.
     *  Возвращается при подключении канала ConnectResponse->getScopeId()
     * @param string $conversationRefId ID чата в API чатов
     * @param array $query GET параметры
     * @return HistoryChatResponse
     */
    public function getHistoryChat(string $scopeId, string $conversationRefId, array $query = []): AbstractResponse
    {
        $uri = $scopeId . ActionsType::CHAT . '/' . $conversationRefId . ActionsType::GET_HISTORY;
        $response = $this->gateway->request('GET', $uri, [], $query);

        return ResponseFactory::create(ActionsType::GET_HISTORY, $response);
    }

    /**
     * `Метод передачи информации о печатание`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Передача-информации-о-печатание
     *
     * @param string $scopeId Строится из ChannelUid + AccountUid.
     *  Возвращается при подключении канала ConnectResponse->getScopeId()
     * @param Conversation $conversation ID чата на стороне интеграции
     * @param SenderInterface $sender ID пользователя на стороне интеграции
     * @return TypingResponse
     */
    public function typing(string $scopeId, Conversation $conversation, SenderInterface $sender): AbstractResponse
    {
        $response = $this->gateway->request('POST', $scopeId . ActionsType::TYPING, [
            'conversation_id' => $conversation->getId(),
            'sender'          => $sender->toTyping()
        ]);

        return ResponseFactory::create(ActionsType::TYPING, $response);
    }

    /**
     * `Метод отправки или снятия реакции`
     * @link https://www.amocrm.ru/developers/content/chats/chat-api-reference#Отправка-или-снятие-реакции
     *
     * @param string $scopeId Строится из ChannelUid + AccountUid.
     *  Возвращается при подключении канала ConnectResponse->getScopeId()
     * @param Conversation $conversation ID чата на стороне интеграции
     * @param SenderInterface $sender ID пользователя на стороне интеграции/в API чатов
     * @param MessageInterface $message ID сообщения на стороне интеграции/в API чатов
     * @param bool $type true = react | false =unreact
     * @param string|null $emoji Реакция
     * @return ReactResponse
     */
    public function react(
        string $scopeId,
        Conversation $conversation,
        SenderInterface $sender,
        MessageInterface $message,
        string $emoji = null,
        bool $type = true
    ): AbstractResponse {
        $response = $this->gateway->request('POST', $scopeId . ActionsType::REACT, array_filter([
            'conversation_id' => $conversation->getId(),
            'id'              => $message->getRefUid(),
            'msgid'           => $message->getUid(),
            'user'            => $sender->toReact(),
            'type'            => $type ? 'react' : 'unreact',
            'emoji'           => $emoji
        ]));

        return ResponseFactory::create(ActionsType::REACT, $response);
    }

    /**
     * @param string $value
     * @param string $paramName
     * @return void
     */
    private function validateUuid(string $value, string $paramName): void
    {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value)) {
            throw new AmoJoException("Invalid UUID format for {$paramName}: {$value}");
        }
    }
}
