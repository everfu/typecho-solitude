<?php
if(!defined('__TYPECHO_ROOT_DIR__'))exit;
require_once __DIR__.'/functions.php';
use Solitude\Theme;
http_response_code(404);
Theme::render('header',['archive'=>$this,'type'=>'404','title'=>Theme::t('pageNotFound')]);
?>
<main id="error-wrap" aria-labelledby="error-page-title">
        <div class="error-content tw:[#body-wrap.error_#error-wrap_&]:flex tw:[#body-wrap.error_#error-wrap_&]:flex-row tw:[#body-wrap.error_#error-wrap_&]:justify-center tw:[#body-wrap.error_#error-wrap_&]:items-center tw:[#body-wrap.error_#error-wrap_&]:relative">
            <div class="error-img" style="background-image:url(<?=Theme::e(Theme::json(Theme::media(Theme::get('errorpage.img'))))?>)" aria-hidden="true"></div>
            <div class="error-info tw:[#body-wrap.error_#error-wrap_.error-content_&]:text-center">
                <h1 class="error_title" id="error-page-title">404</h1>
                <div class="error_subtitle"><?=Theme::e(Theme::get('errorpage.text',Theme::t('pageNotFound')))?></div>
                <a class="button--animated" href="<?=Theme::e(Theme::url())?>"><i class="solitude fas fa-arrow-up-right-from-square" aria-hidden="true"></i><?=Theme::e(Theme::t('backHome'))?></a>
            </div>
        </div>
</main>
<?php if(Theme::get('errorpage.recommendList')):?>
<section class="aside-list tw:[#body-wrap.error_&]:flex tw:[#body-wrap.error_&]:flex-row" aria-label="<?=Theme::e(Theme::t('recentPosts'))?>">
    <div class="aside-list-group tw:[#body-wrap.error_.aside-list_&]:flex tw:[#body-wrap.error_.aside-list_&]:flex-row tw:[#body-wrap.error_.aside-list_&]:flex-wrap tw:[#body-wrap.error_.aside-list_&]:justify-between">
        <?php foreach(array_slice(Theme::publicPosts(),0,5) as $post):?>
        <article class="aside-list-item">
            <a class="thumbnail" href="<?=Theme::e(Theme::permalink($post))?>"><img src="<?=Theme::e(Theme::cover($post))?>" alt="<?=Theme::e($post->title)?>" loading="lazy"></a>
            <div class="content"><a class="title" href="<?=Theme::e(Theme::permalink($post))?>"><?=Theme::e($post->title)?></a></div>
        </article>
        <?php endforeach;?>
    </div>
</section>
<?php endif;Theme::render('footer',['archive'=>$this,'type'=>'404']);?>
