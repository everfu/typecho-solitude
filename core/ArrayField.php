<?php
namespace Solitude;

use Typecho\Widget\Helper\Form\Element;
use Typecho\Widget\Helper\Layout;

/** Compound native form input. Named controls post PHP arrays directly to Typecho. */
final class ArrayField extends Element
{
    private int $sequence=0;
    public function input(?string $name=null,?array $options=null): ?Layout {
        $input=new Layout('div',['class'=>'sl-editor']);$this->container($input);return $input;
    }
    protected function inputValue($value) {
        // Compatibility for settings saved by the previous editor, never a text input.
        if(is_string($value))$value=json_decode($value,true)??[];
        $value=self::decode($this->name,(array)$value);
        $this->value=$value; // Native Form must recognize compound requests as arrays.
        $this->sequence=0;
        $this->input->html($this->renderValue($this->name,$value,self::prototype($this->name),'',0));
    }
    public static function caption(string $key): string {
        if(str_starts_with($key,'sl_'))$key=match($key){
            'sl_menus'=>'导航菜单',
            'sl_navgroup'=>'快捷导航分组',
            'sl_nav_custom'=>'导航入口',
            'sl_cover'=>'image',
            'sl_share_list'=>'分享服务',
            'sl_beian'=>'备案',
            'sl_footer'=>'链接',
            'sl_author_social'=>'社交链接',
            'sl_author_greetings'=>'分时问候',
            'sl_author_witty'=>'问候文字',
            'sl_recommendations'=>'推荐文章',
            'sl_statistics'=>'统计脚本',
            'sl_algolia'=>'Algolia 搜索',
            'sl_docsearch'=>'DocSearch 搜索',
            'sl_music'=>'音乐馆',
            'sl_capsule'=>'音乐胶囊',
            'sl_keyboard_list'=>'快捷键',
            'sl_rightmenu_items'=>'菜单入口',
            'sl_aside_tag_highlights'=>'高亮标签',
            'sl_option_hometop_banner_icon'=>'横幅图标',
            'sl_option_hometop_group'=>'首页推荐分组',
            'sl_option_page_default_cover'=>'页面封面',
            'sl_option_right_menu_exclude'=>'排除页面',
            'sl_option_footer_information_left'=>'页脚左侧信息',
            'sl_option_footer_group'=>'页脚导航分组',
            'sl_option_footer_links'=>'页脚快捷链接',
            'sl_option_memorial_date'=>'纪念日日期',
            'sl_option_search_tags'=>'搜索推荐标签',
            'sl_option_verify_site'=>'站点验证',
            'sl_option_extends_head'=>'页头代码',
            default=>substr($key,3)
        };
        static $labels=[
            'mutex'=>'与其他播放器互斥','order'=>'播放顺序','hits'=>'搜索结果','per_page'=>'每页数量',
            'placeholder'=>'占位提示','option'=>'扩展选项','field'=>'数据字段','fallback'=>'备用内容',
            'map_light'=>'浅色地图','map_dark'=>'深色地图','statistics'=>'统计信息',
            'intro'=>'自我介绍','skills_career'=>'技能与经历','personality_photo'=>'性格与照片','values'=>'座右铭与专长','interests'=>'兴趣','stats_profile'=>'统计与资料','story'=>'故事','game'=>'游戏','media'=>'影音','entries'=>'链接条目','value'=>'内容','name'=>'名称','title'=>'标题','description'=>'介绍','desc'=>'说明','descr'=>'介绍','content'=>'正文','label'=>'标签文字','url'=>'链接','link'=>'链接','icon'=>'图标','image'=>'图片','img'=>'图片','avatar'=>'头像','background'=>'背景图片','cover'=>'封面','color'=>'颜色','enable'=>'开启','enabled'=>'开启','external'=>'新窗口打开','text'=>'文字','type'=>'类型','action'=>'交互动作','id'=>'标识','children'=>'子菜单','items'=>'条目','groups'=>'分组','sections'=>'内容区块','date'=>'日期与时间','location'=>'位置','class_name'=>'分组名称','class_desc'=>'分组介绍','link_list'=>'友链列表','topimg'=>'顶部图片','tag'=>'标签','specification'=>'型号 / 规格','button'=>'按钮','greeting'=>'问候语','name_prefix'=>'姓名前缀','pursuit'=>'追求','lines'=>'文字行','rotating_words'=>'轮换文字','skills'=>'技能','career'=>'经历','summary'=>'概要','timeline'=>'时间线','start'=>'起始值','end'=>'结束值','start_label'=>'起点文字','end_label'=>'终点文字','track_label'=>'轨道文字','aria_label'=>'辅助说明','events'=>'事件','from'=>'开始','to'=>'结束','personality'=>'性格','photo'=>'照片','image_alt'=>'图片说明','alt'=>'图片说明','badge'=>'徽章','motto'=>'座右铭','prefix'=>'前缀','expertise'=>'专长','specialist'=>'领域','level'=>'程度','variant'=>'样式','heroes'=>'角色','mode'=>'模式','tooltip'=>'提示','right_text'=>'右侧文字','right_tip'=>'右侧提示','title_lines'=>'标题行','subtitle'=>'副标题','metrics'=>'指标','stats'=>'统计','profile'=>'个人资料','host'=>'网站地址','morning'=>'早上','noon'=>'中午','afternoon'=>'下午','night'=>'晚上','goodnight'=>'晚安','appId'=>'应用 ID','apiKey'=>'搜索密钥','indexName'=>'索引名称','server'=>'服务平台','volume'=>'音量','autoplay'=>'自动播放','modifier'=>'组合键','key'=>'按键','click'=>'动作或地址','qrcode'=>'二维码','aplayer'=>'音乐','audio'=>'音频','video'=>'视频','player'=>'播放器','meting'=>'歌单','music'=>'音乐','capsule'=>'音乐胶囊','hometop'=>'首页推荐区','footer'=>'页脚','console'=>'中控台','envelope'=>'留言墙','highlight'=>'代码高亮','expire'=>'文章过期提示','copyright'=>'版权信息','right_menu'=>'右键菜单','keyboard'=>'快捷键','font'=>'字体','theme_color'=>'主题色','display_mode'=>'显示模式','lazyload'=>'图片懒加载','related_post'=>'相关文章','recent_comments'=>'最近评论','pwa'=>'应用清单','links'=>'链接','information'=>'底部信息','left'=>'左侧','right'=>'右侧','group'=>'分组','randomlink'=>'随机友链','count'=>'显示计数','sidebar'=>'侧栏评论','newest_comment'=>'最新评论','limit'=>'数量上限','storage'=>'缓存时长','loop'=>'循环','hover'=>'悬停暂停','speed'=>'速度','newestPost'=>'最新文章','banner'=>'横幅','max_height'=>'最大高度','line_numbers'=>'显示行号','copy'=>'复制按钮','themes'=>'配色','light'=>'浅色','dark'=>'深色','tip'=>'提示','position'=>'位置','time'=>'时长','day'=>'天数','style'=>'样式','buttonText'=>'按钮文字','author'=>'作者','since'=>'开始年份','leftend'=>'左下文字','rightend'=>'右下文字','rightbtn'=>'按钮文字','rightbtnlink'=>'按钮链接','iconfont'=>'图标字体','highlight_list'=>'高亮条目','recommendList'=>'推荐列表','vague'=>'淡化未选中目录','preload'=>'预加载','path'=>'路径','errorimg'=>'失败占位图片','head'=>'页头代码','body'=>'页尾代码','css'=>'样式文件','js'=>'脚本文件','size'=>'大小','alignment'=>'对齐','ctrlOriginalMenu'=>'按住 Ctrl 使用浏览器菜单','exclude'=>'排除页面','message'=>'留言','middle'=>'中部','layout'=>'布局','view'=>'显示','friendly'=>'友好提示','article'=>'文章','post'=>'文章','page'=>'页面'
        ];
        return match($key){'card'=>'卡片分组','item'=>'列表分组',default=>$labels[$key]??str_replace('_',' ',$key)};
    }
    private static function prototype(?string $name) {
        $defaults=json_decode(file_get_contents(__DIR__.'/../resources/defaults.json'),true);
        if(str_starts_with($name??'','sl_data_')){
            $data=json_decode(file_get_contents(__DIR__.'/../resources/data/'.substr($name,8).'.json'),true);
            if($name==='sl_data_brevity')$data['items'][]=['date'=>'','content'=>'','location'=>'','image'=>[''],'link'=>'','video'=>'','aplayer'=>['server'=>'netease','type'=>'song','id'=>'']];
            if($name==='sl_data_about')$data['sections'][]=['type'=>'stats_profile','statistics'=>['label'=>'','title'=>'','background'=>'','metrics'=>[['label'=>'','field'=>'posts','fallback'=>'']],'button'=>['text'=>'','url'=>'']], 'location'=>['title'=>'','map_light'=>'','map_dark'=>''],'profile'=>['title'=>'','items'=>[['icon'=>'','label'=>'','value'=>'']]]];
            return $data;
        }
        if(str_starts_with($name??'','sl_extra_')||str_starts_with($name??'','sl_compat_'))return [];
        if($name==='sl_menus')return [['name'=>'','url'=>'','icon'=>'','action'=>'','children'=>[['name'=>'','url'=>'','icon'=>'']]]];
        foreach(Settings::schema() as $field)if($field['name']===$name){$v=$defaults;foreach(explode('.',$field['path']??'') as $key)$v=is_array($v)?($v[$key]??[]):[];return $v;}
        return [];
    }
    private static function itemSample(string $key) {
        return match($key){
            'sl_navgroup','group'=>['name'=>'','url'=>'','icon'=>''],
            'sl_nav_custom'=>['id'=>'','name'=>'','url'=>'','icon'=>'','action'=>''],
            'sl_beian','sl_footer','sl_author_social','links'=>['name'=>'','url'=>'','icon'=>''],
            'sl_recommendations','recommendList'=>['title'=>'','url'=>'','cover'=>'','label'=>'','enable'=>true],
            'sl_keyboard_list'=>['name'=>'','modifier'=>'shift','key'=>'','action'=>'','url'=>''],
            'sl_rightmenu_items'=>['name'=>'','icon'=>'','id'=>'','click'=>''],
            'image','sl_cover','sl_share_list','sl_author_witty','sl_aside_tag_highlights','sl_statistics','lines','rotating_words','title_lines','exclude'=> '',
            default=>''
        };
    }
    private function scalar(string $name,$value,string $key,$sample=null): string {
        $id='sl-'.substr(hash('sha256',$name),0,16);$label=self::caption($key?:'value');$e=Theme::e(...);
        if(is_bool($value))return '<label class="sl-native-toggle" for="'.$id.'"><input type="hidden" name="'.$e($name).'[__sl_bool]" value="1"><input type="hidden" name="'.$e($name).'[value]" value="0"><input id="'.$id.'" type="checkbox" name="'.$e($name).'[value]" value="1"'.($value?' checked':'').'>'.$e($label).'</label>';
        $num=is_int($sample)||is_float($sample)||is_int($value)||is_float($value);
        $prefix=$num?'<input type="hidden" name="'.$e($name).'[__sl_number]" value="1">':'';
        $name=$num?$name.'[value]':$name;
        $long=in_array($key,['content','description','desc','descr','class_desc','summary','body','head'],true)||str_contains((string)$value,"\n");
        $input=$long?'<textarea id="'.$id.'" name="'.$e($name).'" rows="3">'.$e($value).'</textarea>':'<input id="'.$id.'" type="'.($num?'number':'text').'"'.($num?' step="any"':'').' name="'.$e($name).'" value="'.$e($value).'">';
        $options=match($key){'server'=>['netease'=>'网易云音乐','tencent'=>'QQ 音乐','kugou'=>'酷狗','kuwo'=>'酷我','baidu'=>'百度音乐'],'modifier'=>['mod'=>'⌘ / Ctrl','shift'=>'Shift'],'order'=>['list'=>'顺序播放','random'=>'随机播放'],default=>[]};
        if($key==='type'&&preg_match('/sl_(?:music|capsule)|\[aplayer\]/',$name))$options=['playlist'=>'歌单','song'=>'单曲','album'=>'专辑','artist'=>'歌手'];
        if($key==='type'&&preg_match('/^sl_data_links\[links\]\[[^\]]+\]\[type\]$/',$name))$options=['card'=>'卡片分组','item'=>'列表分组'];
        if($key==='type'&&preg_match('/^sl_data_about\[sections\]\[[^\]]+\]\[type\]$/',$name))return '<input type="hidden" name="'.$e($name).'" value="'.$e($value).'"><p class="sl-block-type">'.$e(self::caption((string)$value)).'区块</p>';
        if($key==='action')$options=[''=>'使用链接或子菜单','openSearch'=>'打开搜索','toggleTheme'=>'切换明暗模式','toggleConsole'=>'切换中控台','randomPost'=>'随机文章','switchHideAside'=>'显示或隐藏侧栏','musicToggle'=>'播放或暂停音乐'];
        if($options){if(!array_key_exists((string)$value,$options))$options[(string)$value]=(string)$value;$input='<select id="'.$id.'" name="'.$e($name).'">';foreach($options as $k=>$labelText)$input.='<option value="'.$e($k).'"'.((string)$value===(string)$k?' selected':'').'>'.$e($labelText).'</option>';$input.='</select>';}
        if(in_array($key,['image','img','avatar','background','cover','topimg','photo','map_light','map_dark','sticker'],true)&&!$long)$input=str_replace('<input ','<input data-sl-image ', $input);
        return $prefix.'<div class="sl-edit-row"><label for="'.$id.'">'.$e($label).'</label>'.$input.'</div>';
    }
    private function renderValue(string $name,$value,$sample,string $key,int $depth): string {
        if($depth>32)throw new \InvalidArgumentException('内容嵌套超过 32 层，无法安全编辑。');
        if(!is_array($value))return $this->scalar($name,$value,$key,$sample);
        $e=Theme::e(...);
        $map=in_array($key,['group'],true)||$name==='sl_navgroup';
        if($map){
            $entries=[];foreach($value as $k=>$v)$entries[]=['name'=>(string)$k,'entries'=>$v];
            $value=$entries;$sample=[['name'=>'','entries'=>[self::itemSample($key)]]];
            return '<input type="hidden" name="'.$e($name).'[__sl_map]" value="1">'.$this->renderList($name,$value,$sample,$key,$depth);
        }
        if(array_is_list($value))return $this->renderList($name,$value,$sample,$key?:$this->name,$depth);
        $out='<div class="sl-native-object">';
        // Retain every field already present, including optional fields added by a reference import.
        foreach($value as $k=>$v){$prototype=is_array($sample)?($sample[$k]??$v):$v;$out.='<div class="sl-object-field">';if(is_array($v))$out.='<details open><summary>'.$e(self::caption((string)$k)).'</summary>';$out.=$this->renderValue($name.'['.$k.']',$v,$prototype,(string)$k,$depth+1);if(is_array($v))$out.='</details>';$out.='</div>';}
        if(is_array($sample)&&!array_is_list($sample))foreach($sample as $k=>$prototype)if(!array_key_exists($k,$value)){
            $id='sl-opt-'.substr(hash('sha256',$name.'['.$k.']'),0,16);
            $out.='<details class="sl-optional-field"><summary><label for="'.$id.'"><input type="checkbox" id="'.$id.'" data-sl-enable-field> 配置'.$e(self::caption((string)$k)).'</label></summary><fieldset disabled>'.$this->renderValue($name.'['.$k.']',self::blank($prototype),$prototype,(string)$k,$depth+1).'</fieldset></details>';
        }
        return $out.'</div>';
    }
    private function renderList(string $name,array $value,$sample,string $key,int $depth): string {
        $e=Theme::e(...);$samples=[];
        foreach(is_array($sample)?$sample:[] as $item){$tag=is_array($item)?($item['type']??$item['variant']??'default'):'default';if(!isset($samples[$tag]))$samples[$tag]=$item;elseif(is_array($samples[$tag])&&is_array($item))$samples[$tag]=array_replace_recursive($item,$samples[$tag]);}
        foreach($value as $item){$tag=is_array($item)?($item['type']??$item['variant']??'default'):'default';if(!isset($samples[$tag]))$samples[$tag]=$item;elseif(is_array($samples[$tag])&&is_array($item))$samples[$tag]=array_replace_recursive($item,$samples[$tag]);}
        if(!$samples)$samples=['default'=>self::itemSample($key)];
        $out='<div class="sl-native-list" data-sl-list><input type="hidden" name="'.$e($name).'[__empty]" value="">';
        foreach($value as $i=>$item){$tag=is_array($item)?($item['type']??$item['variant']??'default'):'default';$out.=$this->listItem($name.'['.$i.']',$item,$samples[$tag]??$item,$key,$depth);}
        $out.='<div class="sl-list-add">';
        foreach($samples as $tag=>$item){$token='__SL_ROW_'.(++$this->sequence).'__';$blank=self::blank($item);$out.='<button type="button" data-sl-add="'.$token.'">添加'.($tag==='default'?'条目':' · '.$e(self::caption((string)$tag))).'</button><template data-sl-template="'.$token.'">'.$this->listItem($name.'['.$token.']',$blank,$item,$key,$depth).'</template>';}
        return $out.'</div></div>';
    }
    private static function blank($value) {
        if(!is_array($value))return is_bool($value)?false:(is_numeric($value)?0:'');
        if(array_is_list($value))return [];
        $out=[];foreach($value as $key=>$v)$out[$key]=in_array($key,['type','variant'],true)?$v:self::blank($v);return $out;
    }
    private function listItem(string $name,$value,$sample,string $key,int $depth): string {
        $label=Theme::e(self::caption(is_array($value)?($value['type']??$value['variant']??$key):$key));
        return '<fieldset class="sl-native-item" data-sl-item><legend>'.$label.'</legend><button type="button" class="sl-item-toggle" data-sl-collapse aria-expanded="true"><span class="sl-item-heading"><span data-sl-item-title>'.$label.'</span><span data-sl-item-summary></span></span><svg class="sl-item-chevron" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 6 4 4 4-4"/></svg></button><div class="sl-item-body">'.$this->renderValue($name,$value,$sample,is_array($value)?'':$key,$depth+1).'</div><div class="sl-array-actions"><button type="button" data-sl-move="up" aria-label="上移条目">上移</button><button type="button" data-sl-move="down" aria-label="下移条目">下移</button><button type="button" data-sl-remove>删除条目</button></div></fieldset>';
    }
    public static function valid(array $value,int $depth=0): bool {
        if($depth>32)return false;
        if($depth===0&&(isset($value['__sl_bool'])||isset($value['__sl_number'])))return false;
        if(isset($value['__sl_bool'])&&(!in_array($value['value']??null,['0','1'],true)||$value['__sl_bool']!=='1'))return false;
        if(isset($value['__sl_number'])&&($value['value']??'')!==''&&(!is_numeric($value['value'])||!is_finite((float)$value['value'])))return false;
        foreach($value as $key=>$item){
            if(in_array($key,['__proto__','constructor','prototype'],true))return false;
            if(is_array($item)){if(!self::valid($item,$depth+1))return false;}
            elseif(!is_scalar($item)&&$item!==null)return false;
            elseif(is_string($item)&&in_array($key,['url','link','src','image','img','avatar','cover','topimg','host'],true)&&preg_match('/^\s*(?:javascript|vbscript|data):/i',$item))return false;
        }
        return true;
    }
    public static function decode(?string $name,array $value): array {return self::clean($value);}
    public static function inputName(string $root,string $path,array $posted): string {
        foreach(explode('.',$path) as $key){
            if(isset($posted['__empty'])&&ctype_digit($key)){
                $keys=array_keys(array_diff_key($posted,array_flip(['__empty','__sl_map'])));$key=$keys[(int)$key]??$key;
            }
            $root.='['.$key.']';$posted=is_array($posted[$key]??null)?$posted[$key]:[];
        }
        return $root.(isset($posted['__sl_number'])?'[value]':'');
    }
    private static function clean($value) {
        if(!is_array($value))return is_string($value)?str_replace(["\r\n","\r"],"\n",$value):$value;
        if(isset($value['__sl_bool']))return ($value['value']??'0')==='1';
        if(isset($value['__sl_number']))return is_numeric($value['value']??null)?0+$value['value']:($value['value']??'');
        $map=isset($value['__sl_map']);$wasList=array_is_list($value)||isset($value['__empty']);
        unset($value['__empty'],$value['__sl_map']);$out=[];
        foreach($value as $key=>$item){if(in_array($key,['__proto__','constructor','prototype'],true))continue;$out[$key]=self::clean($item);}
        if($map){$mapped=[];foreach($out as $item)if(is_array($item)&&trim($item['name']??'')!=='')$mapped[trim($item['name'])]=$item['entries']??[];return $mapped;}
        return $wasList?array_values($out):$out;
    }
}
