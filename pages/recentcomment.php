<?php
use Solitude\Theme;
use Solitude\View;
if(!Theme::get('recent_comments.enable')||!Theme::get('comment.use'))return;
$cover=Theme::field($archive,'cover','/img/demo/cover-pagination-v2.webp');
$tip=Theme::field($archive,'desc',Theme::field($archive,'description',''));
?>
<section class="author-content author-content-item single sharePage" style="background:url(<?=Theme::e(Theme::json(Theme::media($cover)))?>) no-repeat center;background-size:cover">
    <div class="card-content">
        <?php if($tip):?><div class="author-content-item-tips"><?=Theme::e($tip)?></div><?php endif;?>
        <span class="author-content-item-title"><?=Theme::e($archive->title)?></span>
        <?php $left=Theme::field($archive,'leftend','');$right=Theme::field($archive,'rightend','');if($left||$right):?><div class="content-bottom"><?php foreach([$left,$right] as $end)if($end)echo '<span class="tips">'.Theme::e($end).'</span>';?></div><?php endif;?>
        <?php $button=Theme::field($archive,'rightbtn','');$url=Theme::field($archive,'rightbtnlink','');if($button&&$url):?><div class="banner-button-group"><a class="banner-button" href="<?=Theme::e(Theme::url($url))?>"><i class="solitude fas fa-circle-chevron-right" aria-hidden="true"></i><span class="banner-button-text"><?=Theme::e($button)?></span></a></div><?php endif;?>
    </div>
</section>
<?php if(Theme::get('comment.use')==='native'):View::commentWall();else:?><div class="console_recentcomments recent-comments-list" data-comment-provider="<?=Theme::e(Theme::get('comment.use'))?>" aria-live="polite" aria-busy="true"><?=Theme::e(Theme::t('commentsLoading'))?></div><?php endif;?>
