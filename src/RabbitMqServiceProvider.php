<?php

namespace JawadAhmed\LaravelRabbitMq;

use Illuminate\Support\ServiceProvider;
use PhpAmqpLib\Connection\AMQPStreamConnection;

final class RabbitMqServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rabbitmq.php', 'rabbitmq');

        $this->app->singleton(AMQPStreamConnection::class, fn () => new AMQPStreamConnection(
            host: (string) config('rabbitmq.host'),
            port: (int) config('rabbitmq.port'),
            user: (string) config('rabbitmq.user'),
            password: (string) config('rabbitmq.password'),
            vhost: (string) config('rabbitmq.vhost'),
            connection_timeout: (float) config('rabbitmq.connection_timeout'),
        ));

        $this->app->singleton(RabbitMqPublisher::class, fn ($app) => new RabbitMqPublisher(
            $app->make(AMQPStreamConnection::class)->channel(),
        ));
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/rabbitmq.php' => config_path('rabbitmq.php'),
        ], 'rabbitmq-config');
    }
}
