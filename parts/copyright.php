<?php
use Solitude\Theme;
$author=Theme::field($post,'author',Theme::settings()['author']??'Solitude');
$avatar=Theme::field($post,'avatar',Theme::get('post.default.copyright.author',Theme::get('site.icon')));
$background=Theme::field($post,'avatar_background',Theme::get('post.default.copyright.author_background',''));
$copyright=Theme::field($post,'copyright',Theme::get('post.default.copyright.enable')?'1':'0')!=='0';
?>
<section class="post-copyright" aria-label="<?=Theme::e(Theme::t('copyrightSection'))?>">
    <div class="post-copyright__author_group">
        <a class="post-copyright__author_img <?=$background?'post-copyright__author_img--layered':''?>" href="<?=Theme::e(Theme::url('about/'))?>" aria-label="<?=Theme::e(Theme::t('author').': '.$author)?>">
            <?php if($background):?><img class="post-copyright__author_img_back" src="<?=Theme::e(Theme::media($background))?>" alt="" aria-hidden="true"><?php endif;?>
            <img class="post-copyright__author_img_front" src="<?=Theme::e(Theme::media($avatar))?>" alt="<?=Theme::e($author)?>">
        </a>
        <div class="post-copyright__author_name"><?=Theme::e($author)?></div>
        <div class="post-copyright__author_desc"><?=Theme::e(Theme::field($post,'desc',\Solitude\Settings::description()))?></div>
    </div>
    <?php if(Theme::get('post.share.enable')&&Theme::get('post.share.list',[])):?><div class="post-tools" role="group" aria-label="<?=Theme::e(Theme::t('copyrightActions'))?>">
        <?php if(Theme::get('post.share.enable')):?><div class="post-copyright__share">
            <?php foreach(Theme::get('post.share.list',[]) as $name):if($name==='link'):?><button class="post-copyright__share-button" type="button" data-solitude-action="copy" data-solitude-value="<?=Theme::e(Theme::permalink($post))?>" aria-label="<?=Theme::e(Theme::t('copyLink'))?>"><i class="solitude fas fa-link"></i></button>
            <?php else:?><a class="post-copyright__share-button" href="https://www.addtoany.com/add_to/<?=rawurlencode($name)?>?linkurl=<?=rawurlencode(Theme::permalink($post))?>" target="_blank" rel="noopener noreferrer" aria-label="<?=Theme::e(Theme::t('shareTo').' '.$name)?>"><i class="solitude fab fa-<?=Theme::e($name)?>"></i></a><?php endif;endforeach;?>
        </div><?php endif;?>

    </div><?php endif;?>
    <?php if($copyright):?><div class="post-copyright__notice"><div class="post-copyright-info">
        <?php if(Theme::field($post,'reprint','0')==='1'):?><?=Theme::e(Theme::t('copyrightReprint'))?>
        <?php else:?><?=Theme::e(Theme::t('copyrightOriginalPrefix'))?><a href="<?=Theme::e(Theme::get('post.default.copyright.licenurl'))?>"><?=Theme::e(Theme::get('post.default.copyright.license'))?></a><?=Theme::e(Theme::t('copyrightOriginalMiddle'))?><a href="<?=Theme::e(Theme::url())?>"><?=Theme::e(\Solitude\Settings::title())?></a><?=Theme::e(Theme::t('copyrightOriginalSuffix'))?><?php endif;?>
    </div></div><?php endif;?>
</section>
