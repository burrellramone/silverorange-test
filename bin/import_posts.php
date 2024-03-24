<?php
namespace silverorange\DevTest;

use ErrorException;
use Error;
use Exception;
use TypeError;

require __DIR__ . '/../vendor/autoload.php';

$filepath = $argv[1]??"../data/";

try {
    $e = null;

    $total = PostImporter::import($filepath);

    echo "Imported {$total} posts.\n";
    
} catch(Exception $e){
   
} catch(TypeError $e){

} catch(Error $e){

} catch(ErrorException $e){

} finally {
    if($e){
        die("Error: " . $e->getMessage() . "\n");
    }
}

