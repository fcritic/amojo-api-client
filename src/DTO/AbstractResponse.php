<?php

declare(strict_types=1);

namespace AmoJo\DTO;

/**
 * Абстрактный класс для наследования всех DTO ответов
 */
abstract class AbstractResponse
{
    /** @var array массив данных из запроса */
    protected array $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Отдает ответ в форме массива
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
