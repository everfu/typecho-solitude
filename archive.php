<?php
if(!defined('__TYPECHO_ROOT_DIR__'))exit;
use Solitude\Theme;
use Solitude\View;
require_once __DIR__.'/functions.php';require_once __DIR__.'/core/View.php';
$term=$this->is('category')||$this->is('tag');
ob_start();$this->archiveTitle(['category'=>'%s','tag'=>'%s','search'=>Theme::t('search').'：%s','author'=>Theme::t('author').'：%s'],'','');$title=ob_get_clean();
Theme::render('header',['archive'=>$this,'type'=>$term?'term':'archive','title'=>$title]);
?>
<main class="layout tw:[.layout&]:relative" id="content-inner"><div id="<?=$term?'tag':'archive'?>">
<?php if($term):?><div class="article-sort-title"><?=Theme::e($title)?></div><div class="article-sort">
<?php $i=0;while($this->next()):$i++;View::sortItem($this);endwhile;?>
</div><?php else:?><header class="page-title-wrap"><h1 class="page-title"><?=Theme::e($title)?></h1></header><div class="recent-posts" id="recent-posts"><?php $i=0;while($this->next())View::card($this,$i++);if(!$i)echo '<p class="empty-state">'.Theme::e(str_replace('${query}',$title,Theme::t('searchEmpty'))).'</p>';?></div><?php endif;?>
<?php View::nativePagination($this);?>
</div><?php Theme::render('aside',['scope'=>'page']);?></main><?php Theme::render('footer',['archive'=>$this]);?>
