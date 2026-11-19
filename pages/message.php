<?php
use Solitude\Theme;
if(!Theme::get('envelope.enable'))return;
$native=Theme::get('comment.use')==='native';$items=[];
if($native)foreach(Theme::comments(null) as $c){$post=\Utils\Helper::widgetById('contents',(int)$c['cid']);$items[]=['id'=>(string)$c['coid'],'nick'=>$c['author'],'participantKey'=>(string)$c['coid'],'content'=>strip_tags($c['text']),'url'=>Theme::permalink($post).'#comment-'.$c['coid'],'title'=>$c['title'],'avatar'=>Theme::avatar($c['mail']??''),'date'=>date('c',$c['created'])];}
?>
<section class="message-barrage-region" aria-label="<?=Theme::e(Theme::t('comments'))?>"><p id="message-barrage-status" class="sl-sr-only" role="status" aria-live="polite" aria-busy="true"><?=Theme::e(Theme::t('commentsLoading'))?></p><div class="switch_message" aria-controls="barrage"><button type="button" data-message-barrage="show" aria-pressed="true" disabled><span><?=Theme::e(Theme::t('showBarrage'))?></span></button><button type="button" data-message-barrage="hide" aria-pressed="false" disabled><span><?=Theme::e(Theme::t('hideBarrage'))?></span></button></div><div id="barrage" data-native-barrage aria-label="<?=Theme::e(Theme::t('comments'))?>" data-line="<?=Theme::e(Theme::get('envelope.line',10))?>" data-speed="<?=Theme::e(Theme::get('envelope.speed',20))?>" data-hover="<?=Theme::get('envelope.hover')?'true':'false'?>" data-loop="<?=Theme::get('envelope.loop')?'true':'false'?>"></div></section>
<?php if($native):?><template id="sl-native-wall-data"><?=Theme::json($items)?></template><?php endif;?>
