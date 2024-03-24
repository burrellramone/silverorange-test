<?php

namespace silverorange\DevTest;

use Exception;
use PDO;

final class PostImporter {

    /**
     * An instance of PDO
     *
     * @var PDO
     */
    private static $db = null;

    /**
     * Array of author ids, keyed by the ids
     *
     * @var array
     */
    private static $authors = [];

    /**
     * Imports a list of JSON post files into the database.
     * 
     * @throws Exception Throws Exception if there is an issue with the directory prodived to look for the posts,
     * or the post files themselves
     * @param string $dir The directory where the post files are located or the location of a specific post file to import
     * @return integer The number of posts that were imported
     */
    public static function import(string $filepath):int {
        if(empty($filepath)){
            throw new Exception("Directory or filepath empty");
        } else if (!file_exists($filepath)) {
            throw new Exception("Directory or file '{$filepath}' does not exist");
        }

        $total_imported = 0;
        $is_dir = is_dir($filepath);
        $files = [];

        if($is_dir){
            $files = glob($filepath . "/*.json");
        } else {
            $files[] = $filepath;
        }

        foreach($files as $filepath){
            self::importFromFile($filepath);

            $total_imported++;
        }

        return $total_imported;
    }

    /**
     * Imports a post from a specific file
     * 
     * @throws Exception Throws Exception is the post to import is invalid, or an
     * error occured when attempting to import the post into the datbase
     * @param string $filepath The path of the file to import the post from
     * @return void
     */
    private static function importFromFile(string $filepath){
        echo "Importing post from '{$filepath}' ...\n";

        $post = file_get_contents($filepath);

        if(empty($post)){
            throw new Exception("File '{$filepath}' is empty.");
        }

        $post = (array)json_decode($post);

        if(!self::validatePost($post)){
            throw new Exception("Post from file '$filepath' is not valid");
        }

        $id = $post['id'];
        $title = addslashes($post['title']);
        $body = addslashes($post['body']);
        $created_at = $post['created_at'];
        $modified_at = $post['modified_at'];
        $author_id = $post['author_id'];
        
        $insert = "INSERT INTO posts (id, title, body, created_at, modified_at, author_id) 
                    VALUES('{$id}', '{$title}', '{$body}', '{$created_at}', '{$modified_at}', '{$author_id}')
                    ON CONFLICT (id) DO UPDATE
                    SET title = excluded.title,
                    body = excluded.body,
                    modified_at = excluded.modified_at,
                    author_id = excluded.author_id;";
        
        $result = self::$db->exec($insert);

        if(!$result){
            $error = self::$db->errorInfo();

            throw new Exception("Could not import post from file '$filepath'. Received error '{$error[2]}'");
        }

        echo "Imported post from '$filepath'\n";
    }

    public static function validatePost(array $post):bool {
        if(empty($post['id']) || empty($post['title']) || empty($post['body']) || empty($post['author_id'])){
            return false;
        } else if (empty($post['created_at']) || empty($post['modified_at'])) {
            return false;
        }

        self::createDbConn();

        //check to see if the author exists
        $author_id = $post['author_id'];

        if(isset(self::$authors[$author_id])){
            return true;
        }

        $stmt = self::$db->query("SELECT id FROM authors WHERE id = '{$author_id}'", PDO::FETCH_ASSOC);
        $record = $stmt->fetchAll();

        if($record){
            self::$authors[$author_id] = true;
            return true;
        }

        return false;
    }

    private static function createDbConn(){
        if(!isset(self::$db)){
            $config = new Config();
            self::$db = (new Database($config->dsn))->getConnection();
        }
    }
}