<?php
use Solitude\Theme;
use Solitude\View;
$link=Theme::permalink($post);
$right=Theme::get('index_post_list.direction')!=='column'&&(Theme::get('index_post_list.cover')==='right'||(Theme::get('index_post_list.cover')==='both'&&$index%2===1));
$newest=Theme::posts()[0]??null;
?>
<article class="recent-post-item tw:flex tw:items-center tw:overflow-hidden tw:cursor-pointer" <?=$right?'style="flex-direction:row-reverse"':''?> data-solitude-action="navigateTo" data-solitude-url="<?=Theme::e($link)?>">
    <div class="post_cover <?=$index%2?'right':'left'?>"><a href="<?=Theme::e($link)?>" title="<?=Theme::e($post->title)?>"><img class="post_bg nolazyload" src="<?=Theme::e(Theme::cover($post))?>" alt="<?=Theme::e($post->title)?>" loading="lazy" data-solitude-fallback="<?=Theme::e(Theme::media(Theme::get('post.default.cover.0')))?>"></a></div>
    <div class="recent-post-info"><div class="recent-post-info-top"><div class="recent-post-info-top-tips">
        <?php if(Theme::field($post,'sticky',0)):?><span class="article-meta sticky-warp"><i class="solitude fas fa-thumbtack sticky orange"></i><span class="sticky orange"><?=Theme::e(Theme::t('homeSticky'))?></span></span><?php endif;?>
        <?php foreach($post->categories as $category):?><span class="original"><?=Theme::e($category['name'])?></span><?php endforeach;?>
        <?php if($newest&&(int)$newest->cid===(int)$post->cid):?><span class="original latest-post"><?=Theme::e(Theme::t('homeNew'))?></span><?php endif;?>
        <a class="unvisited-post" href="<?=Theme::e($link)?>"><?=Theme::e(Theme::t('homeRead'))?></a>
    </div><a class="article-title" href="<?=Theme::e($link)?>" title="<?=Theme::e($post->title)?>"><?=Theme::e($post->title)?></a></div>
    <div class="article-meta-wrap tw:relative tw:flex tw:flex-row tw:justify-between tw:items-center tw:whitespace-nowrap">
        <?php if($post->allowComment):?><div class="post-card-commenters tw:flex tw:items-center" data-comment-path="<?=Theme::e(parse_url($link,PHP_URL_PATH))?>" hidden></div><?php endif;?>
        <?php if($post->tags):?><span class="article-meta tags"><?=View::tags($post)?></span><?php endif;?>
        <span class="post-meta-date"><time datetime="<?=gmdate('c',$post->created)?>"><?=Theme::e(View::dateLabel((int)$post->created))?></time></span>
    </div></div>
</article>
