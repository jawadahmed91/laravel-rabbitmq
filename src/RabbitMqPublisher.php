<?php

namespace JawadAhmed\LaravelRabbitMq;

use JsonException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

final class RabbitMqPublisher
{
    public function __construct(private readonly AMQPChannel $channel) {}

    /** @param array<string, mixed> $payload */
    public function publish(
        string $exchange,
        string $routingKey,
        array $payload,
        string $exchangeType = 'topic',
        ?string $correlationId = null,
    ): void {
        $this->channel->exchange_declare($exchange, $exchangeType, false, true, false);

        $message = new AMQPMessage(
            json_encode($payload, JSON_THROW_ON_ERROR),
            array_filter([
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'correlation_id' => $correlationId,
            ], static fn (mixed $value): bool => $value !== null),
        );

        $this->channel->basic_publish($message, $exchange, $routingKey);
    }

    public function close(): void
    {
        $connection = $this->channel->getConnection();
        $this->channel->close();
        $connection->close();
    }
}
