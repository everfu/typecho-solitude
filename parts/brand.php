<?php use Solitude\Theme;$brand=Theme::get('site.name');$groups=Theme::get('nav.group',[]);?>
<div id="blog_name">
    <?php if($groups):?><div class="back-home-button tw:flex tw:items-center tw:justify-center" tabindex="0" aria-label="<?=Theme::e(Theme::t('quickLinks'))?>">
        <svg class="back-home-button-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.75 2.5C9.1 2.5 11 4.4 11 6.75V11H6.75a4.25 4.25 0 1 1 0-8.5ZM6.75 13H11v4.25A4.25 4.25 0 1 1 6.75 13ZM17.25 2.5a4.25 4.25 0 0 1 0 8.5H13V6.75a4.25 4.25 0 0 1 4.25-4.25ZM13 13h4.25A4.25 4.25 0 1 1 13 17.25Z"/></svg>
        <div class="back-menu-list-groups"><?php ksort($groups);foreach($groups as $name=>$links):?>
            <div class="back-menu-list-group"><div class="back-menu-list-title"><?=Theme::e($name)?></div><div class="back-menu-list">
                <?php foreach($links as $link):?><a class="back-menu-item" href="<?=Theme::e(Theme::url($link['url']??'#'))?>"><?php if(!empty($link['icon'])):?><img class="nolazyload back-menu-item-icon" src="<?=Theme::e(Theme::media($link['icon']))?>" alt=""><?php endif;?><span class="back-menu-item-text"><?=Theme::e($link['name']??'')?></span></a><?php endforeach;?>
            </div></div>
        <?php endforeach;?></div>
    </div><?php endif;?>
    <a id="site-name" href="<?=Theme::e(Theme::url())?>" title="<?=Theme::e(Theme::t('backHome'))?>" heotip="<?=Theme::e(Theme::t('backHome'))?>">
        <?php if($brand['class']==='img'):?><img src="<?=Theme::e(Theme::media($brand['custom']))?>" alt="<?=Theme::e(\Solitude\Settings::title())?>">
        <?php elseif($brand['class']==='i_class'):?><i class="<?=Theme::e($brand['custom'])?>" aria-hidden="true"></i>
        <?php else:?><span class="title"><?=Theme::e($brand['custom'])?></span><?php endif;?>
        <i class="solitude fas fa-home" aria-hidden="true"></i>
    </a>
</div>
