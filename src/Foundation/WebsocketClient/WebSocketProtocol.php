<?php

namespace Core\Foundation\WebsocketClient;

use Core\Foundation\WebsocketClient\Exceptions\BadOperationCodeException;
use Core\Foundation\WebsocketClient\Exceptions\BadUrlException;
use Core\Foundation\WebsocketClient\Exceptions\ConnectionException;
use Core\Foundation\WebsocketClient\Exceptions\WebSocketProtocolException;
use Core\Foundation\WebsocketClient\Exceptions\SocketException;
use Exception;

class WebSocketProtocol extends Socket
{
    /** @var  bool  Connection state flag. */
    private $isConnectionOpened;

    /** @var  string  Address of current connection. Actual when connection is opened. */
    private $currentConnection;

    /** @var  integer  Size of frame to interact with server. */
    private $frameSize;

    /** @var  bool  Socket closing flag. */
    private $isClosing;

    /** @var  integer  Last operation status code. */
    private $lastOpCode;

    /** @var  string|null  Container for payload frames loading. */
    protected $payloadContainer;

    /** @var  integer  Socket connection close code. */
    protected $closeStatus;

    private const DEFAULT_HEADER_STARTER = 'GET %s HTTP/1.1';

    private const DEFAULT_HEADERS_TEMPLATE = [
        'host' => '',
        'user-agent' => 'websocket-client-php',
        'connection' => 'Upgrade',
        'upgrade' => 'websocket',
        'sec-websocket-key' => '',
        'sec-websocket-version' => '13',
    ];

    private const KEY = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    private const CLOSE_CODE = 'close';

    private const DEFAULT_OPERATION = 'text';

    private const CONTINUATION_CODE = 'continuation';

    public const OPERATION_CODES = [
        'continuation' => 0,
        'text' => 1,
        'binary' => 2,
        'close' => 8,
        'ping' => 9,
        'pong' => 10,
    ];

    /**
     * Establish connection and perform handshake.
     *
     * @param string $uri
     * @param array|null $headers
     * @param integer $socketTimeOut
     * @param integer $frameSize
     * @param resource|null $context
     *
     * @return  void
     *
     * @throws  BadUrlException
     * @throws  SocketException
     * @throws  WebSocketProtocolException
     * @throws Exception
     */
    protected function openConnection($uri, $headers = [], $socketTimeOut = 5, $frameSize = 4096, $context = null): void
    {
        if ($this->isConnectionOpened) {
            throw new WebSocketProtocolException('Connection already opened. Close current connection first.');
        }

        [$remoteAddress, $fullUrl, $host, $auth] = $this->parseUrl($uri);

        $this->openSocket($remoteAddress, $socketTimeOut, $context);

        $key = $this->generateKey();

        $header = $this->makeHeader($fullUrl, $key, $host, $auth, $headers);

        $this->writeToSocket($header);

        $response = $this->readLineFromSocket();

        if (!$this->validateHandshakeResponse($response, $key)) {
            throw new WebSocketProtocolException("Server sent invalid upgrade response.\r\n{$response}");
        }

        $this->currentConnection = $uri;
        $this->frameSize = $frameSize;
        $this->isConnectionOpened = true;
    }

    /**
     * Parse url and make socket address and headers for basic authentication.
     *
     * @param string $url
     *
     * @return  array
     *
     * @throws  BadUrlException
     */
    private function parseUrl($url): array
    {
        if (!$urlParts = parse_url($url)) {
            throw new BadUrlException("Can not parse URL: {$url}");
        }

        $scheme = $urlParts['scheme'] ?? null;
        if (!$scheme) {
            throw new BadUrlException("Url should have scheme ws or wss specified in URI '{$url}'");
        }

        if ($scheme !== 'ws' && $scheme !== 'wss') {
            throw new BadUrlException("Url should have scheme ws or wss, not '{$scheme}' from URI '{$url}'");
        }

        $scheme = $scheme === 'wss' ? 'ssl' : 'tcp';

        $host = $urlParts['host'] ?? null;

        if (!$host) {
            throw new BadUrlException("Can not parse host address from URI '{$url}'");
        }

        $port = $urlParts['port'] ?? ($scheme === 'wss' ? 443 : 80);
        $path = $urlParts['path'] ?? '/';
        $query = $urlParts['query'] ?? null;
        $fragment = $urlParts['fragment'] ?? null;
        $user = $urlParts['user'] ?? null;
        $pass = $urlParts['pass'] ?? null;

        return [
            // remote socket address
            "{$scheme}://{$host}:{$port}",
            // full request path
            $path . ($fragment ? "#{$fragment}" : '') . ($query ? "?{$query}" : ''),
            // host:port
            "{$host}:{$port}",
            // basic auth if user and password given
            ($user && $pass) ? "{$user}:{$pass}" : null,
        ];
    }

    /**
     * Make header for connection.
     *
     * @param string $fullUrl
     * @param string $key
     * @param string $host
     * @param string $auth
     * @param array $additionalHeaders
     *
     * @return  string
     */
    private function makeHeader($fullUrl, $key, $host, $auth, $additionalHeaders): string
    {
        $headers = self::DEFAULT_HEADERS_TEMPLATE;
        $headers['host'] = $host;
        $headers['sec-websocket-key'] = $key;
        if ($auth) {
            $headers['authorization'] = 'Basic ' . base64_encode($auth);
        }
        if (!empty($additionalHeaders)) {
            $headers = array_merge($headers, $additionalHeaders);
        }

        $header = sprintf(self::DEFAULT_HEADER_STARTER, $fullUrl) . "\r\n";
        foreach ($headers as $headerKey => $headerValue) {
            $header .= "{$headerKey}: {$headerValue}\r\n";
        }
        $header .= "\r\n\r\n";

        return $header;
    }

    /**
     * Generate a random string for WebSocket key.
     *
     * @return  string
     *
     * @throws Exception
     */
    private function generateKey(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"$&/()=[]{}0123456789';
        $key = '';
        $chars_length = strlen($chars);
        for ($i = 0; $i < 16; $i++) {
            $key .= $chars[random_int(0, $chars_length - 1)];
        }

        return base64_encode($key);
    }

    /**
     * Validate upgrade response from server.
     *
     * @param string $response
     * @param string $key
     *
     * @return  bool
     *
     * @throws  WebSocketProtocolException
     */
    private function validateHandshakeResponse($response, $key): bool
    {
        $match = preg_match('#Sec-WebSocket-Accept:\s(.*)$#mUi', $response, $matches);
        if ($match === false) {
            throw new WebSocketProtocolException("Some error occurred while checking server upgrade response:\r\n{$response}");
        }
        if ($match === 0) {
            return false;
        }

        $accept = trim($matches[1]);
        $expected = base64_encode(pack('H*', sha1($key . self::KEY)));

        return $accept === $expected;
    }

    /**
     * Set new frame size.
     *
     * @param integer $frameSize
     *
     * @return  void
     */
    public function setFrameSize($frameSize): void
    {
        $this->frameSize = $frameSize;
    }

    /**
     * Make TTFN and close the connection.
     *
     * @param integer $status @see http://tools.ietf.org/html/rfc6455#section-7.4
     * @param string $message A closing message, max 125 bytes.
     *
     * @return  mixed
     *
     * @throws
     */
    protected function closeConnection($status = 1000, $message = 'ttfn')
    {
        if (!$this->isConnectionOpened) {
            return null;
        }

        $binStatus = sprintf('%016b', $status);

        $payLoad = '';

        foreach (str_split($binStatus, 8) as $bin) {
            $payLoad .= chr(bindec($bin));
        }

        $payLoad .= $message;

        try {
            $this->sendToConnection($payLoad, self::CLOSE_CODE);

            $this->isClosing = true;

            // Receiving a close frame will actually close the socket.
            return $this->receiveFromConnection();

        } catch (Exception $e) {
            throw new ConnectionException('Can not close connection. It seems to be already closed.');
        }
    }

    /**
     * Send payload to socket.
     *
     * @param string $payload
     * @param string $operationCode
     * @param bool $mask
     *
     * @return  void
     *
     * @throws  BadOperationCodeException
     * @throws  SocketException
     * @throws  ConnectionException
     */
    protected function sendToConnection($payload, $operationCode = self::DEFAULT_OPERATION, $mask = true): void
    {
        if (!$this->isConnectionOpened) {
            throw new ConnectionException('It seems connection is closed.');
        }

        if (!$this->isOperationCodeCorrect($operationCode)) {
            throw new BadOperationCodeException("Bad operation code '{$operationCode}'. Try 'text' or 'binary'.");
        }

        $frames = str_split($payload, $this->frameSize);
        $lastFrameIndex = count($frames) - 1;

        foreach ($frames as $index => $frame) {
            $this->sendFrameToSocket($frame, $index === $lastFrameIndex, $operationCode, $mask);

            // all frames except first must be marked a continuation
            $operationCode = self::CONTINUATION_CODE;
        }
    }

    /**
     * Check if operation code is correct.
     *
     * @param string $operationCode
     *
     * @return  bool
     */
    private function isOperationCodeCorrect($operationCode): bool
    {
        return array_key_exists($operationCode, self::OPERATION_CODES);
    }

    /**
     * Prepare and send frame immediately to socket.
     *
     * @param string $payload
     * @param bool $isFinalFrame
     * @param string $operationCode
     * @param bool $isMasked
     *
     * @return  void
     *
     * @throws  SocketException
     * @throws Exception
     */
    private function sendFrameToSocket($payload, $isFinalFrame, $operationCode, $isMasked): void
    {
        $mask = $isMasked ? $this->generateMask() : null;

        $frame = $this->makeFrameHeader($isFinalFrame, $mask, $operationCode, strlen($payload));

        $frame .= $this->encodePayload($payload, $mask);

        $this->writeToSocket($frame);
    }

    /**
     * Generate random mask.
     *
     * @return  string
     *
     * @throws Exception
     */
    private function generateMask(): string
    {
        $mask = '';
        for ($i = 0; $i < 4; $i++) {
            $mask .= chr(random_int(0, 255));
        }

        return $mask;
    }

    /**
     * Make frame header.
     *
     * @see  https://tools.ietf.org/html/rfc6455#section-5.2
     *
     * @param bool $isFinal
     * @param string $mask
     * @param string $operationCode
     * @param integer $payloadLength
     *
     * @return  string
     */
    private function makeFrameHeader($isFinal, $mask, $operationCode, $payloadLength): string
    {
        $header = '';

        // [0] frame-fin - 1 bit in length
        $header .= $isFinal ? '1' : '0';

        // [1] frame-rsv1 - 1 bit in length
        // [2] frame-rsv2 - 1 bit in length
        // [3] frame-rsv3 - 1 bit in length
        $header .= '000';

        // [4 - 7] frame-opcode - 4 bits in length
        $header .= sprintf('%04b', self::OPERATION_CODES[$operationCode]);

        // [8] frame-masked - 1 bit in length
        $header .= $mask ? '1' : '0';

        // frame-payload-length
        // [9 - 9+7]
        // [9 - 9+7+16]
        // [9 - 9+7+64]
        if ($payloadLength < 126) {
            $header .= sprintf('%07b', $payloadLength);
        } elseif ($payloadLength < 65536) {
            $header .= decbin(126);
            $header .= sprintf('%016b', $payloadLength);
        } else {
            $header .= decbin(127);
            $header .= sprintf('%064b', $payloadLength);
        }

        $encoded = '';

        // frame-masking-key
        foreach (str_split($header, 8) as $bin) {
            $encoded .= chr(bindec($bin));
        }

        if ($mask) {
            $encoded .= $mask;
        }

        return $encoded;
    }

    /**
     * Encode payload if mask is given.
     *
     * @param string $payload
     * @param string|null $mask
     *
     * @return  string
     */
    private function encodePayload($payload, $mask): string
    {
        if (!$mask) {
            return $payload;
        }

        $encoded = '';
        $length = strlen($payload);

        for ($i = 0; $i < $length; $i++) {
            $encoded .= $payload[$i] ^ $mask[$i % 4];
        }

        return $encoded;
    }

    /**
     * Receive payload from socket.
     *
     * @return  bool|null|string
     *
     * @throws  BadOperationCodeException
     * @throws  ConnectionException
     * @throws  SocketException
     */
    protected function receiveFromConnection()
    {
        if (!$this->isConnectionOpened) {
            throw new ConnectionException('It seems connection is closed.');
        }

        $this->payloadContainer = '';
        $this->lastOpCode = null;
        $response = null;

        while ($response === null) {
            $response = $this->receiveFrameFromConnection();
        }

        return $response;
    }


    /**
     * @return bool|null|string
     * @throws BadOperationCodeException
     * @throws ConnectionException
     * @throws SocketException
     */
    private function receiveFrameFromConnection()
    {

        // Read the main fragment information
        $buffer = $this->readFromSocket(2);

        // [0] frame-fin - 1 bit in length
        $isFinalFrame = (bool)(ord($buffer[0]) & 1 << 7);

        // [1] frame-rsv1 - 1 bit in length
        // [2] frame-rsv2 - 1 bit in length
        // [3] frame-rsv3 - 1 bit in length
        // $rsv  = (bool) (ord($buffer[0]) & (1<<6 || 1<<5 || 1<<4));

        // [4 - 7] frame-opcode - 4 bits in length
        $operationCodeId = ord($buffer[0]) & 31;
        if (!$operationCode = $this->getOperationCodeById($operationCodeId)) {
            throw new BadOperationCodeException("Bad opcode in received frame: {$operationCodeId}");
        }

        if ($operationCode !== self::CONTINUATION_CODE) {
            $this->lastOpCode = $operationCode;
        }

        // [8] frame-masked - 1 bit in length
        $isMasked = (bool)(ord($buffer[1]) >> 7);

        // frame-payload-length
        // [9 - 9+7]
        // [9 - 9+7+16]
        // [9 - 9+7+64]
        $payloadLength = (integer)ord($buffer[1]) & 127;
        if ($payloadLength === 126) {
            $buffer = $this->readFromSocket(2);
            $payloadLength = bindec($this->stringToBinaryString($buffer));
        } elseif ($payloadLength === 127) {
            $buffer = $this->readFromSocket(8);
            $payloadLength = bindec($this->stringToBinaryString($buffer));
        }

        // frame-masking-key
        $mask = $isMasked ? $this->readFromSocket(4) : null;

        $payload = '';

        // Read payload
        if ($payloadLength > 0) {
            $buffer = $this->readFromSocket($payloadLength);
            $payload = $this->decodePayload($buffer, $mask);
        }

        // Check if closing frame
        if ($operationCode === self::CLOSE_CODE) {
            if ($payloadLength > 1) {
                $status_bin = $payload[0] . $payload[1];
                $status = bindec(sprintf('%08b%08b', ord($payload[0]), ord($payload[1])));
                $this->closeStatus = $status;
                // $payload = substr($payload, 2);
                if (!$this->isClosing) {
                    $this->sendToConnection($status_bin . 'Close acknowledged: ' . $status, self::CLOSE_CODE);
                }
            }

            // Close the socket.
            $this->isConnectionOpened = false;
            $this->closeSocket();
            $this->isClosing = false;

            return true;
        }

        $this->payloadContainer .= $payload;

        if (!$isFinalFrame) {
            return null;
        }

        if ($this->payloadContainer) {
            $payload = $this->payloadContainer;
            $this->payloadContainer = null;
        }

        return $payload;
    }

    /**
     * Get operation code by it's id.
     *
     * @param integer $operationCodeId
     *
     * @return  string|null
     */
    private function getOperationCodeById($operationCodeId): ?string
    {
        $operationCode = array_search($operationCodeId, self::OPERATION_CODES, true);

        return $operationCode === false ? null : $operationCode;
    }

    /**
     * Convert a binary to a string of '0' and '1'.
     *
     * @param string $toBinary
     *
     * @return  string
     */
    private function stringToBinaryString($toBinary): string
    {
        $string = '';
        $length = strlen($toBinary);

        for ($i = 0; $i < $length; $i++) {
            $string .= sprintf('%08b', ord($toBinary[$i]));
        }

        return $string;
    }

    /**
     * Decode payload if mask is given.
     *
     * @param string $payload
     * @param string|null $mask
     *
     * @return  string
     */
    private function decodePayload($payload, $mask): string
    {
        if (!$mask) {
            return $payload;
        }

        $decoded = '';
        $length = strlen($payload);

        for ($i = 0; $i < $length; $i++) {
            $decoded .= ($payload[$i] ^ $mask[$i % 4]);
        }

        return $decoded;
    }

    /**
     * Get socket connection state.
     *
     * @return  bool
     */
    public function isConnected(): bool
    {
        return $this->isConnectionOpened ?: false;
    }
}