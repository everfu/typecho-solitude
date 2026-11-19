<?php
use Solitude\Theme;
use Solitude\View;

switch($module):
case 'about':
    $card=Theme::get('aside.my_card');
    $author=Theme::settings()['author']??'Solitude';
?>
<div class="card-widget card-info tw:relative tw:[#aside-content_&]:overflow-hidden">
    <div class="card-content">
        <div class="card-info-avatar is-center"><div class="top-group"><div class="sayhi" id="sayhi" data-solitude-action="changeWittyWord"></div></div></div>
        <div class="avatar">
            <img src="<?=Theme::e($card['author']['img'])?>" alt="<?=Theme::e($author)?>" loading="lazy">
            <?php if(!empty($card['author']['emoji'])):?><span class="sticker sticker-emoji" aria-hidden="true"><?=Theme::e($card['author']['emoji'])?></span>
            <?php elseif(!empty($card['author']['sticker'])):?><span class="sticker"><img class="sticker-img" src="<?=Theme::e(Theme::media($card['author']['sticker']))?>" alt=""></span><?php endif;?>
        </div>
        <div class="description"><?=$card['content']??''?></div>
        <div class="bottom-group"><span class="left"><div class="name"><?=Theme::e($author)?></div><div class="desc"><?=$card['description']??''?></div></span>
            <div class="social-icons is-center"><?php foreach($card['information']??[] as $link):?><a class="social-icon" href="<?=Theme::e(Theme::url($link['url']??'#'))?>" title="<?=Theme::e($link['name']??'')?>" aria-label="<?=Theme::e($link['name']??'')?>"><i class="solitude <?=Theme::e($link['icon']??'fas fa-link')?>" aria-hidden="true"></i></a><?php endforeach;?></div>
        </div>
    </div>
</div>
<?php break;case 'newestPost':?>
<div class="card-widget card-recent-post"><div class="item-headline"><i class="solitude fas fa-map"></i><span><?=Theme::e(Theme::t('recentPosts'))?></span></div>
    <div class="aside-list"><?php foreach(array_slice($posts,0,5) as $post):?><a class="aside-list-item" href="<?=Theme::e(Theme::permalink($post))?>" title="<?=Theme::e($post->title)?>">
        <?php if(Theme::field($post,'cover')):?><div class="thumbnail"><img src="<?=Theme::e(Theme::cover($post))?>" alt="" loading="lazy"></div><?php endif;?>
        <div class="content"><span class="title"><?=Theme::e($post->title)?></span><span class="categories"><?=Theme::e($post->categories[0]['name']??'')?></span></div>
    </a><?php endforeach;?></div>
</div>
<?php break;case 'allInfo':
    $info=Theme::get('aside.siteinfo');$limit=max(1,(int)Theme::get('aside.tags.limit',20));
?>
<div class="card-widget card-tags card-archives card-webinfo card-allinfo">
    <?php if(Theme::get('aside.tags.enable')):?><div class="card-tag-cloud tw:overflow-hidden tw:relative tw:flex tw:flex-row tw:flex-wrap">
        <?php foreach(array_slice($tags,0,$limit) as $tag):?><a href="<?=Theme::e($tag['permalink'])?>" class="<?=in_array($tag['name'],Theme::get('aside.tags.highlight_list',[]),true)?'highlight':''?>"><?=Theme::e($tag['name'])?><sup><?=$tag['count']?></sup></a><?php endforeach;?>
    </div><?php if(count($tags)>=$limit):?><button type="button" id="more-tags-btn" data-solitude-action="openAllTags"><?=Theme::e(Theme::t('moreTags'))?></button><?php endif;?><hr><?php endif;?>
    <div class="webinfo"><?php View::siteInfo($allPosts);?></div>
</div>
<?php break;case 'newest_comment':
    if(!Theme::get('comment.use')||!Theme::get('comment.sidebar',true))break;
?>
<div class="card-widget card-recent-comment"><div class="item-headline"><i class="solitude fas fa-comment"></i><span><?=Theme::e(Theme::t('recentComments'))?></span>
    <?php if(Theme::get('recent_comments.enable')):?><a class="recent-comment-more" href="<?=Theme::e(Theme::url('recentcomments/'))?>" title="<?=Theme::e(Theme::t('more'))?>" aria-label="<?=Theme::e(Theme::t('more'))?>"><i class="solitude fas fa-circle-chevron-right" aria-hidden="true"></i></a><?php endif;?>
    </div><div class="aside-list" data-comment-provider="<?=Theme::e(Theme::get('comment.use'))?>" aria-live="polite" aria-busy="false">
    <?php $comments=Theme::comments(5);foreach($comments as $comment):$post=\Utils\Helper::widgetById('contents',(int)$comment['cid']);?>
    <a class="aside-list-item" href="<?=Theme::e(Theme::permalink($post).'#comment-'.$comment['coid'])?>" title="<?=Theme::e($comment['title'])?>">
        <div class="thumbnail"><img src="<?=Theme::e(Theme::avatar($comment['mail']??''))?>" alt="" loading="lazy"></div>
        <div class="content"><div class="comment-meta"><span class="comment-author"><?=Theme::e($comment['author'])?></span><time class="datetime" datetime="<?=gmdate('c',$comment['created'])?>"><?=Theme::e(View::dateLabel($comment['created']))?></time></div>
            <div class="comment"><?=Theme::e(mb_substr(strip_tags($comment['text']),0,150))?></div><div class="comment-source"><i class="solitude fas fa-file-lines" aria-hidden="true"></i><span><?=Theme::e($comment['title'])?></span></div>
        </div>
    </a><?php endforeach;if(!$comments):?><div class="comment-status is-empty" role="status"><?=Theme::e(Theme::t('commentsEmpty'))?></div><?php endif;?>
    </div>
</div>
<?php break;endswitch;?>
