<?php

namespace Pug\Framework;

use Pug\Framework\Session\Handler;
use Pug\Http\Cookie;
use Pug\Http\Request;

class Session
{
    /**
     * The session handler.
     *
     * @var Handler
     */
    public static $handler = null;

    /**
     * Bootstrap the session.
     *
     * @param Config $config The session config
     */
    public static function bootstrap(Config $config)
    {
        if (!$config->cookie_name) {
            $config->cookie_name = 'session_id';
        }

        if (!$config->id_format) {
            $config->id_format = Handler::DEFAULT_ID_FORMAT;
        }

        // Create and set the session save handler
        static::$handler = new Handler($config);
        session_set_save_handler(static::$handler, true);

        // Set the session name
        session_name($config->cookie_name);

        // Set the cookie parameters
        $app      = Application::instance();
        $path     = $app->config->base_uri ?: '/';
        $domain   = $app->config->domain ?: $_SERVER['HTTP_HOST'];
        $secure   = $app->request->isSecure();
        $lifetime = $config->lifetime ?: 2592000;
        session_set_cookie_params($lifetime, $path, $domain, $secure, true);

        // Start the session
        session_start();

        // Register session_write_close() as a shutdown function
        session_register_shutdown();
    }

    /**
     * Get a value from the session.
     *
     * TODO: Replace $_SESSION superglobal with Molovo\Object\Object instance
     *
     * @param string $key The session key
     *
     * @return mixed The value
     */
    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return;
    }

    /**
     * Store a value in the session.
     *
     * TODO: Replace $_SESSION superglobal with Molovo\Object\Object instance
     *
     * @param string $key   The session key
     * @param mixed  $value The value to set
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
}
