<?php

namespace silverorange\DevTest;
use silverorange\DevTest\Model\Post;

class Context {


    public string $title = '';

    public string $content = '';

    public array $posts;

    public ?Post $post;

    public function getPostId():?string {
        return @$this->post->id;
    }
}
