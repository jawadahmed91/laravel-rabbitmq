<?php

namespace Tests;

use JawadAhmed\LaravelRabbitMq\RabbitMqPublisher;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

final class RabbitMqPublisherTest extends TestCase
{
    public function test_it_publishes_persistent_json(): void
    {
        $channel = $this->createMock(AMQPChannel::class);
        $channel->expects(self::once())->method('exchange_declare')
            ->with('orders', 'topic', false, true, false);
        $channel->expects(self::once())->method('basic_publish')
            ->with(self::callback(function (AMQPMessage $message): bool {
                self::assertSame('{"order_id":123}', $message->getBody());
                self::assertSame('application/json', $message->get('content_type'));
                self::assertSame(AMQPMessage::DELIVERY_MODE_PERSISTENT, $message->get('delivery_mode'));
                return true;
            }), 'orders', 'order.created');

        (new RabbitMqPublisher($channel))->publish('orders', 'order.created', ['order_id' => 123]);
    }
}
