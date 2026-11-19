<?php
use Solitude\Theme;
use Solitude\View;
require_once __DIR__.'/../core/View.php';
$scope=$scope??'page';
$allPosts=Theme::publicPosts();
$posts=$scope==='home'?array_values(array_filter($allPosts,fn($post)=>Theme::field($post,'home','1')!=='0')):$allPosts;
$tags=View::taxonomy($allPosts);
$groups=[Theme::get('aside.'.$scope.'.noSticky','about'),Theme::get('aside.'.$scope.'.Sticky','allInfo')];
$seen=[];
?>
<aside class="aside-content tw:flex tw:flex-col" id="aside-content">
    <?php foreach($groups as $index=>$names):?>
    <?php if($index===1):?><div class="sticky_layout tw:flex tw:flex-col">
        <?php if(!empty($headings)&&Theme::get('aside.toc.'.($scope==='post'?'post':'page'),true)):?>
        <div class="card-widget" id="card-toc"><div class="item-headline"><i class="solitude fas fa-bars"></i><span id="toc-title"><?=Theme::e(Theme::t('toc'))?></span><button class="toc-close" type="button" aria-label="<?=Theme::e(Theme::t('close'))?>"><i class="fas fa-xmark" aria-hidden="true"></i></button></div>
            <div class="toc-content" id="toc-content"><ol class="toc"><?php View::toc($headings);?></ol></div>
        </div>
        <?php endif;?>
    <?php endif;?>
    <?php foreach(explode(',',$names) as $module):$module=trim($module);if(isset($seen[$module]))continue;$seen[$module]=true;
        Theme::render('aside-module',compact('module','scope','posts','allPosts','tags'));
    endforeach;?>
    <?php if($index===1):?></div><?php endif;?>
    <?php endforeach;?>
</aside>
