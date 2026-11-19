<?php
use Solitude\Theme;
use Solitude\View;
$taxonomy=($slPageType??'categories')==='categories'?'categories':'tags';
$groups=View::taxonomy(Theme::publicPosts(),$taxonomy);
?>
<h1 class="page-title"><?=Theme::e($archive->title)?></h1>
<div class="category-lists"><div class="tag-cloud-list is-center">
<?php foreach($groups as $group):?><a href="<?=Theme::e($group['permalink'])?>" title="<?=Theme::e($group['name'])?>"><span class="tags-punctuation"><i class="solitude fas fa-hashtag"></i><?=Theme::e($group['name'])?><span class="tagsPageCount"><?=$group['count']?></span></span></a><?php endforeach;?>
</div></div>
