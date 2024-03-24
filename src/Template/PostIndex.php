<?php

namespace silverorange\DevTest\Template;

use silverorange\DevTest\Context;

class PostIndex extends Layout
{
    protected function renderPage(Context $context): string
    {
        $posts = $context->posts;
        ob_start();
        ?>
        <div class="ptctr">
            <h1>Posts</h1>
            <table class="posts">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($posts) { ?>
                        <?php foreach ($posts as $post) { ?>
                            <tr>
                                <td><?=$post->title?></td>
                                <td><?=$post->author->full_name?></td>
                                <td><a href="/posts/<?=$post->id?>">Details</a></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr class="no-posts">
                            <td colspan="3">No Posts Found</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <th>Title</th>
                    <th>Author</th>
                    <th></th>
                </tfoot>
            </table>
            <p><?=count($posts)?> Posts</p>
        </div>
        <?php

        $page = ob_get_clean();

        return $page;
    }
}
