<?php

namespace Core\Foundation\WebsocketClient;


use Core\Foundation\WebsocketClient\Exceptions\SocketException;
use Core\Foundation\WebsocketClient\Exceptions\ConnectionException;
use Core\Foundation\WebsocketClient\Exceptions\BadUrlException;
use Core\Foundation\WebsocketClient\Exceptions\WebSocketProtocolException;
use Core\Foundation\WebsocketClient\Exceptions\BadOperationCodeException;
use Exception;
use JsonException;

class WebsocketClient extends WebSocketProtocol
{
    /** @var  string  Address to connect. */
    protected $socketAddress;

    /** @var  array  Additional headers to send via socket. */
    protected $headers;

    /** @var  string  Last error message. */
    protected $lastError;

    /**
     * Constructor.
     *
     * @param  string  $uri  A ws/wss-URI
     * @param  array  $headers
     * @param  integer  $socketTimeOut
     * @param  integer  $frameSize
     * @param  resource  $context
     *
     * @return  void
     *
     * @throws  SocketException
     */
    public function __construct($uri, $headers = [], $socketTimeOut = 5, $frameSize = 4096, $context = null) {
        $this->socketAddress = $uri;
        $this->headers = $headers;
        $this->setFrameSize($frameSize);
        $this->setSocketTimeout($socketTimeOut);
        $this->setContext($context);
    }

    /**
     * Destructor.
     *
     * @return  void
     *
     */
    public function __destruct() {
        $this->disconnect();
    }

    /**
     * Open connection to server.
     *
     * @return  boolean
     * @throws Exception
     */
    public function connect(): bool
    {
        try {
            $this->openConnection($this->socketAddress);
        } catch (BadUrlException | WebSocketProtocolException | SocketException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Disconnect from server.
     *
     * @return  boolean
     */
    public function disconnect(): bool
    {
        try {
            $this->closeConnection();
        } catch (ConnectionException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Send data to server.
     *
     * @param mixed $payload
     *
     * @return  boolean
     * @throws JsonException
     */
    public function send($payload): bool
    {
        // You can perform type conversions here
        if(is_array($payload)) {
            $payload = json_encode($payload, JSON_THROW_ON_ERROR);
        }

        try {
            $this->sendToConnection($payload);
        } catch (ConnectionException | SocketException | BadOperationCodeException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Receive data from server.
     *
     * @return  mixed|null
     */
    public function receive()
    {
        try {
            $received = $this->receiveFromConnection();
        } catch (BadOperationCodeException | ConnectionException | SocketException $e) {
            $this->lastError = $e->getMessage();
            return null;
        }

        // You can perform type conversions here

        return $received;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}