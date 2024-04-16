<?php

namespace MJ\Router;

use ReflectionException;
use ReflectionMethod;


/**
 * Class Router
 * @package MJ\Router
 */
class Router
{
    /**
     * @var array
     */
    private static $afterRoutes = [];


    /**
     * @var array
     */
    private static $beforeRoutes = [];


    /**
     * @var array
     */
    private static $notFoundCallback = [];


    /**
     * @var string
     */
    private static $baseRoute = '';


    /**
     * @var string
     */
    private static $requestedMethod = '';


    /**
     * @var string
     */
    private static $serverBasePath = '';


    /**
     * @var string
     */
    private static $namespace = '';


    /**
     *
     * @param string $methods
     * @param string $pattern
     * @param callable $fn
     * @author Tjavan
     * @version 1.0.0
     */
    public static function before($methods, $pattern, $fn)
    {
        $pattern = self::$baseRoute . '/' . trim($pattern, '/');
        $pattern = self::$baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            self::$beforeRoutes[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn
            ];
        }
    }


    /**
     *
     * @param string $methods
     * @param string $pattern
     * @param callable $fn
     * @author Tjavan
     * @version 1.0.0
     */
    public static function match($methods, $pattern, $fn)
    {
        $pattern = self::$baseRoute . '/' . trim($pattern, '/');
        $pattern = self::$baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            self::$afterRoutes[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn
            ];
        }
    }


    /**
     *
     * @param string $pattern
     * @param callable $fn
     * @author Tjavan
     * @version 1.0.0
     */
    public static function all($pattern, $fn)
    {
        self::match('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $pattern, $fn);
    }


    /**
     *
     * @param string $pattern
     * @param callable
     * @author Tjavan
     * @version 1.0.0
     */
    public static function get($pattern, $fn)
    {
        self::match('GET', $pattern, $fn);
    }


    /**
     *
     * @param string $pattern
     * @param callable $fn
     * @author Tjavan
     * @version 1.0.0
     */
    public static function post($pattern, $fn)
    {
        self::match('POST', $pattern, $fn);
    }


    /**
     *
     * @param string $pattern
     * @param callable $fn
     * @author Tjavan
     * @version 1.0.0
     */
    public static function patch($pattern, $fn)
    {
        self::match('PATCH', $pattern, $fn);
    }


    /**
     *
     * @param string $pattern
     * @param callable $fn
     * @author Tjavan
     * @version 1.0.0
     */
    public static function delete($pattern, $fn)
    {
        self::match('DELETE', $pattern, $fn);
    }


    /**
     *
     * @param string $pattern
     * @param callable $fn
     * @author Tjavan
     * @version 1.0.0
     */
    public static function put($pattern, $fn)
    {
        self::match('PUT', $pattern, $fn);
    }


    /**
     *
     * @param string $pattern
     * @param callable $fn
     * @author Tjavan
     * @version 1.0.0
     */
    public static function options($pattern, $fn)
    {
        self::match('OPTIONS', $pattern, $fn);
    }


    /**
     *
     * @param string $baseRoute
     * @param callable $fn
     * @author Tjavan
     * @version 1.0.0
     */
    public static function mount($baseRoute, $fn)
    {
        $curBaseRoute = self::$baseRoute;

        self::$baseRoute .= $baseRoute;

        call_user_func($fn);

        self::$baseRoute = $curBaseRoute;
    }


    /**
     *
     * @return array|false
     * @version 1.0.0
     * @author Tjavan
     */
    public static function getRequestHeaders()
    {
        $headers = array();

        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            if ($headers !== false) {
                return $headers;
            }
        }

        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }


    /**
     *
     * @return array|false
     * @version 1.0.0
     * @author Tjavan
     */
    public static function getRequestMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = self::getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
    }


    /**
     *
     * @param string $namespace
     * @version 1.0.0
     * @author Tjavan
     */
    public static function setNamespace($namespace)
    {
        if (is_string($namespace)) {
            self::$namespace = $namespace;
        }
    }


    /**
     *
     * @return string
     * @version 1.0.0
     * @author Tjavan
     */
    public static function getNamespace()
    {
        return self::$namespace;
    }


    /**
     *
     * @param callable | null $callback
     * @return bool
     * @version 1.0.0
     * @author Tjavan
     */
    public static function run($callback = null)
    {
        self::$requestedMethod = self::getRequestMethod();

        if (isset(self::$beforeRoutes[self::$requestedMethod])) {
            self::handle(self::$beforeRoutes[self::$requestedMethod]);
        }

        $numHandled = 0;
        if (isset(self::$afterRoutes[self::$requestedMethod])) {
            $numHandled = self::handle(self::$afterRoutes[self::$requestedMethod], true);
        }

        if ($numHandled === 0) {
            self::trigger404(self::$afterRoutes[self::$requestedMethod]);
        } else {
            if ($callback && is_callable($callback)) {
                $callback();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }

        return $numHandled !== 0;
    }


    /**
     *
     * @param callable | null $match_fn
     * @param callable | null $fn
     * @version 1.0.0
     * @author Tjavan
     */
    public static function set404($match_fn, $fn = null)
    {
        if (!is_null($fn)) {
            self::$notFoundCallback[$match_fn] = $fn;
        } else {
            self::$notFoundCallback['/'] = $match_fn;
        }
    }


    /**
     *
     * @param string | null $match
     * @version 1.0.0
     * @author Tjavan
     */
    public static function trigger404($match = null)
    {
        $numHandled = 0;

        if (count(self::$notFoundCallback) > 0) {
            foreach (self::$notFoundCallback as $route_pattern => $route_callable) {

                $matches = [];

                $is_match = self::patternMatches($route_pattern, self::getCurrentUri(), $matches, PREG_OFFSET_CAPTURE);

                if ($is_match) {

                    $matches = array_slice($matches, 1);

                    $params = array_map(static function ($match, $index) use ($matches) {

                        if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                            if ($matches[$index + 1][0][1] > -1) {
                                return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                            }
                        }

                        return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
                    }, $matches, array_keys($matches));

                    self::invoke($route_callable);

                    ++$numHandled;
                }
            }
        }
        if (($numHandled == 0) && (isset(self::$notFoundCallback['/']))) {
            self::invoke(self::$notFoundCallback['/']);
        } elseif ($numHandled == 0) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        }
    }


    /**
     *
     * @param string $pattern
     * @param string $uri
     * @param mixed $matches
     * @param string $flags
     * @return bool
     * @author Tjavan
     * @version 1.0.0
     */
    private static function patternMatches($pattern, $uri, &$matches, $flags)
    {
        $pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $pattern);

        return boolval(preg_match_all('#^' . $pattern . '$#', $uri, $matches, PREG_OFFSET_CAPTURE));
    }


    /**
     *
     * @param array $routes
     * @param bool $quitAfterRun
     * @return int
     * @author Tjavan
     * @version 1.0.0
     */
    private static function handle($routes, $quitAfterRun = false)
    {
        $numHandled = 0;

        $uri = self::getCurrentUri();

        foreach ($routes as $route) {

            $is_match = self::patternMatches($route['pattern'], $uri, $matches, PREG_OFFSET_CAPTURE);

            if ($is_match) {

                $matches = array_slice($matches, 1);

                $params = array_map(static function ($match, $index) use ($matches) {

                    if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        if ($matches[$index + 1][0][1] > -1) {
                            return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                        }
                    }

                    return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
                }, $matches, array_keys($matches));

                self::invoke($route['fn'], $params);

                ++$numHandled;

                if ($quitAfterRun) {
                    break;
                }
            }
        }

        return $numHandled;
    }


    /**
     *
     * @param callable $fn
     * @param array $params
     * @version 1.0.0
     * @author Tjavan
     */
    private static function invoke($fn, $params = array())
    {
        if (is_callable($fn)) {
            call_user_func_array($fn, $params);
        } elseif (stripos($fn, '@') !== false) {
            list($controller, $method) = explode('@', $fn);

            if (self::getNamespace() !== '') {
                $controller = self::getNamespace() . '\\' . $controller;
            }

            try {
                $reflectedMethod = new ReflectionMethod($controller, $method);
                if ($reflectedMethod->isPublic() && (!$reflectedMethod->isAbstract())) {
                    if ($reflectedMethod->isStatic()) {
                        forward_static_call_array(array($controller, $method), $params);
                    } else {
                        if (is_string($controller)) {
                            $controller = new $controller();
                        }
                        call_user_func_array(array($controller, $method), $params);
                    }
                }
            } catch (ReflectionException $reflectionException) {
            }
        }
    }


    /**
     *
     * @return string
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCurrentUri()
    {
        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen(self::getBasePath()));

        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        return '/' . trim($uri, '/');
    }


    /**
     *
     * @return string
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getBasePath()
    {
        if (self::$serverBasePath === null) {
            self::$serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }

        return self::$serverBasePath;
    }


    /**
     *
     * @param string $serverBasePath
     * @author Tjavan
     * @version 1.0.0
     */
    public static function setBasePath($serverBasePath)
    {
        self::$serverBasePath = $serverBasePath;
    }

}