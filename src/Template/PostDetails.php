<?php

namespace silverorange\DevTest\Template;

use Parsedown;
use silverorange\DevTest\Context;

class PostDetails extends Layout
{
    protected function renderPage(Context $context): string
    {
        $post = $context->post;
        $pd = new Parsedown(); ?>

        <div>
            <a href="/posts" title="Back"><< Back</a>
        </div>
        <div class="post">
            <div class="post-title"><h1><?=$post->title?></h1></div>
            <div class="post-author"><strong>Author:</strong> <?=$post->author->full_name?></div>
            <div class="post-body"><?=$pd->text($post->body)?></div>
        </div>
        <div>
            <a href="/posts" title="Back"><< Back</a>
        </div>
        <?php
        $page = ob_get_clean();
        
        return $page;
    }
}
