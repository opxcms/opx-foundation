<?php

namespace Core\Foundation\SocketBroadcaster;


use Core\Foundation\WebsocketClient\Exceptions\SocketException;
use Core\Foundation\WebsocketClient\WebsocketClient;
use Exception;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use JsonException;

class SocketBroadcaster extends Broadcaster
{
    protected $app;
    protected $config;

    /** @var WebsocketClient */
    protected $socketClient;

    public function __construct($app, $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function auth($request)
    {
        // TODO: Implement auth() method.
    }

    /**
     * Return the valid authentication response.
     *
     * @param Request $request
     * @param mixed $result
     *
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result)
    {
        // TODO: Implement validAuthenticationResponse() method.
    }

    /**
     * Broadcast the given event.
     *
     * @param array $channels
     * @param string $event
     * @param array $payload
     *
     * @return  void
     *
     * @throws  JsonException
     * @throws Exception
     */
    public function broadcast(array $channels, $event, array $payload = []): void
    {
        $connection = $this->socketClient();

        $payload = [
            'event' => $event,
            'data' => $payload,
            'socket' => Arr::pull($payload, 'socket'),
        ];

        foreach ($this->formatChannels($channels) as $channel) {
            $connection->send(['channel' => $channel, 'payload' => $payload]);
        }
    }

    /**
     * Return connected websocket client.
     *
     * @return WebsocketClient
     *
     * @throws Exception
     */
    protected function socketClient(): WebsocketClient
    {
        if (!$this->socketClient) {
            $server = $this->config['server'] ?? 'ws://' . $_SERVER['SERVER_NAME'];
            $port = $this->config['port'] ?? '49123';

            $this->socketClient = new WebsocketClient("{$server}:{$port}");
        }

        if (!$this->socketClient->isConnected()) {
            $this->socketClient->connect();
        }

        return $this->socketClient;
    }
}