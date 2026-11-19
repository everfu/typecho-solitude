<?php
use Solitude\Theme;
use Solitude\View;
$count=$post->hidden?0:View::wordCount($post->content);
$reprint=Theme::field($post,'reprint','0')==='1';
?>
<div id="post-info" class="tw:[.post_&]:absolute tw:[.post_&]:flex tw:[.post_&]:flex-col tw:[.post_&]:items-start tw:[.post_&]:justify-center">
    <div id="post-firstinfo"><div class="meta-firstline">
        <a class="post-meta-original"><?=Theme::e(Theme::t($reprint?'reprint':'original'))?></a>
        <?php if($post->categories):$category=$post->categories[0];?><span class="post-meta-categories"><a class="post-meta-categories" href="<?=Theme::e($category['permalink'])?>"><?=Theme::e($category['name'])?></a></span><?php endif;?>
        <div class="tag_share"><div class="post-meta__tag-list"><?php View::postTags($post);?></div></div>
    </div></div>
    <h1 class="post-title"><?=Theme::e($post->title)?></h1>
    <div id="post-meta"><div class="meta-secondline">
        <?php if(Theme::get('post.meta.wordcount')):?><span class="post-meta-wordcount"><i class="post-meta-icon solitude fas fa-file-word"></i><span class="word-count"><?=$count?></span></span><?php endif;?>
        <?php foreach(['date'=>['calendar-days',$post->created],'updated'=>['arrows-rotate',$post->modified?:$post->created]] as $key=>[$icon,$time]):if(Theme::get('post.meta.'.$key)):?><span class="post-meta-date"><i class="post-meta-icon solitude fas fa-<?=$icon?>"></i><time datetime="<?=gmdate('c',$time)?>"><?=Theme::e(View::dateLabel((int)$time))?></time></span><?php endif;endforeach;?>
        <?php if(Theme::get('post.meta.readtime')):?><span class="post-meta-readtime"><i class="post-meta-icon solitude fas fa-clock"></i><span><?=max(1,(int)ceil($count/(str_starts_with(Theme::locale(),'zh')?500:212)))?> <?=Theme::e(Theme::t('readingMinutes'))?></span></span><?php endif;?>
        <?php if(Theme::get('post.meta.locate')):?><span class="post-meta-position"><i class="solitude fas fa-location-dot"></i><span><?=Theme::e(Theme::field($post,'locate',Theme::get('post.default.locate')))?></span></span><?php endif;?>
        <?php if(Theme::get('post.meta.comment')&&$post->allowComment&&Theme::get('comment.count')&&Theme::get('comment.use')):?><a class="post-meta-commentcount" href="#post-comment" aria-label="<?=Theme::e(Theme::t('comments').' '.(int)$post->commentsNum)?>"><i class="solitude fas fa-comment" aria-hidden="true"></i><span class="comment-count" data-xid="<?=Theme::e(parse_url(Theme::permalink($post),PHP_URL_PATH))?>"><?=(int)$post->commentsNum?></span></a><?php endif;?>
    </div></div>
</div>
<div class="post-radius-bottom"></div>
