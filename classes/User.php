<?php
/**
 * User
 *
 * A person or entity that can login to the site
 *  */
class User
{
/**
 * id
 * @var integer
 */
/**
 * username
 * @var string
 */
/**
 * password
 * @var string
 */
    public $id;
    public $username;
    public $password;
    /**
     * Authenticate a user
     *
     * @param string username
     * @param string password
     * @return boolean true if credentials are checking out or null if they aren't.
     *
     *  */
    public static function authenticate($conn, $username, $password)
    {
        $sql = "SELECT *
        FROM user
        WHERE username= :username";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
        //we set to return an object(class) rather than an array.
        $stmt->execute();

        if ($user = $stmt->fetch()) {
            return password_verify($password, $user->password);
            
        }
    }

}
