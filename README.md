# Laravel RabbitMQ

A focused Laravel 13 example for publishing persistent JSON messages to RabbitMQ exchanges.

## What it demonstrates

- Laravel service-provider configuration
- Durable exchange and queue declarations
- Direct, topic, fanout, and headers exchange support
- Persistent JSON messages with correlation IDs
- Dependency-injected `RabbitMqPublisher`
- PHPUnit tests without a live broker

## Install and test

```bash
composer install
composer test
```

Set `RABBITMQ_HOST`, `RABBITMQ_PORT`, `RABBITMQ_USER`, `RABBITMQ_PASSWORD`, and `RABBITMQ_VHOST` in the consuming Laravel application's environment. Never commit real broker credentials.

```php
use JawadAhmed\LaravelRabbitMq\RabbitMqPublisher;

$publisher->publish(
    exchange: 'orders',
    routingKey: 'order.created',
    payload: ['order_id' => 123],
);
```

Laravel 13 requires PHP 8.3 or newer.
