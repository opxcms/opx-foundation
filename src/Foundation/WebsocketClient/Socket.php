<?php

namespace Core\Foundation\WebsocketClient;

use Core\Foundation\WebsocketClient\Exceptions\SocketException;

class Socket
{
    /** @var  resource  Opened socket handler. */
    private $socket;

    /** @var  string  Socket address. */
    private $remote_socket;

    /** @var  integer  Socket timeout. */
    private $timeout;

    /** @var  resource  Socket context. */
    private $context;

    private const STREAM_CONTEXT = 'stream-context';

    /**
     * Open socket.
     *
     * @param  string  $remote_socket
     * @param  integer|null  $timeout
     * @param  resource|null  $context
     *
     * @return  void
     *
     * @throws  SocketException
     */
    protected function openSocket($remote_socket, $timeout = null, $context = null)
    {
        $this->remote_socket = $remote_socket;

        $this->timeout = $this->timeout ?: ($timeout ?: (ini_get("default_socket_timeout") ?: 5));

        if($context) {
            if(! $this->checkContext($context)) {
                throw new SocketException("Error. Wrong context given.");
            }
            $this->context = $context;
        } else {
            $this->context = stream_context_create();
        }

        $this->socket = @stream_socket_client($remote_socket, $errorNumber, $errorString, $this->timeout, STREAM_CLIENT_CONNECT, $this->context);

        if(! $this->socket) {
            throw new SocketException("Error connecting to socket '{$this->remote_socket}'. {$errorString} ({$errorNumber})");
        }
    }

    /**
     * Check context for compatibility.
     *
     * @param  resource  $context
     *
     * @return  boolean
     */
    protected function checkContext($context)
    {
        return get_resource_type($context) === static::STREAM_CONTEXT;
    }

    /**
     * Set new context.
     *
     * @param  resource  $context
     *
     * @throws  SocketException
     */
    protected function setContext($context)
    {
        if($context && !$this->checkContext($context)) {
            throw new SocketException("Error. Wrong context given.");
        }

        $this->context = $context;
    }

    /**
     * Set new timeout for socket.
     *
     * @param  $timeout
     *
     * @return  void
     *
     * @throws  SocketException
     */
    protected function setSocketTimeout($timeout)
    {
        $this->timeout = $timeout;

        if($this->socket) {
            if(! stream_set_timeout($this->socket, $timeout)) {
                throw new SocketException("Can not set timeout for socket.");
            }
        }
    }

    /**
     * Read data from socket.
     *
     * @param  integer  $bytesToRead
     *
     * @return  string
     *
     * @throws  SocketException
     */
    protected function readFromSocket($bytesToRead) {
        $received = '';
        $receivedLength = 0;

        while ($receivedLength < $bytesToRead) {
            $buffer = fread($this->socket, $bytesToRead - $receivedLength);

            if ($buffer === false || $buffer === '') {
                $metadata = json_encode(stream_get_meta_data($this->socket));
                throw new SocketException(
                    $buffer === false
                        ? "Error reading {$bytesToRead} bytes from socket. Read only {$receivedLength} bytes. Socket state:\r\n{$metadata}"
                        : "Empty read. Connection dead? Stream state:\r\n{$metadata}"
                );
            }

            $received .= $buffer;
            $receivedLength += strlen($buffer);
        }

        return $received;
    }

    /**
     * Read line from socket.
     *
     * @return  string
     *
     * @throws  SocketException
     */
    protected function readLineFromSocket()
    {
        $read = stream_get_line($this->socket, 4096, "\r\n\r\n");

        if($read === false) {
            throw new SocketException("Can not read line from socket.");
        }

        return $read;
    }

    /**
     * Write data to socket.
     *
     * @param  string  $data
     *
     * @return  void
     *
     * @throws  SocketException
     */
    protected function writeToSocket($data) {
        $length = strlen($data);

        if(is_null($length)) {
            $type = gettype($data);
            throw new SocketException("You are trying to send {$type} instead of string.");
        }

        $written = fwrite($this->socket, $data, $length);

        if ($written !== $length) {
            throw new SocketException(
                "Could only write $written out of " . strlen($data) . " bytes."
            );
        }
    }

    /**
     * Close socket.
     *
     * @return  void
     *
     * @throws  SocketException
     */
    protected function closeSocket()
    {
        if(! fclose($this->socket)) {
            $metadata = json_encode(stream_get_meta_data($this->socket));
            throw new SocketException("Can not close socket. Socket state:\r\n{$metadata}");
        }
    }

}