<?php
if(!defined('__TYPECHO_ROOT_DIR__'))exit;
use Solitude\Theme;use Solitude\View;
require_once __DIR__.'/functions.php';require_once __DIR__.'/core/View.php';
$type=$slPageType??'page';Theme::render('header',['archive'=>$this,'type'=>$type,'title'=>$this->title]);
$asideDefault=in_array($type,['page','archives'],true)?'1':'0';
$aside=in_array($type,['page','archives','message','recentcomment'],true)&&Theme::field($this,'aside',$asideDefault)!=='0';
$ordinary=$type==='page'||($type==='music'&&!Theme::get('music.enable'));
[$html,$headings]=View::headingContent($this->content);
if($type==='archives'&&!$this->hidden){
    echo '<main class="layout layout--archive tw:[.layout&]:flex-col tw:[.layout&]:relative" id="content-inner">';
    View::categoryBar(false);
    echo '<div class="archive-layout-row tw:flex tw:items-start"><div id="page">';
    $archive=$this;include __DIR__.'/pages/archives.php';
    echo '</div>';
    if($aside)Theme::render('aside',['scope'=>'page']);
    echo '</div></main>';
    Theme::render('footer',['archive'=>$this]);
    return;
}
?><main class="layout <?=$aside?'':'hide-aside'?> tw:[.layout&]:relative" id="content-inner"><div id="page">
<?php if($ordinary):?><header class="page-title-wrap"><h1 class="page-title"><?=Theme::e($this->title)?></h1><?php $description=Theme::field($this,'description','');if($description):?><p><?=Theme::e($description)?></p><?php endif;?></header><?php endif;?>
<?php if($this->hidden):?><article class="article-container"><?=$html?></article><?php else:?>
<?php if($ordinary):?><article class="post-content article-container" data-sl-math><?=$html?></article><?php elseif(trim(strip_tags($html))&&!in_array($type,['about','links'],true)):?><article class="post-content article-container"><?=$html?></article><?php endif;?><?php if($type!=='page'):$archive=$this;include __DIR__.'/pages/'.$type.'.php';endif;?>
<?php if(in_array($type,['page','links','kit','message'],true))Theme::render('comments',['archive'=>$this]);endif;?></div><?php if($aside)Theme::render('aside',['scope'=>'page','headings'=>$headings]);?></main><?php Theme::render('footer',['archive'=>$this]); ?>
