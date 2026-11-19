<?php
namespace Solitude;

use Typecho\Db;
use Typecho\Config;
use Utils\Helper;
use Widget\Base\Contents;
use Widget\Base\Metas;
use Widget\Options;

final class Theme
{
    public const VERSION = '1.0.0';
    private const BUNDLES = [
        'site' => [
            'script' => 'dist/site-DVxf9gDX6xqs.js',
            'styles' => ['dist/site-lwJrg1Z5GSTQ.css'],
        ],
        'admin' => [
            'script' => 'dist/admin-nRY88aICezfH.js',
            'styles' => ['dist/admin-CVQFHZbUTCCo.css'],
        ],
        'editor' => [
            'script' => 'dist/editor-qKGusBXeyXAt.js',
            'styles' => [],
        ],
    ];
    private static ?array $config = null;
    private static ?array $settingsCache = null;
    private static array $records=[];
    private static array $decoded=[];
    private static ?array $postsCache=null;
    private static ?array $publicPostsCache=null;
    private static array $canonicalPaths=[];
    private static array $pageUrls=[];
    private static ?array $nativeOverride=null;
    public const DATA_TYPES=['about','links','kit','brevity'];
    public static function e($value): string { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
    public static function json($value): string { return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR); }
    public static function options(): Options { return Options::alloc(); }
    public static function asset(string $path): string { return rtrim(self::options()->themeUrl, '/') . '/assets/' . ltrim($path, '/'); }
    public static function bundle(string $entry, bool $script = true): void
    {
        $bundle = self::BUNDLES[$entry] ?? null;
        if ($bundle === null) {
            return;
        }

        foreach ($bundle['styles'] as $style) {
            echo '<link rel="stylesheet" href="' . self::e(self::asset($style)) . '">';
        }
        if ($script) {
            echo '<script type="module" src="' . self::e(self::asset($bundle['script'])) . '"></script>';
        }
    }
    public static function url(string $path = ''): string {
        if(ltrim($path,'/')==='index.xml')return self::options()->feedUrl;
        if (preg_match('~^(https?://|mailto:|tel:|#)~i', $path)) return $path;
        $slug=trim($path,'/');
        if(!self::enabled()&&in_array($slug,['about','links','equipment','brevity','music','message','recentcomments','archives','categories','tags'],true)){
            if(!array_key_exists($slug,self::$pageUrls)){$db=Db::get();$row=$db->fetchRow($db->select('cid')->from('table.contents')->where('type = ?','page')->where('slug = ?',$slug)->where('status = ?','publish')->where('created <= ?',time()));self::$pageUrls[$slug]=$row?Helper::widgetById('contents',(int)$row['cid'])->permalink:null;}
            if(self::$pageUrls[$slug])return self::$pageUrls[$slug];
        }
        return rtrim(self::options()->index, '/') . '/' . ltrim($path, '/');
    }
    public static function media($path): string {
        if (!$path) return self::asset('public/img/default.avif');
        if (preg_match('~^(https?://|data:image/)~i', $path)) return $path;
        if (preg_match('~^/(img|media|js|css)/~', $path)) return self::asset('public' . $path);
        return rtrim(self::options()->siteUrl, '/') . '/' . ltrim($path, '/');
    }
    public static function stored(string $name, $default = null,bool $associative=true) {
        if(!array_key_exists($name,self::$records)){$db=Db::get();$query=$db->select('value')->from('table.options')->where('name = ?',$name)->where('user = ?',0);$row=$db->fetchRow($db->query($query->prepare($query),Db::WRITE));self::$records[$name]=$row['value']??null;}
        if(self::$records[$name]===null)return $default;
        $key=$name.($associative?':array':':object');
        if(!array_key_exists($key,self::$decoded))self::$decoded[$key]=json_decode(self::$records[$name],$associative);
        return self::$decoded[$key]??$default;
    }
    public static function put(string $name, $value): void {
        $db = Db::get(); $query=$db->select('name')->from('table.options')->where('name = ?', $name)->where('user = ?', 0);$row=$db->fetchRow($db->query($query->prepare($query),Db::WRITE));
        $data = ['value' => self::json($value)];
        if ($row) $db->query($db->update('table.options')->rows($data)->where('name = ?', $name)->where('user = ?', 0));
        else $db->query($db->insert('table.options')->rows($data + ['name' => $name, 'user' => 0]));
        unset(self::$records[$name],self::$decoded[$name.':array'],self::$decoded[$name.':object']);
        if(in_array($name,['solitude:settings','theme:Solitude'],true))self::$config=self::$settingsCache=null;
        if($name==='theme:Solitude')self::$nativeOverride=$value;
    }
    public static function transaction(callable $write): void {
        $nativeBefore=self::$nativeOverride;$db=Db::get();$sqlite=str_contains($db->getAdapterName(),'SQLite');
        if(!$sqlite){
            $query=$db->select('ENGINE')->from('information_schema.TABLES')->where('TABLE_SCHEMA = DATABASE()')->where('TABLE_NAME = ?',$db->getPrefix().'options');
            $table=$db->fetchRow($db->query($query->prepare($query),Db::WRITE));
            if(strcasecmp($table['ENGINE']??'','InnoDB')!==0)throw new \Typecho\Widget\Exception('配置迁移与恢复需要 options 表使用 InnoDB，请先备份数据库并转换存储引擎。',500);
        }
        $db->query($sqlite?'BEGIN IMMEDIATE':'BEGIN',Db::WRITE);
        try{
            // Serialize migration and restore on the same writer connection.
            if(!$sqlite){$query=$db->select('value')->from('table.options')->where('name = ?','theme')->where('user = ?',0);$db->query($query->prepare($query).' FOR UPDATE',Db::WRITE);}
            self::$records=self::$decoded=[];self::$config=self::$settingsCache=null;
            $write();$db->query('COMMIT',Db::WRITE);
        }
        catch(\Throwable $error){$db->query('ROLLBACK',Db::WRITE);self::$nativeOverride=$nativeBefore;throw $error;}
        finally{self::$records=self::$decoded=[];self::$config=self::$settingsCache=null;}
    }
    public static function snapshot(): array {
        $data=[];foreach(self::DATA_TYPES as $type)$data[$type]=self::dataset($type);
        return ['version'=>3,'created'=>time(),'form'=>self::nativeSettings(),'legacy'=>self::stored('solitude:settings',[]),'datasets'=>$data];
    }
    public static function migrateContent(): void {
        if(self::stored('solitude:data:version')===1)return;
        self::transaction(function(){
            // Recheck on the writer connection before switching away from native datasets.
            if(self::stored('solitude:data:version')===1)return;
            $snapshot=self::snapshot();
            self::put('solitude:upgrade:backup',$snapshot);
            foreach($snapshot['datasets'] as $type=>$value)self::put('solitude:data:'.$type,$value);
            self::put('solitude:data:version',1);
        });
    }
    public static function restoreSnapshot(array $backup,bool $undo=false): void {
        require_once __DIR__.'/Settings.php';
        if(!in_array($backup['version']??null,[2,3],true)||!is_array($backup['form']??null))throw new \InvalidArgumentException('备份格式或版本无效。');
        if(($backup['version']===3&&!is_array($backup['legacy']??null))||(isset($backup['legacy'])&&!is_array($backup['legacy'])))throw new \InvalidArgumentException('备份中的旧版配置格式无效。');
        $data=$backup['datasets']??[];
        if($backup['version']===2){
            if(($backup['form']['sl_native_config']??null)!=='1')throw new \InvalidArgumentException('旧版备份缺少主题配置。');
            foreach(self::DATA_TYPES as $type){
                $value=$backup['form']['sl_data_'.$type]??null;
                if(is_string($value))$value=json_decode($value,true,512,JSON_THROW_ON_ERROR);
                if(!is_array($value))throw new \InvalidArgumentException('旧版备份缺少'.$type.'内容。');
                $data[$type]=ArrayField::decode('sl_data_'.$type,$value);
            }
        }
        foreach(self::DATA_TYPES as $type){
            if(!isset($data[$type])||!is_array($data[$type])||!Settings::validDataset($data[$type],$type))throw new \InvalidArgumentException('备份内容无效：'.$type);
        }
        foreach(Settings::schema() as $field)if(array_key_exists($field['name'],$backup['form'])&&!Settings::validateValue($backup['form'][$field['name']],$field,$backup['form']))throw new \InvalidArgumentException('备份设置无效：'.$field['label']);
        self::transaction(function()use($backup,$data,$undo){
            $before=self::snapshot();
            if(!$undo)self::put('solitude:restore:undo',$before);
            self::put('theme:Solitude',$backup['form']);
            if(isset($backup['legacy']))self::put('solitude:settings',$backup['legacy']);
            foreach($data as $type=>$value)if(in_array($type,self::DATA_TYPES,true))self::put('solitude:data:'.$type,$value);
            self::put('solitude:data:version',1);
            if($undo)self::put('solitude:restore:undo',null);
        });
    }
    public static function merge(array $base, array $override): array {
        foreach ($override as $key => $value) {
            if (in_array($key, ['__proto__', 'constructor', 'prototype'], true)) continue;
            $base[$key] = is_array($value) && !array_is_list($value) && isset($base[$key]) && is_array($base[$key]) ? self::merge($base[$key], $value) : $value;
        } return $base;
    }
    public static function nativeSettings(): array {
        if(self::$nativeOverride!==null)return self::$nativeOverride;
        // Read a separate cursor through Widget Options' public API. This retains
        // explicit null values (native empty selections) without moving its cursor
        // or interpreting Typecho's storage encoding.
        $cursor=clone self::options();
        $row=$cursor->next();if($row===false)$row=$cursor->next();
        return array_filter($row?:[],fn($name)=>str_starts_with($name,'sl_'),ARRAY_FILTER_USE_KEY);
    }
    public static function settings(): array {
        if(self::$settingsCache!==null)return self::$settingsCache;
        $fields=self::nativeSettings();
        if(($fields['sl_native_config']??'')==='1') {
            require_once __DIR__.'/Settings.php';
            return self::$settingsCache=Settings::record($fields);
        }
        // Read the previous theme's record until the first native form save.
        return self::$settingsCache=self::stored('solitude:settings',[]);
    }
    public static function config(): array {
        if (self::$config !== null) return self::$config;
        $defaults = json_decode(file_get_contents(__DIR__ . '/../resources/defaults.json'), true);
        $saved = self::settings();
        $cfg = self::merge($defaults, $saved['theme'] ?? []);
        if(is_bool($cfg['expire']??null))$cfg['expire']=self::merge($defaults['expire'],['enable'=>$cfg['expire']]);
        $cfg['site']['name']['custom'] = $saved['theme']['site']['name']['custom'] ?? self::options()->title;
        $cfg['aside']['my_card']['description'] = $saved['theme']['aside']['my_card']['description'] ?? self::options()->description;
        $cfg['aside']['my_card']['author']['img'] = self::media($cfg['aside']['my_card']['author']['img']);
        $cfg['post']['rss'] = self::options()->feedUrl;
        foreach(['twikoo','waline','valine','artalk','giscus'] as $service)unset($cfg[$service]);
        $cfg['comment']['use']=empty($cfg['comment']['use'])?'':'native';
        unset($cfg['post']['meta']['pv'],$cfg['comment']['pv']);
        return self::$config = $cfg;
    }
    public static function get(string $path, $default = null) {
        $value = self::config(); foreach (explode('.', $path) as $key) { if (!is_array($value) || !array_key_exists($key, $value)) return $default; $value = $value[$key]; } return $value;
    }
    public static function locale(): string { return self::settings()['locale'] ?? 'zh-CN'; }
    public static function t(string $key): string {
        static $cache = []; $locale = self::locale();
        $cache[$locale] ??= json_decode(file_get_contents(__DIR__ . '/../resources/i18n/' . $locale . '.json'), true);
        return $cache[$locale][$key] ?? $key;
    }
    public static function field($post, string $name, $default = null) { $v = $post->fields->{'sl_' . $name}; return $v === null || $v === '' ? $default : $v; }
    public static function cover($post): string { return self::media(self::field($post, 'cover', self::get('post.default.cover.0', '/img/default.avif'))); }
    public static function enabled(): bool { return isset(self::options()->plugins['activated']['SolitudeX']); }
    public static function dataset(string $type): array {
        if(!in_array($type,self::DATA_TYPES,true))throw new \InvalidArgumentException('未知内容类型。');
        $sample = json_decode(file_get_contents(__DIR__ . '/../resources/data/' . $type . '.json'), true);
        if(self::stored('solitude:data:version')===1)return self::stored('solitude:data:'.$type,$sample);
        $fields=self::nativeSettings();
        if(isset($fields['sl_data_'.$type])){require_once __DIR__.'/ArrayField.php';$value=$fields['sl_data_'.$type];if(is_string($value))$value=json_decode($value,true,512,JSON_THROW_ON_ERROR);return ArrayField::decode('sl_data_'.$type,(array)$value);}
        return self::stored('solitude:data:' . $type, $sample);
    }
    public static function datasetJson(string $type): string {
        if(!in_array($type,self::DATA_TYPES,true))throw new \InvalidArgumentException('Unknown dataset');
        return self::json(self::dataset($type));
    }
    public static function posts(bool $home = false): array {
        if(self::$postsCache===null)self::$postsCache=self::loadPosts();
        $posts=$home?array_values(array_filter(self::$postsCache,fn($post)=>self::field($post,'home','1')!=='0')):self::$postsCache;
        if ($home) usort($posts, fn($a,$b) => ((int)self::field($b, 'sticky', 0) <=> (int)self::field($a, 'sticky', 0)) ?: ($b->created <=> $a->created));
        return $posts;
    }
    private static function loadPosts(): array
    {
        $db = Db::get();
        $rows = $db->fetchAll(
            $db->select()->from('table.contents')
                ->where('type = ?', 'post')
                ->where('status = ?', 'publish')
                ->where('created <= ?', time())
                ->order('created', Db::SORT_DESC)
        );
        if (!$rows) {
            return [];
        }

        $ids = array_column($rows, 'cid');
        $fields = [];
        $tags = [];
        $categoryIds = [];
        foreach ($ids as $cid) {
            $fields[$cid] = [];
            $tags[$cid] = [];
            $categoryIds[$cid] = [];
            self::$canonicalPaths[$cid] = null;
        }

        $fieldRows = $db->fetchAll(
            $db->select()->from('table.fields')->where('cid IN ?', $ids)
        );
        foreach ($fieldRows as $field) {
            $fields[$field['cid']][$field['name']] = $field['type'] === 'json'
                ? json_decode($field['str_value'], true)
                : $field[$field['type'] . '_value'];
            if ($field['name'] === 'sl_url') {
                self::$canonicalPaths[$field['cid']] = $field['str_value'];
            }
        }

        $relations = $db->fetchAll(
            $db->select('table.metas.*', 'table.relationships.cid')
                ->from('table.metas')
                ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
                ->where('table.relationships.cid IN ?', $ids)
        );
        foreach ($relations as $term) {
            if ($term['type'] === 'category') {
                $categoryIds[$term['cid']][] = $term['mid'];
            } elseif ($term['type'] === 'tag') {
                $tags[$term['cid']][] = $term;
            }
        }

        $categoryRows = $db->fetchAll(
            $db->select()->from('table.metas')->where('type = ?', 'category')
        );
        $posts = [];
        $options = self::options();
        foreach ($rows as $row) {
            $cid = (int) $row['cid'];
            // These temporary widgets materialize metadata without retaining stale pool entries.
            $categories = new PostCategories(
                $options->request,
                $options->response,
                ['cid' => $cid, 'rows' => $categoryRows, 'ids' => $categoryIds[$cid]]
            );
            $categories->execute();
            $tagWidget = new \Widget\Metas\Tag\Related(
                $options->request,
                $options->response,
                ['cid' => $cid]
            );
            $tagWidget->pushAll($tags[$cid]);
            $row['#fields'] = new Config($fields[$cid]);
            $row['#tags'] = $tagWidget->toArray(['mid', 'name', 'slug', 'description', 'count', 'permalink']);
            $row['#categories'] = $categories->toArray([
                'mid', 'name', 'slug', 'description', 'order', 'parent', 'count', 'permalink'
            ]);
            $post = Contents::allocWithAlias((string) $cid);
            // Push through Typecho's normal filters using the row already fetched above.
            $post->push($row);
            $posts[] = $post;
        }
        return $posts;
    }

    public static function canonicalPath(int $cid): ?string
    {
        if (!array_key_exists($cid, self::$canonicalPaths)) {
            $db = Db::get();
            $field = $db->fetchRow(
                $db->select('str_value')->from('table.fields')
                    ->where('cid = ?', $cid)
                    ->where('name = ?', 'sl_url')
            );
            self::$canonicalPaths[$cid] = $field['str_value'] ?? null;
        }
        return self::$canonicalPaths[$cid];
    }

    public static function invalidatePosts(): void
    {
        self::$postsCache = null;
        self::$publicPostsCache = null;
        self::$canonicalPaths = [];
        if (class_exists(View::class, false)) {
            View::invalidateTaxonomy();
        }
    }
    public static function permalink($post): string { return self::enabled() && self::field($post, 'url') ? self::url(self::field($post, 'url')) : $post->permalink; }
    public static function publicPosts(): array { return self::$publicPostsCache??=array_values(array_filter(self::posts(), fn($p) => !$p->password)); }
    public static function series($post): array { return array_values(array_filter(array_map('trim', explode(',', (string) self::field($post, 'series', ''))))); }
    public static function comments(?int $limit = 50): array {
        $db = Db::get();
        $query=$db->select('table.comments.coid','table.comments.cid','table.comments.author','table.comments.mail','table.comments.text','table.comments.created','table.contents.title','table.contents.type','table.contents.slug')
            ->from('table.comments')->join('table.contents', 'table.contents.cid = table.comments.cid', Db::INNER_JOIN)
            ->where('table.comments.status = ?', 'approved')->where('table.comments.type = ?', 'comment')
            ->where('table.contents.status = ?', 'publish')->where('(table.contents.password IS NULL OR table.contents.password = ?)', '')
            ->where('table.contents.created <= ?', time())->order('table.comments.created', Db::SORT_DESC);
        if($limit!==null)$query->limit($limit);
        return $db->fetchAll($query);
    }
    public static function avatar(string $mail): string { return 'https://www.gravatar.com/avatar/' . hash('md5', strtolower(trim($mail))) . '?d=mp&s=96'; }
    public static function runtime(string $type, bool $post = false): array {
        $runtime = json_decode(file_get_contents(__DIR__ . '/../resources/runtime-defaults.json'), true);
        $runtime = self::merge($runtime, self::config());
        $base = parse_url(self::options()->siteUrl, PHP_URL_PATH) ?: '/'; $runtime['root'] = rtrim($base, '/') . '/';
        $runtime['localsearch']['path'] = self::url('solitude/search.xml');
        $runtime['localsearch']['preload'] = self::get('search.local.preload', false);
        $runtime['algolia']=self::get('search.algolia',[]);
        $runtime['aside']['state']=self::get('aside.my_card.state',[]);
        $runtime['aside']['witty_words']=self::get('aside.my_card.witty_words',[]);
        $runtime['aside']['witty_comment']=['prefix'=>self::t('hello'),'back'=>self::t('welcomeBack')];
        $runtime['covercolor']=self::get('post.covercolor',[]);
        $runtime['randomlink']=self::get('footer.randomlink',false);
        $right=self::get('right_menu.enable')&&!in_array($type,self::get('right_menu.exclude',[]),true);
        if(!$right)$runtime['right_menu']=false;
        if(!self::get('translate.enable'))$runtime['translate']=false;
        $runtime['assetRoot']=self::asset('public/');
        $runtime['comment_runtime']['routes']=[];foreach(self::publicPosts() as $p)$runtime['comment_runtime']['routes'][parse_url(self::permalink($p),PHP_URL_PATH)]=$p->title;
        $runtime['comment_runtime']['default_avatar']=self::media('/img/default_avatar.avif');$runtime['comment_runtime']['barrage_script']=self::media('/js/third_party/barrage.min.js');
        $runtime['random_posts'] = array_map(fn($p) => self::permalink($p), array_filter(self::publicPosts(), fn($p) => self::field($p, 'random', '1') !== '0'));
        $runtime['lazyload']['error'] = self::media(self::get('lazyload.errorimg'));
        $runtime['runtime'] = self::get('aside.siteinfo.runtimeenable') ? self::get('aside.siteinfo.runtime') : false;
        $links = self::dataset('links'); $total = array_sum(array_map(fn($g) => count($g['link_list'] ?? []), $links['links'] ?? []));
        $runtime['friend_links']['total'] = $total; $runtime['friend_links']['async'] = $total > self::get('page.links.async_threshold', 200);
        $runtime['friend_links']['path'] = self::url('solitude/links.json'); $runtime['friend_links']['default_avatar'] = self::media('/img/default_avatar.avif');
        $runtime['friend_links']['more_url'] = self::url('links/');
        $runtime['feature_modules'] = ['search' => self::get('search.enable') && self::enabled() ? self::get('search.type') : false, 'friend_links' => $total > 0,
            'keyboard' => self::get('keyboard.enable'), 'music' => self::get('music.enable') || self::get('capsule.enable'), 'right_menu' => $right,
            'translate' => self::get('translate.enable'), 'covercolor' => self::get('post.covercolor.enable') ? self::get('post.covercolor.mode') : false];
        foreach (['fancyapps_js','fancyapps_css','aplayer_js','aplayer_css','meting_js','lazyload'] as $key) {
            $files = ['fancyapps_js'=>'fancybox.umd.js','fancyapps_css'=>'fancybox.css','aplayer_js'=>'APlayer.min.js','aplayer_css'=>'APlayer.min.css','meting_js'=>'Meting.min.js','lazyload'=>'lazyload.iife.min.js'];
            if (is_file(__DIR__.'/../assets/public/vendor/'.$files[$key])) $runtime['cdn'][$key] = self::asset('public/vendor/'.$files[$key]);
        }
        $en = json_decode(file_get_contents(__DIR__.'/../resources/i18n/en.json'),true); $translated = json_decode(file_get_contents(__DIR__.'/../resources/i18n/'.self::locale().'.json'),true);
        $reverse = array_flip(array_filter($en, 'is_string'));
        $walk = function($v) use (&$walk,$reverse,$translated) { if (is_array($v)) return array_map($walk,$v); return is_string($v) && isset($reverse[$v]) ? ($translated[$reverse[$v]] ?? $v) : $v; };
        $runtime['lang'] = $walk($runtime['lang']);foreach(['zoomIn','zoomOut','rotate','original','close','image'] as $key)$runtime['lang']['ui'][$key]=self::t($key);
        $runtime['native_comments']=self::get('comment.use')==='native';
        if ($runtime['native_comments']) $runtime['comment']['use'] = '';
        foreach(['twikoo','waline','valine','artalk','giscus'] as $service)unset($runtime[$service]);
        foreach(['twikoo','waline','waline_css','valine','blueimp_md5','artalk','artalk_css','giscus'] as $service)unset($runtime['cdn'][$service]);
        return $runtime;
    }
    public static function classes(string $html): string {
        static $map=null;$map??=json_decode(file_get_contents(__DIR__.'/../resources/class-map.json'),true);
        return preg_replace_callback('~<[a-zA-Z][\w:-]*(?:"[^"]*"|\x27[^\x27]*\x27|[^>"\x27])*>~',function($m)use($map){$tag=$m[0];preg_match('/\bclass="([^"]*)"/',$tag,$classes);preg_match('/\bid="([^"]*)"/',$tag,$id);$original=html_entity_decode($classes[1]??'',ENT_QUOTES,'UTF-8');$util=[];foreach(explode(' ',$original) as $class)$util=array_merge($util,$map['.'.$class]??[]);if(isset($id[1]))$util=array_merge($util,$map['#'.$id[1]]??[]);if(!$util)return $tag;$value=self::e(trim($original.' '.implode(' ',array_unique($util))));return isset($classes[0])?str_replace($classes[0],'class="'.$value.'"',$tag):substr($tag,0,-1).' class="'.$value.'">';},$html);
    }
    public static function render(string $part, array $vars = []): void { extract($vars);include __DIR__ . '/../parts/' . $part . '.php'; }
}

/** Keep Typecho's category tree ordering and filters while using preloaded rows. */
final class PostCategories extends Metas
{
    use \Widget\Base\TreeTrait;

    protected function initTreeRows(): array
    {
        return $this->parameter->rows;
    }

    public function execute(): void
    {
        $ids = $this->parameter->ids;
        $positions = array_flip($this->orders);
        usort($ids, fn($a, $b) => ($positions[$a] ?? 0) <=> ($positions[$b] ?? 0));
        $this->pushAll($this->getRows($ids));
    }
}
