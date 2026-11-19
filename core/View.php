<?php
namespace Solitude;

final class View
{
    private static array $taxonomyCache=[];
    public static function invalidateTaxonomy(): void { self::$taxonomyCache=[]; }
    public static function menu(array $items): void {
        $link=function(array $item,bool $child=false): void {
            $action=!empty($item['children'])?'noop':($item['action']??'');
            echo '<a class="site-page'.($child?' child':'').'" href="'.Theme::e($action?'#':Theme::url($item['url']??'#')).'"';
            if($action)echo ' data-solitude-action="'.Theme::e($action).'" data-solitude-prevent="true"';
            echo '>';
            if(!empty($item['icon']))echo '<i class="solitude '.Theme::e($item['icon']).'" aria-hidden="true"></i>';
            echo '<span>'.Theme::e($item['name']??'').'</span></a>';
        };
        foreach($items as $item){
            echo '<div class="menus_item">';$link($item);
            if(!empty($item['children'])){echo '<ul class="menus_item_child">';foreach($item['children'] as $child){echo '<li>';$link($child,true);echo '</li>';}echo '</ul>';}
            echo '</div>';
        }
    }
    public static function menus(): array {return (Theme::settings()['menus']??[])?:[['name'=>Theme::t('allPosts'),'url'=>'archives/'],['name'=>Theme::t('links'),'url'=>'links/'],['name'=>Theme::t('about'),'url'=>'about/']];}
    public static function tags($post): string {
        $html='';foreach($post->tags as $tag)$html.='<a class="article-meta__tags" href="'.Theme::e($tag['permalink']).'" data-solitude-action="noop" data-solitude-stop="true"><span class="tags-punctuation"><i class="solitude fas fa-hashtag"></i>'.Theme::e($tag['name']).'</span></a>';return $html;
    }
    public static function postTags($post,bool $counts=false): void {
        $all=$counts?self::taxonomy(Theme::publicPosts()):[];
        $map=array_column($all,'count','mid');
        foreach($post->tags as $tag){
            echo '<a class="post-meta__tags" href="'.Theme::e($tag['permalink']).'"><span class="tags-name tags-punctuation"><i class="solitude fas fa-hashtag"></i>'.Theme::e($tag['name']);
            if($counts)echo '<span class="tagsPageCount">'.($map[$tag['mid']]??0).'</span>';
            echo '</span></a>';
        }
    }
    public static function card($post,int $index=0): void {Theme::render('card',compact('post','index'));}
    public static function pagination(int $current,int $total,string $base): void {
        if($total<2)return;
        $href=fn($page)=>Theme::e(Theme::url($page===1?$base:trim($base,'/').'/page/'.$page.'/'));
        echo '<nav id="pagination" aria-label="'.Theme::e(Theme::t('paginationNavigation')).'"><div class="pagination">';
        if($current>1)echo '<a class="extend prev" href="'.$href($current-1).'" aria-label="'.Theme::e(Theme::t('previousPage')).'"><i class="fas fa-chevron-left" aria-hidden="true"></i><span class="pagination_tips_prev">'.Theme::e(Theme::t('previousPageShort')).'</span></a>';
        $numbers=$total<=7?range(1,$total):($current<=4?array_merge(range(1,5),[$total]):($current>=$total-3?array_merge([1],range($total-4,$total)):array_merge([1],range($current-1,$current+1),[$total])));$last=0;
        foreach($numbers as $page){
            if($last&&$page-$last>1)echo '<span class="page-gap" aria-hidden="true">…</span>';
            echo $page===$current?'<span class="page-number current" aria-current="page">'.$page.'</span>':'<a class="page-number" href="'.$href($page).'">'.$page.'</a>';
            $last=$page;
        }
        if($current<$total)echo '<a class="extend next" href="'.$href($current+1).'" aria-label="'.Theme::e(Theme::t('nextPage')).'"><span class="pagination_tips_next">'.Theme::e(Theme::t('nextPageShort')).'</span><i class="fas fa-chevron-right" aria-hidden="true"></i></a>';
        echo '</div></nav>';
    }
    public static function nativePagination($archive): void {
        if($archive->getTotalPage()<2)return;
        ob_start();$archive->pageNav(Theme::t('previousPageShort'),Theme::t('nextPageShort'),3,'…',['itemTag'=>'span','textTag'=>'span']);$html=ob_get_clean();
        $document=new \DOMDocument('1.0','UTF-8');$previous=libxml_use_internal_errors(true);$document->loadHTML('<?xml encoding="UTF-8">'.$html);libxml_clear_errors();libxml_use_internal_errors($previous);
        $root=$document->getElementsByTagName('ol')->item(0);if(!$root)return;
        echo '<nav id="pagination" aria-label="'.Theme::e(Theme::t('paginationNavigation')).'"><div class="pagination">';
        foreach($root->childNodes as $item){if(!$item instanceof \DOMElement)continue;$anchor=$item->getElementsByTagName('a')->item(0);$class=$item->getAttribute('class');$text=trim($item->textContent);
            if($class==='current'){echo '<span class="page-number current" aria-current="page">'.Theme::e($text).'</span>';continue;}
            if($anchor){$url=Theme::e($anchor->getAttribute('href'));
                if($class==='prev'||$class==='next'){echo '<a class="extend '.$class.'" href="'.$url.'" aria-label="'.Theme::e(Theme::t($class==='prev'?'previousPage':'nextPage')).'">';if($class==='prev')echo '<i class="fas fa-chevron-left" aria-hidden="true"></i>';echo '<span class="pagination_tips_'.$class.'">'.Theme::e($text).'</span>';if($class==='next')echo '<i class="fas fa-chevron-right" aria-hidden="true"></i>';echo '</a>';}
                else echo '<a class="page-number" href="'.$url.'">'.Theme::e($text).'</a>';
            }else echo $class==='current'?'<span class="page-number current" aria-current="page">'.Theme::e($text).'</span>':'<span class="page-gap" aria-hidden="true">'.Theme::e($text).'</span>';
        }
        echo '</div></nav>';
    }
    public static function categoryBar(bool $home=true): void {
        $categories=self::taxonomy(Theme::publicPosts(),'categories');
        echo '<div class="home-category-bar category-in-bar"><div id="category-bar"><div class="category-bar-items'.($home?' home':'').'" id="category-bar-items">';
        foreach([['category-bar-home','home','star',Theme::url(),$home],['archives','allPosts','book',Theme::url('archives/'),!$home]] as $order=>[$id,$label,$icon,$url,$selected])echo '<div class="category-bar-item'.($selected?' select':'').'" id="'.$id.'" data-default-order="'.$order.'"><a href="'.Theme::e($url).'"><span class="category-bar-link-icon tw:inline-flex tw:items-center tw:justify-center"><i class="solitude fas fa-'.$icon.'"></i></span><span>'.Theme::e(Theme::t($label)).'</span></a></div>';
        foreach($categories as $index=>$category)echo '<div class="category-bar-item" id="category-'.Theme::e($category['name']).'" data-default-order="'.($index+2).'"><a href="'.Theme::e($category['permalink']).'">'.Theme::e($category['name']).'</a></div>';
        echo '</div><div class="category-bar-more-group">';
        if(count($categories)>5)echo '<button class="category-bar-next" id="category-bar-next" type="button" data-solitude-action="scrollCategoryBarToRight" aria-label="'.Theme::e(Theme::t('more')).'"><i class="solitude fas fa-angles-right"></i></button>';
        echo '<a class="category-bar-more" href="'.Theme::e(Theme::url('categories/')).'">'.Theme::e(Theme::t('more')).'</a></div></div></div>';
    }
    public static function sortItem($post): void {
        echo '<div class="article-sort-item"><a class="article-sort-item-img" href="'.Theme::e(Theme::permalink($post)).'"><img src="'.Theme::e(Theme::cover($post)).'" alt="'.Theme::e($post->title).'" loading="lazy" decoding="async"></a><div class="article-sort-item-info"><time datetime="'.gmdate('c',$post->created).'">'.gmdate('Y-m-d',$post->created+(int)Theme::options()->timezone).'</time><a class="article-sort-item-title" href="'.Theme::e(Theme::permalink($post)).'">'.Theme::e($post->title).'</a>';
        if($post->tags){echo '<div class="article-sort-item-tags">';foreach($post->tags as $tag)echo '<a class="article-meta__tags" href="'.Theme::e($tag['permalink']).'">#'.Theme::e($tag['name']).'</a>';echo '</div>';}
        echo '</div></div>';
    }
    public static function siteInfo(array $posts): void {
        $info=Theme::get('aside.siteinfo');
        if($info['postcount'])echo '<div class="webinfo-item"><div class="item-name">'.Theme::e(Theme::t('posts')).'</div><div class="item-count">'.count($posts).'</div></div>';
        if($info['runtimeenable'])echo '<div class="webinfo-item"><div class="item-name">'.Theme::e(Theme::t('uptime')).'</div><div class="item-count runtimeshow"></div></div>';
        if($info['updatetime']){$updated=max(array_map(fn($post)=>(int)$post->modified,$posts)?:[time()]);echo '<div class="webinfo-item"><div class="item-name">'.Theme::e(Theme::t('lastUpdated')).'</div><time class="item-count" datetime="'.gmdate('c',$updated).'">'.Theme::e(self::dateLabel($updated)).'</time></div>';}
        if($info['wordcount'])echo '<div class="webinfo-item"><div class="item-name">'.Theme::e(Theme::t('totalWords')).'</div><div class="item-count">'.array_sum(array_map(fn($post)=>self::wordCount($post->content),$posts)).'</div></div>';
    }
    public static function headingContent(string $html): array {
        $headings=[];$seen=[];$html=preg_replace_callback('~<h([1-6])([^>]*)>(.*?)</h\1>~s',function($m)use(&$headings,&$seen){$text=html_entity_decode(strip_tags($m[3]),ENT_QUOTES,'UTF-8');preg_match('/\bid="([^"]+)"/',$m[2],$id);$slug=$id[1]??trim(preg_replace('/[^\pL\pN]+/u','-',mb_strtolower($text)),'-');$slug=$slug?:'section';$n=$seen[$slug]??0;$seen[$slug]=$n+1;if($n)$slug.='-'.$n;$headings[]=['depth'=>(int)$m[1],'slug'=>$slug,'text'=>$text];return '<h'.$m[1].preg_replace('/\s+id="[^"]*"/','',$m[2]).' id="'.Theme::e($slug).'">'.$m[3].'</h'.$m[1].'>';},$html);return [$html,$headings];
    }
    public static function wordCount(string $html): int {
        $plain=trim(html_entity_decode(strip_tags(preg_replace('~<(script|style)\b[^>]*>.*?</\1>~is','',$html)),ENT_QUOTES,'UTF-8'));
        preg_match_all('~<pre\b([^>]*)>\s*<code\b([^>]*)>~i',$html,$blocks,PREG_SET_ORDER);
        foreach($blocks as $block){
            if(str_contains($block[1],'chartjs-src'))continue;
            preg_match('~data-language="([^"]+)"|class="(?:lang-|language-)([^" ]+)~',$block[1].' '.$block[2],$language);
            $plain.=' '.(($language[1]??'')?:($language[2]??'text')).' '.Theme::t('codeExpand');
            if(Theme::get('highlight.copy',true))$plain.=' '.Theme::t('codeCopy');
        }
        if(trim($plain)==='')return 0;
        return str_starts_with(Theme::locale(),'zh')?preg_match_all('/[\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}]|[^\s\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}]+/u',$plain):count(preg_split('/\s+/u',trim($plain),-1,PREG_SPLIT_NO_EMPTY));
    }
    public static function taxonomy(array $posts,string $type='tags'): array {
        $key=$type.':'.implode(',',array_map('spl_object_id',$posts));
        if(array_key_exists($key,self::$taxonomyCache))return self::$taxonomyCache[$key];
        $groups=[];
        foreach($posts as $post)foreach($post->{$type} as $term){$id=(int)$term['mid'];$groups[$id]??=array_merge($term,['count'=>0]);$groups[$id]['count']++;}
        $groups=array_values($groups);usort($groups,fn($a,$b)=>($b['count']<=>$a['count'])?:strcmp($a['name'],$b['name']));return self::$taxonomyCache[$key]=$groups;
    }
    public static function toc(array $headings): void {
        $index=0;$render=function(int $parent)use(&$render,&$index,$headings){
            while($index<count($headings)&&$headings[$index]['depth']>$parent){
                $heading=$headings[$index++];echo '<li class="toc-item toc-level-'.$heading['depth'].'"><a class="toc-link" href="#'.Theme::e($heading['slug']).'"><span class="toc-text">'.Theme::e($heading['text']).'</span></a>';
                if($index<count($headings)&&$headings[$index]['depth']>$heading['depth']){echo '<ol class="toc-child">';$render($heading['depth']);echo '</ol>';}
                echo '</li>';
            }
        };$render(0);
    }
    public static function dateLabel(int $timestamp): string {
        $format=match(Theme::locale()){'en'=>'m/d/Y','es'=>'d/m/Y',default=>'Y/m/d'};
        return gmdate($format,$timestamp+(int)Theme::options()->timezone);
    }
    public static function archiveData(array $posts): array {
        $data=[];
        $dateFormat=match(Theme::locale()){'en'=>'F j, Y','es'=>'d/m/Y',default=>'Y年n月j日'};
        foreach($posts as $post)$data[]=[
            'title'=>$post->title,'url'=>Theme::permalink($post),'cover'=>Theme::cover($post),
            'primaryCategory'=>$post->categories[0]['name']??Theme::t('uncategorized'),
            'dateLabel'=>gmdate($dateFormat,(int)$post->created+(int)Theme::options()->timezone),
            'year'=>gmdate('Y',(int)$post->created+(int)Theme::options()->timezone)
        ];
        return $data;
    }
    public static function commentCards(array $comments): void {
        foreach($comments as $comment){
            $post=\Utils\Helper::widgetById('contents',(int)$comment['cid']);$url=Theme::permalink($post).'#comment-'.$comment['coid'];
            echo '<div class="comment-card" title="'.Theme::e($comment['title']).'" data-solitude-action="navigateTo" data-solitude-url="'.Theme::e($url).'" role="link" tabindex="0"><div class="comment-info"><img class="nolazyload" src="'.Theme::e(Theme::avatar($comment['mail']??'')).'" alt="'.Theme::e($comment['author']).'" loading="lazy" decoding="async"><div><span class="comment-user">'.Theme::e($comment['author']).'</span></div><time class="comment-time" datetime="'.gmdate('c',$comment['created']).'">'.gmdate('n/j',(int)$comment['created']+(int)Theme::options()->timezone).'</time></div><div class="comment-content">'.Theme::e(mb_substr(strip_tags($comment['text']),0,150)).'</div><div class="comment-title"><i class="solitude fas fa-comment"></i> '.Theme::e($comment['title']).'</div></div>';
        }
        if(!$comments)echo Theme::e(Theme::t('commentsEmpty'));
    }
    public static function commentWall(bool $wall=false): void {
        echo '<div class="console_recentcomments recent-comments-list" data-comment-provider="'.Theme::e(Theme::get('comment.use')).'" aria-live="polite" aria-busy="false">';
        self::commentCards(Theme::comments());echo '</div>';
    }
}
