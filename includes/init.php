<?php

/**
 * Initializations
 *
 * Register an autoloader, start or resume the session etc.
 */
spl_autoload_register(function ($class) {
    require dirname(__DIR__) . "/classes/{$class}.php";
    //dirname part is like in node the bodyparser and such that we always have the path added
});

session_start();
// we need this to be able to work with this data and cookies.

require dirname(__DIR__) . '/config.php';
/* this is how we include all the global constants we declared in the file as this
file is being used in all the other files */

/**
 * Error and exception handling
 */
function errorHandler($level, $message, $file, $line)
{
    throw new ErrorException($message, 0, $level, $file, $line);
    /* function that turns and error into an exception
    notice how we pass over the arguments(properties) to the Exception class
    to be turned into an exception */
}

function exceptionHandler($exception)
{
    http_response_code(500);

    if (SHOW_ERROR_DETAIL) {

        echo "<h1>An error occurred</h1>";
        echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
        echo "<p>" . $exception->getMessage() . "'</p>";
        echo "<p>Stack trace: <pre>" . $exception->getTraceAsString() . "</pre></p>";
        echo "<p>In file '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        /* these properties are on the exception object and we echo them out tobe able to better
debug our code. */
    } else {
        /* this else code is to prevent the server to throw sensitive data in a production code and pose
a security issue. */
        echo "<h1>An error occurred</h1>";
        echo "<p>Please try again later.</p>";
    }

    exit();
}

set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
/* we redirect to the functions we defined the errors and exceptions. the errorhandler 
turns errors into exceptions so that is its only purpose overall. */
