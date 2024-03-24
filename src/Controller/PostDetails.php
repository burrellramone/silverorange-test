<?php

namespace silverorange\DevTest\Controller;

use silverorange\DevTest\Context;
use silverorange\DevTest\Template;
use silverorange\DevTest\Model;

class PostDetails extends Controller
{
    private ?Model\Post $post = null;

    public function getContext(): Context
    {
        $context = new Context();

        if ($this->post === null) {
            $context->title = 'Not Found';
            $context->content = "A post with id {$this->params[0]} was not found.";
        } else {
            $context->title = $this->post->title;
            $context->post = $this->post;
        }

        return $context;
    }

    public function getTemplate(): Template\Template
    {
        if ($this->post === null) {
            return new Template\NotFound();
        }

        return new Template\PostDetails();
    }

    public function getStatus(): string
    {
        if ($this->post === null) {
            return $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found';
        }

        return $_SERVER['SERVER_PROTOCOL'] . ' 200 OK';
    }

    protected function loadData(): void
    {
        $id = $this->params[0];

        $query = "SELECT posts.*
                    FROM posts
                    WHERE posts.id = '{$id}'";

        $stmt = $this->db->query($query, \PDO::FETCH_CLASS, 'silverorange\DevTest\Model\Post');
        $this->post = $stmt->fetch();

        if ($this->post) {
            $author_id = $this->post->author_id;

            $query = "SELECT authors.*
                    FROM authors 
                    WHERE id = '{$author_id}'";

            $stmt = $this->db->query($query, \PDO::FETCH_CLASS, 'silverorange\DevTest\Model\Author');
            $author = $stmt->fetch();
            $this->post->author = $author;
        }
    }
}
