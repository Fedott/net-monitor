<?php

namespace Fedot\NetMonitor\Service;

use Dflydev\ApacheMimeTypes\PhpRepository;
use finfo;
use Guzzle\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServerInterface;
use Symfony\Component\HttpFoundation\Response;

class HttpServer implements HttpServerInterface
{
    /**
     * @var PhpRepository
     */
    protected $mimeRepositiory;

    public function __construct()
    {
        $this->mimeRepositiory = new PhpRepository();
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     *
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     *
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        // TODO: Implement onClose() method.
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     *
     * @param  ConnectionInterface $conn
     * @param  \Exception          $e
     *
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    /**
     * @param \Ratchet\ConnectionInterface          $conn
     * @param \Guzzle\Http\Message\RequestInterface $request null is default because PHP won't let me overload; don't pass null!!!
     *
     * @throws \UnexpectedValueException if a RequestInterface is not passed
     */
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null)
    {
        $path = $request->getPath();
        if ($path === '/') {
            $path = '/index.html';
        }

        $fullPath = __DIR__ . '/../../../../front/dist' . $path;

        if (!file_exists($fullPath)) {
            $response = new Response('', 404);
        } else {
            $fileExtension = pathinfo($fullPath, PATHINFO_EXTENSION);
            $mimeType = $this->mimeRepositiory->findType($fileExtension);
            $response = new Response(file_get_contents($fullPath), 200, [
                'Content-Type' => $mimeType,
            ]);
        }

        $conn->send($response);
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     *
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string                       $msg  The message received
     *
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        // TODO: Implement onMessage() method.
    }
}
