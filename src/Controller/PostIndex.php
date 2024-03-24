<?php

namespace silverorange\DevTest\Controller;

use silverorange\DevTest\Context;
use silverorange\DevTest\Template;

class PostIndex extends Controller
{
    private array $posts = [];

    public function getContext(): Context
    {
        $context = new Context();
        $context->title = 'Posts';
        $context->posts = $this->posts;

        return $context;
    }

    public function getTemplate(): Template\Template
    {
        return new Template\PostIndex();
    }

    protected function loadData(): void
    {
        $query = "SELECT posts.*
                    FROM posts 
                    ORDER BY posts.created_at DESC, posts.modified_at DESC";

        $stmt = $this->db->query($query, \PDO::FETCH_CLASS, 'silverorange\DevTest\Model\Post');
        $this->posts = $stmt->fetchAll();

        $authors = array();

        foreach ($this->posts as &$post) {
            $author_id = $post->author_id;

            if (isset($authors[$author_id])) {
                $post->author = $authors[$author_id];
                continue;
            }

            $query = "SELECT authors.*
                    FROM authors 
                    WHERE id = '{$author_id}'";

            $stmt = $this->db->query($query, \PDO::FETCH_CLASS, 'silverorange\DevTest\Model\Author');
            $authors[$author_id] = $stmt->fetch();

            $post->author = $authors[$author_id];
        }
    }
}
