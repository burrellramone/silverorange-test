<?php

namespace silverorange\DevTest;

use Exception;

require __DIR__ . '/../vendor/autoload.php';

try {
    $config = new Config();
    $db = (new Database($config->dsn))->getConnection();
    
    $app = new App($db);
    return $app->run();

} catch(Exception $e) {

} finally {
    if($e){
        die("Error:" . $e->getMessage());
    }
}