<?php
class Auth
{
/**
 * Return the user authentication status
 *
 * @return boolean True if a user is logged in, false otherwise
 */
    public static function isLoggedIn()
    {
        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'];
    }
    /**
     * Checks if user is logged in
     *
     * @return string message about not being authorized
     */
    public static function requireLogin()
    {
        if (!static::isLoggedIn()) {
            die('unauthorized');
        }
    }
    /**
     * Log in user logic and regenerate id for safety
     *
     * @return void
     */
    public static function login()
    {
        session_regenerate_id(true);
// this will delete the old session id help prevent session fixation attacks.
        $_SESSION['is_logged_in'] = true;
    }
    /**
     * Log out using the session
     *
     * @return void
     */
    public static function logout()
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }
}
