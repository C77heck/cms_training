<?php

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
/* we use the declared constants from the config file */
return $db->getConn();
//create the database instance and then call the function to get the connection
