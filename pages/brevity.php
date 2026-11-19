<?php
use Solitude\Theme;
use Solitude\View;
if(!Theme::get('brevity.enable'))return;
$data=Theme::dataset('brevity');$header=$data['header']??[];
$items=array_slice($data['items'],0,(int)Theme::get('brevity.strip',30));
$style=(int)Theme::get('brevity.style',1);
$author=Theme::settings()['author']??'Solitude';
?>
<header class="author-content author-content-item single sharePage brevity-header">
    <img class="brevity-header-cover" src="<?=Theme::e(Theme::media($header['cover']??'/img/demo/cover-legacy-url-v2.webp'))?>" alt="" fetchpriority="high">
    <div class="card-content">
        <div class="author-content-item-tips"><?=Theme::e($header['description']??Theme::t('brevityDescription'))?></div>
        <h1 class="author-content-item-title"><?=Theme::e(Theme::t('brevity'))?></h1>
        <div class="content-bottom"><div class="tips"><?=Theme::e($author)?></div></div>
        <div class="banner-button-group"><a class="banner-button" href="<?=Theme::e(Theme::url($header['button']['link']??'/archives/'))?>" aria-label="<?=Theme::e($header['button']['text']??Theme::t('brevityReadMore'))?>"><i class="solitude fas fa-circle-chevron-right" aria-hidden="true"></i><span class="banner-button-text"><?=Theme::e($header['button']['text']??Theme::t('brevityReadMore'))?></span></a></div>
    </div>
</header>
<solitude-brevity id="bber" data-style="<?=$style?>" class="tw:block"><section class="timeline page-1"><ul class="list waterfall">
<?php foreach($items as $item):$date=str_replace(' ','T',$item['date']);?>
<li class="item">
    <?php if($style===2):?><div class="meta"><img class="avatar" src="<?=Theme::e(Theme::get('aside.my_card.author.img'))?>" alt=""><div class="info"><span class="bber_nick"><?=Theme::e($author)?></span><time class="bber_date" datetime="<?=Theme::e($date)?>" title="<?=Theme::e($item['date'])?>"><?=Theme::e(View::dateLabel(strtotime($date)))?></time></div></div><?php endif;?>
    <div class="bber-content"><p class="datacont"><?=Theme::e($item['content'])?></p>
        <?php if(!empty($item['image'])):?><div class="bber-content-img"><?php foreach(is_array($item['image'])&&array_is_list($item['image'])?$item['image']:[$item['image']] as $image):?><img src="<?=Theme::e(Theme::media(is_string($image)?$image:$image['url']))?>" alt="<?=Theme::e(is_string($image)?$item['content']:($image['alt']??$item['content']))?>" loading="lazy" decoding="async"><?php endforeach;?></div><?php endif;?>
    </div>
    <?php if(Theme::get('brevity.music')&&!empty($item['aplayer'])):?><div class="bber-music"><solitude-meting server="<?=Theme::e($item['aplayer']['server'])?>" type="song" id="<?=Theme::e($item['aplayer']['id'])?>" mutex="true" preload="none" theme="var(--efu-main)" data-lrctype="0"></solitude-meting></div><?php endif;?>
    <?php if(!empty($item['video'])):?><div class="bber-video">
        <?php if(!empty($item['video']['player'])):?><video src="<?=Theme::e(Theme::media($item['video']['player']))?>" controls preload="metadata"></video><?php endif;?>
        <?php if(!empty($item['video']['bilibili'])):?><iframe src="https://player.bilibili.com/player.html?autoplay=0&amp;bvid=<?=rawurlencode($item['video']['bilibili'])?>" title="Bilibili video" loading="lazy" allowfullscreen></iframe><?php endif;?>
    </div><?php endif;?>
    <?php if($style===1||!empty($item['location'])||!empty($item['link'])):?><hr><div class="bber-bottom"><div class="bber-info">
        <?php if($style===1):?><div class="bber-info-time"><i class="solitude fas fa-calendar-day"></i><time datetime="<?=Theme::e($date)?>" title="<?=Theme::e($item['date'])?>"><?=Theme::e(View::dateLabel(strtotime($date)))?></time></div><?php endif;?>
        <?php if(!empty($item['location'])):?><div class="bber-info-location"><i class="solitude fas fa-location-dot"></i><?=Theme::e($item['location'])?></div><?php endif;?>
        <?php if(!empty($item['link'])):?><a class="bber-content-link" href="<?=Theme::e(Theme::url($item['link']))?>"><i class="solitude fas fa-link"></i><?=Theme::e(Theme::t('brevityLink'))?></a><?php endif;?>
    </div></div><?php endif;?>
</li>
<?php endforeach;?>
</ul><div id="bber-tips"><?=Theme::e($items?str_replace('{count}',(string)count($items),Theme::t('brevityCount')):Theme::t('brevityEmpty'))?></div></section></solitude-brevity>
