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

## Add this to an existing Laravel project

1. Install the AMQP client with `composer require php-amqplib/php-amqplib`.
2. Copy `src/RabbitMqPublisher.php` to `app/Messaging/RabbitMqPublisher.php` and change its namespace to `App\Messaging`.
3. Copy `config/rabbitmq.php` into your application's `config` directory.
4. Add the `RABBITMQ_*` values shown above to `.env`. Use a restricted RabbitMQ user and a dedicated virtual host in production.
5. Copy the bindings from `RabbitMqServiceProvider` into an application service provider, updating the class namespace, or adapt and register that provider in `bootstrap/providers.php`.
6. Inject `RabbitMqPublisher` into the service that owns the business operation.
7. Publish only after the related database transaction commits. Supply a stable exchange, routing key, JSON-safe payload, and correlation ID.

```php
$publisher->publish('orders', 'order.created', [
    'order_id' => $order->id,
], correlationId: (string) $order->id);
```

8. Declare and bind the consumer queue in your RabbitMQ infrastructure or consumer application using the same exchange and routing pattern.
9. Make consumers idempotent and acknowledge a message only after successful processing. Route repeatedly failing messages to a dead-letter queue.
10. Copy `tests/RabbitMqPublisherTest.php` into your project, update its namespace and imports, and run `php artisan test` without a live broker.
11. Perform one integration test against a development broker and confirm the exchange, routing key, payload, persistence flag, and correlation ID.
