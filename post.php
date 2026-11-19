<?php
if(!defined('__TYPECHO_ROOT_DIR__'))exit;
use Solitude\Theme;
use Solitude\View;
require_once __DIR__.'/functions.php';require_once __DIR__.'/core/View.php';
Theme::render('header',['archive'=>$this,'type'=>'post','title'=>$this->title]);
[$html,$headings]=View::headingContent($this->content);
if(Theme::field($this,'toc','1')==='0')$headings=[];
$aside=Theme::field($this,'aside','1')!=='0';
?>
<main class="layout <?=$aside?'':'hide-aside'?> tw:[.layout&]:relative" id="content-inner"><div id="post">
<?php if(Theme::field($this,'not_cover','0')==='1')Theme::render('post-meta',['post'=>$this]);?>
<article class="post-content article-container" data-sl-math><?=$html?></article>
<?php if(!$this->hidden):?>
    <?php Theme::render('copyright',['post'=>$this]);?>
    <div class="post-tools-right"><div class="tag_share"><div class="post-meta__tag-list"><?php View::postTags($this,true);?></div></div></div>
    <?php
    $posts=Theme::publicPosts();$idx=null;
    foreach($posts as $i=>$post)if((int)$post->cid===(int)$this->cid)$idx=$i;
    $previous=$idx!==null?($posts[$idx+1]??null):null;
    $next=$idx!==null?($posts[$idx-1]??null):null;
    if($previous||$next):?>
    <nav class="needEndHide pagination-post" id="pagination" aria-label="<?=Theme::e(Theme::t('postNavigation'))?>">
        <?php foreach(['prev'=>[$previous,$next],'next'=>[$next,$previous]] as $direction=>[$item,$other]):if(!$item)continue;?>
        <div class="<?=$direction?>-post <?=$other?($direction==='prev'?'pull-left':'pull-right'):'pull-full'?>"><a href="<?=Theme::e(Theme::permalink($item))?>"><div class="pagination-info"><div class="label"><?=Theme::e(Theme::t($direction==='prev'?'previousPost':'nextPost'))?></div><div class="<?=$direction?>_info"><?=Theme::e($item->title)?></div></div></a></div>
        <?php endforeach;?>
    </nav>
    <?php endif;
    $tagIds=array_column($this->tags,'mid');$related=[];
    foreach($posts as $post){
        if((int)$post->cid===(int)$this->cid||$post->created>$this->created)continue;
        $score=count(array_intersect($tagIds,array_column($post->tags,'mid')));
        if($score)$related[]=['post'=>$post,'score'=>$score];
    }
    usort($related,fn($a,$b)=>$b['score']<=>$a['score']);
    $related=array_slice($related,0,(int)Theme::get('related_post.limit',6));
    if(Theme::get('related_post.enable')&&$related):?>
    <section class="relatedPosts" aria-labelledby="related-posts-title">
        <div class="headline"><i class="solitude fas fa-star"></i><span id="related-posts-title"><?=Theme::e(Theme::t('relatedPostsLead'))?></span><button class="relatedPosts-random" type="button" data-solitude-action="randomPost"><?=Theme::e(Theme::t('browseRandomly'))?></button></div>
        <div class="relatedPosts-list"><?php foreach($related as ['post'=>$post]):?><article class="relatedPosts-item"><a class="relatedPosts-link" href="<?=Theme::e(Theme::permalink($post))?>"><img class="cover" src="<?=Theme::e(Theme::cover($post))?>" alt="" loading="lazy"><div class="content is-center"><div class="title"><?=Theme::e($post->title)?></div><time class="date"><?=gmdate('Y-m-d',(int)(Theme::get('related_post.date_type')==='updated'?$post->modified:$post->created)+(int)Theme::options()->timezone)?></time></div></a></article><?php endforeach;?></div>
    </section>
    <?php endif;Theme::render('comments',['archive'=>$this]);?>
<?php endif;?></div><?php if($aside)Theme::render('aside',['scope'=>'post','headings'=>$headings]);?></main>
<?php Theme::render('footer',['archive'=>$this]);?>
