<?php
namespace Solitude;

use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Widget\Helper\Form\Element\Textarea;
use Typecho\Widget\Helper\Form\Element\Select;
use Utils\Helper;
require_once __DIR__.'/ArrayField.php';

final class Settings
{
    public static function groups(): array {
        return [
            'basic'=>['基础设置','compass',['identity'=>'站点信息','author'=>'作者资料','menus'=>'导航菜单']],
            'layout'=>['外观布局','palette',['appearance'=>'颜色与字体','home'=>'首页布局','aside'=>'侧栏模块','footer'=>'页脚与备案']],
            'post'=>['文章设置','book-open',['reading'=>'阅读信息','cover'=>'封面与取色','toc'=>'文章目录','copyright'=>'版权信息','share'=>'文章分享','related'=>'相关文章','components'=>'代码与内容组件']],
            'pages'=>['页面设置','file-lines',['default'=>'默认页面','archives'=>'归档与分类','links-display'=>'友链展示','brevity-display'=>'即刻展示','envelope'=>'留言板','recent-comments'=>'最近评论','error'=>'404 页面','initialize'=>'页面初始化']],
            'services'=>['功能服务','puzzle-piece',['search'=>'搜索','comments'=>'评论','music'=>'音乐','seo'=>'订阅与 SEO','pwa'=>'应用清单']],
            'interaction'=>['交互增强','hand-pointer',['rightmenu'=>'右键菜单','keyboard'=>'键盘快捷键','copy'=>'复制与署名','lightbox'=>'图片灯箱','translate'=>'简繁转换','console'=>'中控台与工具栏','effects'=>'其他效果']],
            'advanced'=>['高级维护','sliders',['custom'=>'自定义代码','images'=>'图片加载','resources'=>'资源地址','compat'=>'历史扩展','backup'=>'备份与恢复']]
        ];
    }
    public static function schema(): array {static $fields;return $fields??=require __DIR__.'/../resources/settings.php';}
    public static function workspace(array $groups,string $title='Solitude 设置',string $attributes=''): void {
        Theme::bundle('admin');
        echo '<div id="sl-settings" data-site-url="'.Theme::e(Theme::options()->siteUrl).'" data-content-url="'.Theme::e(self::contentUrl()).'" data-action="'.Theme::e(Helper::security()->getIndex('/action/solitude-x')).'" '.$attributes.'>';
        echo '<a class="sl-skip-link" href="#sl-content">跳到设置内容</a><header class="sl-settings-header"><div><h2 class="sl-workspace-title">'.Theme::e($title).'</h2><p id="sl-location">按页签管理主题设置</p></div><div class="sl-header-links"><a href="'.Theme::e(Theme::options()->adminUrl.'options-theme.php').'">主题设置</a><a href="'.Theme::e(self::contentUrl()).'">内容管理</a><a href="'.Theme::e(Theme::options()->siteUrl).'" target="_blank" rel="noopener">查看站点 ↗</a></div></header>';
        echo '<div class="sl-workspace"><nav id="sl-navigation" class="sl-main-nav" aria-label="设置分类"><div class="sl-primary-tabs">';
        foreach($groups as $group=>[$label,$icon,$tabs]){
            echo '<button type="button" data-sl-group="'.Theme::e($group).'" aria-expanded="false" aria-controls="sl-group-'.Theme::e($group).'"><span>'.Theme::e($label).'</span></button>';
        }
        echo '</div><div class="sl-section-nav" aria-label="设置子分类">';
        foreach($groups as $group=>[$label,$icon,$tabs]){
            echo '<div id="sl-group-'.Theme::e($group).'" class="sl-sub-nav" data-sl-sections="'.Theme::e($group).'" hidden>';
            foreach($tabs as $id=>$label)echo '<button type="button" data-sl-tab="'.Theme::e($id).'" data-sl-parent="'.Theme::e($group).'">'.Theme::e($label).'</button>';
            echo '</div>';
        }
        echo '</div></nav><section id="sl-content" class="sl-content" tabindex="-1" aria-label="设置内容"><div class="sl-search-bar"><label for="sl-search" class="sr-only">搜索设置</label><div class="sl-search-control"><input id="sl-search" type="search" placeholder="搜索设置名称或说明" autocomplete="off"><button type="button" data-sl-clear-search hidden>清空</button></div><p id="sl-search-status" role="status" hidden></p></div><p class="sl-search-empty" hidden>没有找到相关设置，试试其他关键词。</p></section></div></div>';
    }
    public static function contentUrl(?string $type=null): string {
        $file=in_array($type,Theme::DATA_TYPES,true)?$type:'panel';
        return Theme::options()->adminUrl.'extending.php?panel='.urlencode('SolitudeX/admin/'.$file.'.php');
    }
    public static function saveBar(bool $content=false): void {
        echo '<div class="sl-save-bar"><div class="sl-save-inner"><span role="status" id="sl-save-status">'.($content?'内容已保存':'设置已保存').'</span><div class="sl-save-actions"><button type="button" data-sl-reset>放弃修改</button><button type="button" class="sl-primary" data-sl-save>'.($content?'保存当前内容':'保存全部设置').'</button></div></div></div>';
    }
    public static function form($form): void {
        // A failed migration must stop rendering a form that would drop native content.
        Theme::migrateContent();
        $plugin=Theme::enabled();$disabled=$plugin?'':' disabled';
        $menuUpdated=$plugin&&class_exists(\TypechoPlugin\SolitudeX\Plugin::class)&&\TypechoPlugin\SolitudeX\Plugin::ensureContentPanel();
        self::workspace(self::groups(),'Solitude 设置',$menuUpdated?'data-menu-updated="1"':'');
        echo '<section class="sl-welcome sl-static" data-group="basic" data-tab="identity"><p>站点地址、时区与 RSS 名称在 <a href="'.Theme::e(Theme::options()->adminUrl.'options-general.php').'">Typecho 基本设置</a> 管理。</p>';
        if(!$plugin)echo '<p class="sl-plugin-state">内容管理、搜索与维护需要启用 SolitudeX。</p>';
        echo '</section>';
        echo '<section class="sl-static" data-group="pages" data-tab="default"><p>关于、友链、装备与即刻在 <a href="'.Theme::e(self::contentUrl()).'">内容管理</a> 编辑。</p></section>';
        echo '<section class="sl-static" data-group="pages" data-tab="initialize"><p>创建缺失的特色页面。</p><button type="button" data-sl-operation="initialize"'.$disabled.'>初始化特色页面</button><p role="status" data-sl-operation-status></p></section>';
        echo '<section class="sl-static" data-group="advanced" data-tab="resources"><p>资源地址需与对应文件类型和版本兼容。</p></section>';
        echo '<section class="sl-static" data-group="advanced" data-tab="compat"><p>保留历史扩展配置。</p></section>';
        echo '<section class="sl-static" data-group="advanced" data-tab="backup"><p>备份已保存的主题设置与四类页面内容。恢复前创建撤销快照。</p><button type="button" data-sl-operation="backup"'.$disabled.'>备份已保存配置</button> <button type="button" class="sl-danger" data-sl-operation="restore"'.$disabled.'>恢复备份</button> <button type="button" data-sl-operation="undo-restore"'.$disabled.'>撤销最近恢复</button><p role="status" id="sl-operation-status" data-sl-operation-status></p></section>';
        echo '<section class="sl-static" data-group="services" data-tab="seo"><p><a href="'.Theme::e(Theme::options()->feedUrl).'">RSS 订阅地址</a> · <a href="'.Theme::e(Theme::url('solitude/sitemap.xml')).'">站点地图</a></p><p>仅收录已发布的公开内容。</p></section>';
        $saved=Theme::settings();
        $posted=[];foreach(self::schema() as $definition)$posted[$definition['name']]=\Typecho\Request::getInstance()->get($definition['name']);
        $form->addInput(new \Typecho\Widget\Helper\Form\Element\Hidden('sl_native_config',null,'1'));
        foreach(self::schema() as $field){
            ['name'=>$name,'label'=>$label,'group'=>$group,'tab'=>$tab,'path'=>$path,'type'=>$type,'default'=>$default,'help'=>$help]=$field;
            $v=$path?Theme::get($path,$default):match($name){'sl_title'=>self::title(),'sl_description'=>self::description(),'sl_author'=>$saved['author']??$default,'sl_locale'=>Theme::locale(),'sl_menus'=>$saved['menus']??[],'sl_custom_css'=>$saved['custom_css']??'',default=>$default};
            if($type==='bool')$v=$v?'1':'0';
            $el=$type==='array'?new ArrayField($name,null,(array)$v,$label,$help):(in_array($type,['select','bool'])?new Select($name,$field['options'],$v,$label,$help):($type==='textarea'?new Textarea($name,null,$v,$label,$help):new Text($name,null,$v,$label,$help)));
            self::decorate($el,$group,$tab,$type);
            $el->setAttribute('data-sl-when',Theme::e(Theme::json($field['when'])));
            $el->setAttribute('data-sl-advanced',$field['advanced']?'1':'0');
            if($type==='array')self::arrayRule($form,$name,$group,$tab,$label.'无效，请检查必填内容与链接。');
            else {
                if(!empty($field['rules']['required'])||(in_array($type,['number','decimal','color'],true)&&self::active($field,$posted)))$el->addRule('required','请填写'.$label.'。');
                $el->addRule([self::class,'validPostedValue'],'请检查'.$label.'的格式或取值范围。',$name);
            }
            $form->addInput($el);
        }
        foreach(self::extras() as $key=>[$label,$group,$tab,$value]){
            $el=new ArrayField('sl_compat_'.$key,null,$value,$label,'');self::decorate($el,$group,$tab,'array');$el->setAttribute('data-sl-advanced','1');$form->addInput($el);
        }
        $complete=new \Typecho\Widget\Helper\Form\Element\Hidden('sl_complete',null,'1');
        self::decorate($complete,'basic','identity','validation');$complete->addRule('required','表单未完整提交，请提高服务器 max_input_vars 后重新保存。');$form->addInput($complete);
        $formId=md5(implode('"',array_keys($form->getInputs())));
        if(!\Typecho\Cookie::get('__typecho_form_record_'.$formId))foreach((array)json_decode(\Typecho\Cookie::get('__typecho_form_message_'.$formId)??'[]',true) as $name=>$message)if(isset($form->getInputs()[$name]))$form->getInput($name)->message($message);
        self::saveBar();
    }
    private static function arrayRule($form,string $name,string $group,string $tab,string $message): void {
        // Typecho's scalar validator calls strlen() for custom rules. Validate the
        // compound value through a scalar companion while retaining native saving.
        $el=new \Typecho\Widget\Helper\Form\Element\Hidden('sl_validation_'.$name,null,'1');
        self::decorate($el,$group,$tab,'validation');
        $el->addRule('required',$message);$el->addRule([self::class,'validPostedArray'],$message,$name);$form->addInput($el);
    }
    public static function active(array $field,array $values): bool {
        foreach($field['when'] as $name=>$value)if(!is_scalar($values[$name]??null)||(string)$values[$name]!==(string)$value)return false;
        return true;
    }
    public static function validateValue($value,array $field,array $values): bool {
        $type=$field['type'];$name=$field['name'];
        if($type==='array'){
            if(is_string($value))$value=json_decode($value,true);
            if(!is_array($value)||!ArrayField::valid($value))return false;
            $value=ArrayField::decode($name,$value);
            return $name!=='sl_menus'||self::validMenus($value);
        }
        if(is_array($value)||is_object($value))return false;
        if(in_array($type,['select','bool'],true))return array_key_exists((string)$value,$field['options']);
        if($type==='color'&&$value!==''&&!preg_match('/^#[0-9a-fA-F]{6}$/',(string)$value))return false;
        if($type==='modules')return self::validModules($value);
        if(!self::active($field,$values))return true;
        if(!empty($field['rules']['required'])&&trim((string)$value)==='')return false;
        if(in_array($type,['number','decimal'],true))return is_numeric($value)&&is_finite((float)$value)&&($type!=='number'||ctype_digit((string)$value))&&$value>=($field['rules']['min']??0)&&$value<=($field['rules']['max']??1000000000);
        return $type!=='color'||$value!=='';
    }
    public static function validPostedValue($value,string $name): bool {
        $request=\Typecho\Request::getInstance();$values=[];foreach(self::schema() as $field)$values[$field['name']]=$request->get($field['name']);
        foreach(self::schema() as $field)if($field['name']===$name)return self::validateValue($value,$field,$values);
        return false;
    }
    public static function validPostedArray($value,string $name): bool {return self::validPostedValue(\Typecho\Request::getInstance()->get($name,[]),$name);}
    private static function decorate($el,string $group,string $tab,string $type): void {
        $el->setAttribute('class','sl-field sl-type-'.$type);$el->setAttribute('data-group',$group);$el->setAttribute('data-tab',$tab);
    }
    public static function title(): string {return Theme::options()->sl_title??Theme::options()->title;}
    public static function description(): string {$fields=Theme::nativeSettings();return array_key_exists('sl_description',$fields)?(string)$fields['sl_description']:Theme::options()->description;}
    public static function extras(): array {
        $remaining=Theme::config();
        foreach(self::schema() as $field)if($field['path'])self::removePath($remaining,explode('.',$field['path']));
        unset($remaining['comment']['use'],$remaining['post']['award'],$remaining['post']['rss']);
        foreach(['algolia','comment_runtime','covercolor','feature_modules','friend_links','lang','localsearch','random_posts','randomlink','root','runtime'] as $key)unset($remaining[$key]);
        $out=[];
        foreach($remaining as $key=>$value)if($value!==[])$out[$key]=['历史扩展 · '.ArrayField::caption($key),'advanced','compat',is_array($value)?$value:['value'=>$value]];
        return $out;
    }
    private static function removePath(array &$data,array $keys): void {
        $key=array_shift($keys);if(!array_key_exists($key,$data))return;
        if(!$keys)unset($data[$key]);elseif(is_array($data[$key])){self::removePath($data[$key],$keys);if($data[$key]===[])unset($data[$key]);}
    }
    public static function record(array $fields): array {
        $theme=[];
        foreach($fields as $key=>$value) if((str_starts_with($key,'sl_extra_')||str_starts_with($key,'sl_compat_'))&&is_array($value)){
            $key=substr($key,str_starts_with($key,'sl_extra_')?9:10);$value=ArrayField::decode('sl_compat_'.$key,$value);
            $theme[$key]=array_keys($value)===['value']?$value['value']:$value;
        }
        foreach(self::schema() as $field) {
            ['name'=>$name,'path'=>$path,'type'=>$type]=$field;
            if(!array_key_exists($name,$fields))continue;
            $v=$fields[$name];if($v===null&&!in_array($type,['array','bool','number'],true))$v='';
            if($type==='array'){if(is_string($v))$v=json_decode($v,true)??[];$v=ArrayField::decode($name,(array)$v);}
            if($type==='bool')$v=(string)$v==='1';
            if($type==='number')$v=$v===null||$v===''?'':(int)$v;
            if($type==='decimal')$v=$v===null||$v===''?'':(float)$v;
            if($path)self::setPath($theme,$path,$v);
        }
        return ['version'=>2,'theme'=>$theme,'locale'=>$fields['sl_locale']??'zh-CN','author'=>$fields['sl_author']??'Solitude',
            'menus'=>ArrayField::decode('sl_menus',is_string($fields['sl_menus']??null)?(json_decode($fields['sl_menus'],true)??[]):(array)($fields['sl_menus']??[])),'custom_css'=>$fields['sl_custom_css']??''];
    }
    public static function validMenus($menus): bool {
        if(!is_array($menus))return false;
        $valid=function($items,$depth=0)use(&$valid){if(!is_array($items)||$depth>32)return false;foreach($items as $key=>$item){if($key==='__empty')continue;if(!is_array($item)||!is_string($item['name']??null)||trim($item['name'])==='')return false;
            if(isset($item['children'])&&(!$valid($item['children'],$depth+1)))return false;
            if(!empty($item['action'])&&(!is_string($item['action'])||!preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/',$item['action'])))return false;
            if(empty($item['children'])&&empty($item['action'])&&(!is_string($item['url']??null)||!preg_match('~^(https?://|mailto:|tel:|/|#)~i',$item['url'])))return false;
        }return true;};return $valid($menus);
    }
    public static function validDataset($v,$type): bool {try{$file=__TYPECHO_ROOT_DIR__.__TYPECHO_PLUGIN_DIR__.'/SolitudeX/core/Data.php';if(!is_file($file))return is_array($v);require_once $file;\TypechoPlugin\SolitudeX\Data::validate($type,ArrayField::decode('sl_data_'.$type,(array)$v));return true;}catch(\Throwable){return false;}}
    public static function validModules($v): bool {foreach(explode(',',(string)$v) as $name)if(trim($name)!==''&&!in_array(trim($name),['about','newestPost','newest_comment','allInfo'],true))return false;return true;}
    public static function setPath(array &$cfg,string $path,$value): void {$ref=&$cfg;foreach(explode('.',$path) as $k){$ref=&$ref[$k];}$ref=$value;}
    public static function fields($layout): void {
        foreach (['cover'=>'文章封面','color'=>'文章主题色（留空自动取色，或填写 #RRGGBB）','description'=>'摘要','sticky'=>'置顶权重','series'=>'系列（逗号分隔）','author'=>'文章作者','desc'=>'作者介绍','avatar'=>'文章作者头像','avatar_background'=>'作者头像背景','locate'=>'文章位置','leftend'=>'页面横幅左下文字','rightend'=>'页面横幅右下文字','rightbtn'=>'页面横幅按钮文字','rightbtnlink'=>'页面横幅按钮链接','url'=>'自定义路径','aliases'=>'旧路径（每行一条）'] as $key=>$label) {
            $input=$key==='aliases'?new Textarea('sl_'.$key,null,'',$label):new Text('sl_'.$key,null,'',$label);$layout->addItem($input);
        }
        foreach (['home'=>'首页显示','toc'=>'文章目录','random'=>'参与随机文章'] as $key=>$label) $layout->addItem(new Select('sl_'.$key,['1'=>'开启','0'=>'关闭'],'1',$label));
        $layout->addItem(new Select('sl_aside',[''=>'跟随页面布局','1'=>'开启','0'=>'关闭'],'','显示侧栏','文章、普通页面和归档默认开启；留言板和最近评论默认关闭。展示类模板保持无侧栏。'));
        $layout->addItem(new Select('sl_not_cover',['0'=>'显示封面','1'=>'隐藏封面'],'0','阅读页封面'));
        $layout->addItem(new Select('sl_reprint',['0'=>'原创','1'=>'转载'],'0','文章来源'));
        $layout->addItem(new Select('sl_copyright',[''=>'沿用主题设置','1'=>'显示','0'=>'隐藏'],'','文章许可声明'));
    }
}
