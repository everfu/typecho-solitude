<?php
use Solitude\Theme;
use Solitude\View;
require_once __DIR__.'/../core/View.php';
$type=$type??'page';$title=$title??$archive->title??\Solitude\Settings::title();$isPost=$type==='post';
$color=isset($archive)?Theme::field($archive,'color',false):false;
if(!is_string($color)||!preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i',$color))$color=false;
elseif(strlen($color)===4)$color='#'.$color[1].$color[1].$color[2].$color[2].$color[3].$color[3];
$pageConfig=['is_post'=>$isPost,'is_page'=>!$isPost&&$type!=='solitude','is_home'=>$type==='solitude','page'=>$type,'toc'=>isset($archive)&&Theme::field($archive,'toc','1')!=='0'&&in_array($type,['post','page']),'comment'=>isset($archive)?(bool)$archive->allowComment:false,'color'=>$color];
$cfg=Theme::config();$runtime=Theme::runtime($type,$isPost);$canonical=$isPost?Theme::permalink($archive):Theme::url(ltrim(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)??'','/'));
$site=parse_url(Theme::options()->siteUrl);$origin=$site['scheme'].'://'.$site['host'].(isset($site['port'])?':'.$site['port']:'');$canonical=$isPost?Theme::permalink($archive):$origin.(parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/');
$description=isset($archive)?Theme::field($archive,'description',\Solitude\Settings::description()):\Solitude\Settings::description();
$vars=['theme-light'=>Theme::get('theme_color.light'),'theme-dark'=>Theme::get('theme_color.dark'),'nav-hover-light'=>Theme::get('theme_color.nav_hover_text.light','#fff'),'nav-hover-dark'=>Theme::get('theme_color.nav_hover_text.dark','#1b1c20'),'font-size'=>Theme::get('font.font_size','16px'),'code-font-size'=>Theme::get('font.code_font_size','14px'),'font-family'=>Theme::get('font.font_family'),'code-font-family'=>Theme::get('font.code_font_family'),'index-columns'=>Theme::get('index_post_list.column',2)];$style='';foreach($vars as $key=>$v)$style.='--solitude-'.$key.':'.$v.';';
?>
<!doctype html><html lang="<?=Theme::e(Theme::locale())?>" data-theme="light" style="<?=Theme::e($style)?>"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?=Theme::e($type==='solitude'?\Solitude\Settings::title():$title.' | '.\Solitude\Settings::title())?></title>
<meta name="description" content="<?=Theme::e($description)?>"><link rel="canonical" href="<?=Theme::e($canonical)?>"><link rel="icon" href="<?=Theme::e(Theme::media(Theme::get('site.icon')))?>">
<link rel="alternate" type="application/rss+xml" title="<?=Theme::e(\Solitude\Settings::title())?>" href="<?=Theme::e(Theme::options()->feedUrl)?>">
<meta property="og:title" content="<?=Theme::e($title)?>"><meta property="og:description" content="<?=Theme::e($description)?>"><meta property="og:url" content="<?=Theme::e($canonical)?>"><meta property="og:type" content="<?=$isPost?'article':'website'?>">
<?php if($isPost):?><meta property="og:image" content="<?=Theme::e(Theme::cover($archive))?>"><?php endif;?>
<?php if(Theme::get('pwa.enable')):?><link rel="manifest" href="<?=Theme::e(Theme::url('solitude/manifest.webmanifest'))?>"><meta name="theme-color" content="<?=Theme::e(Theme::get('pwa.theme_color',Theme::get('theme_color.light')))?>"><meta name="mobile-web-app-capable" content="yes">
<?php foreach(['apple_touch_icon'=>['apple-touch-icon','180x180'],'mask_icon'=>['mask-icon',''],'favicon_16_16'=>['icon','16x16'],'favicon_32_32'=>['icon','32x32'],'bookmark_icon'=>['bookmark','']] as $key=>[$rel,$sizes]):$icon=Theme::get('pwa.'.$key);if(!$icon)continue;?><link rel="<?=$rel?>" href="<?=Theme::e(Theme::media($icon))?>" <?=$sizes?'sizes="'.$sizes.'"':''?> <?=$rel==='mask-icon'?'color="'.Theme::e(Theme::get('pwa.theme_color')).'"':''?>><?php endforeach;endif;?>
<script>(function(){try{var v=JSON.parse(localStorage.getItem('theme')||'null'),m=v&&v.value;var d=<?=Theme::json(Theme::get('display_mode.type','auto'))?>;document.documentElement.dataset.theme=m||(d==='auto'?(matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'):d);}catch(e){}})();</script>
<?php if(isset($archive)){ob_start();$archive->header('description=&keywords=&generator=&template=&pingback=&xmlrpc=&wlw=&rss1=&rss2=&atom=&social=&commentReply=&antiSpam=');$nativeHead=ob_get_clean();echo preg_replace('~<link rel="canonical"[^>]*>~','',$nativeHead);}Theme::bundle('site'); ?>
<template id="site-config"><?=Theme::json($runtime)?></template><template id="config-diff"><?=Theme::json($pageConfig)?></template>
<?php foreach(array_merge(Theme::get('verify_site',[]),Theme::get('extends.head',[])) as $html)echo $html; $custom=Theme::settings()['custom_css']??'';if($custom)echo '<style>'.str_replace('</style','<\/style',$custom).'</style>'; ?>
</head><body id="body" class="<?=Theme::get('memorial.enable')&&in_array(gmdate('m-d',time()+(int)Theme::options()->timezone),Theme::get('memorial.date',[]),true)?'solitude-memorial':''?>" data-type="<?=Theme::e($type)?>" data-search="<?=Theme::e(Theme::get('search.type'))?>" data-aside-position="<?=Theme::e(Theme::get('aside.position',1))?>" data-index-layout="<?=Theme::e(Theme::get('index_post_list.direction','column'))?>">
<?php if(Theme::get('loading.fullpage')):?><div id="loading-box" aria-hidden="true"><img src="<?=Theme::e(Theme::media(Theme::get('site.icon')))?>" alt=""></div><?php endif;?>
<a class="skip-link" href="#content-inner"><?=Theme::e(Theme::t('startReading'))?></a>
<?php Theme::render('overlays'); ?>
<div id="body-wrap" class="<?=$type==='404'?'error':($isPost?'post':'page')?>"><header id="page-header" class="<?=$isPost&&Theme::field($archive,'not_cover','0')!=='1'?'post-bg':'not-top-img'?> nav-at-top">
<nav id="nav" class="show" data-solitude-action="onNavBlankClickCloseConsole" data-solitude-event="true"><div id="nav-group"><?php Theme::render('brand');?><div id="page-name-mask"><div id="page-name"><a id="page-name-text" data-solitude-action="toTop"><?=Theme::e($title)?></a></div></div><div id="menus"><div class="menus_items"><?php View::menu(View::menus());?></div></div><div id="nav-left"></div><?php Theme::render('nav-controls');?>
</div></nav>

<?php if($isPost&&Theme::field($archive,'not_cover','0')!=='1'):?><div class="post-cover-aside"><a class="post-cover-aside-link fancybox" href="<?=Theme::e(Theme::cover($archive))?>"><img id="post-cover" class="post-cover-aside-img nolazyload" src="<?=Theme::e(Theme::cover($archive))?>" alt="<?=Theme::e($archive->title)?>" loading="eager" fetchpriority="high" decoding="async"></a></div><?php Theme::render('post-meta',['post'=>$archive]);endif;?>
</header>
