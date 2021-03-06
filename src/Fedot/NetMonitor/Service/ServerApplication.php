<?php

namespace Fedot\NetMonitor\Service;

use Ratchet\ComponentInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as Reactor;
use Ratchet\Http\HttpServerInterface;
use Ratchet\Http\OriginCheck;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer as BaseHttpServer;
use Ratchet\Http\Router;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * An opinionated facade class to quickly and easily create a WebSocket server.
 * A few configuration assumptions are made and some best-practice security conventions are applied by default.
 */
class ServerApplication
{
    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    public $routes;

    /**
     * @var \Ratchet\Server\IoServer
     */
    protected $server;

    /**
     * The Host passed in construct used for same origin policy
     * @var string
     */
    protected $httpHost;

    /***
     * The port the socket is listening
     * @var int
     */
    protected $port;

    /**
     * @var int
     */
    protected $routeCounter = 0;

    /**
     * @param string        $httpHost HTTP hostname clients intend to connect to. MUST match JS `new
     *                                WebSocket('ws://$httpHost');`
     * @param int           $port     Port to listen on. If 80, assuming production, Flash on 843 otherwise expecting
     *                                Flash to be proxied through 8843
     * @param string        $address  IP address to bind to. Default is localhost/proxy only. '0.0.0.0' for any
     *                                machine.
     * @param LoopInterface $loop     Specific React\EventLoop to bind the application to. null will create one for
     *                                you.
     */
    public function __construct(LoopInterface $loop, $httpHost = 'localhost', $port = 1788, $address = '0.0.0.0')
    {
        if (extension_loaded('xdebug')) {
            trigger_error('XDebug extension detected. Remember to disable this if performance testing or going live!', E_USER_WARNING);
        }

        if (3 !== strlen('✓')) {
            throw new \DomainException('Bad encoding, length of unicode character ✓ should be 3. Ensure charset UTF-8 and check ini val mbstring.func_autoload');
        }

        if (null === $loop) {
            $loop = LoopFactory::create();
        }

        $this->httpHost = $httpHost;
        $this->port = $port;

        $socket = new Reactor($loop);
        $socket->listen($port, $address);

        $this->routes = new RouteCollection;
        $this->server = new IoServer(new BaseHttpServer(new Router(new UrlMatcher($this->routes, new RequestContext))), $socket, $loop);
    }

    /**
     * Add an endpoint/application to the server
     *
     * @param string             $path           The URI the client will connect to
     * @param ComponentInterface $controller     Your application to server for the route. If not specified, assumed to
     *                                           be for a WebSocket
     * @param array              $allowedOrigins An array of hosts allowed to connect (same host by default), ['*'] for
     *                                           any
     * @param string             $httpHost       Override the $httpHost variable provided in the __construct
     *
     * @return ComponentInterface|WsServer
     */
    public function route($path, ComponentInterface $controller, array $allowedOrigins = ['*'], $httpHost = null)
    {
        if ($controller instanceof HttpServerInterface || $controller instanceof WsServer) {
            $decorated = $controller;
        } elseif ($controller instanceof WampServerInterface) {
            $decorated = new WsServer(new WampServer($controller));
        } elseif ($controller instanceof MessageComponentInterface) {
            $decorated = new WsServer($controller);
        } else {
            $decorated = $controller;
        }

        if ($httpHost === null) {
            $httpHost = $this->httpHost;
        }

        $allowedOrigins = array_values($allowedOrigins);
        if (0 === count($allowedOrigins)) {
            $allowedOrigins[] = $httpHost;
        }
        if ('*' !== $allowedOrigins[0]) {
            $decorated = new OriginCheck($decorated, $allowedOrigins);
        }

        $this->routes->add('rr-' . ++$this->routeCounter, new Route($path, ['_controller' => $decorated], ['Origin' => $this->httpHost], [], $httpHost));

        return $decorated;
    }

    /**
     * Run the server by entering the event loop
     */
    public function run()
    {
        $this->server->run();
    }
}
