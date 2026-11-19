<?php
use Solitude\Theme;
use Solitude\View;

$data=View::archiveData(Theme::publicPosts());
$perPage=max(1,(int)Theme::get('page.archives_per_page',Theme::options()->pageSize));
$requestedYear=isset($archive)?(string)$archive->request->get('sl_archive_year',$archive->request->get('sl_year',$archive->request->get('year','all'))):'all';
$filtered=array_values(array_filter($data,fn($post)=>$requestedYear==='all'||$post['year']===$requestedYear));
$requestedPage=isset($archive)?max(1,(int)$archive->request->get('page',1)):1;
$visible=array_slice($filtered,($requestedPage-1)*$perPage,$perPage);
?>
<div class="archive-page-shell tw:overflow-hidden" id="archives-page"
    data-per-page="<?=$perPage?>" data-query-year="<?=Theme::enabled()?'year':'sl_year'?>" data-initial-year="all" data-initial-page="1"
    data-label-error="<?=Theme::e(Theme::t('archiveLoadError'))?>"
    data-label-empty="<?=Theme::e(Theme::t('archiveEmpty'))?>"
    data-label-uncategorized="<?=Theme::e(Theme::t('uncategorized'))?>"
    data-label-previous="<?=Theme::e(Theme::t('previousPageShort'))?>"
    data-label-next="<?=Theme::e(Theme::t('nextPageShort'))?>">
    <section class="archive-page-section archive-page-section-toolbar">
        <div class="archive-page-toolbar tw:justify-between">
            <div class="archive-page-years" role="group" aria-label="<?=Theme::e(Theme::t('archiveYearFilter'))?>">
                <button class="archive-year-button is-active" type="button" data-year="all" aria-pressed="true"><?=Theme::e(Theme::t('archiveAll'))?></button>
                <div id="archives-year-filter-list"></div>
            </div>
        </div>
    </section>
    <section class="archive-page-section archive-page-section-list" aria-live="polite">
        <div class="archive-page-list tw:grid" id="archives-page-list">
            <?php foreach($visible as $post):?>
                <a class="archive-item" href="<?=Theme::e($post['url'])?>"><time><?=Theme::e($post['dateLabel'])?></time> <?=Theme::e($post['title'])?></a>
            <?php endforeach;?>
            <?php if(!$visible):?><div class="archive-page-state"><?=Theme::e(Theme::t('archiveEmpty'))?></div><?php endif;?>
        </div>
    </section>
    <section class="archive-page-section archive-page-section-pagination">
        <nav id="pagination" aria-label="<?=Theme::e(Theme::t('paginationNavigation'))?>" class="tw:[#post_&]:overflow-hidden"><div class="pagination"></div></nav>
    </section>
</div>
<template id="archive-page-data"><?=Theme::json($data)?></template>
