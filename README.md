# Solitude for Typecho

Solitude 是一个卡片式 Typecho 博客主题，包含文章目录、归档、原生评论和多语言界面。配套插件 SolitudeX 提供短代码、特色页面管理和本地搜索。

运行环境为 Typecho **1.3.0**、PHP **8.2+**（启用 DOM 扩展）、MySQL / MariaDB 或 SQLite。

## 安装

1. 从 [GitHub Releases](https://github.com/everfu/typecho-solitude/releases) 下载 `Solitude-1.0.0.zip`。解压后将 `Solitude/` 放入 Typecho 的 `usr/themes/`，确保 `index.php` 位于 `Solitude/` 根目录。CSS 和 JavaScript 随主题提供，上传后即可使用。
2. 在「控制台 → 外观」启用 Solitude，然后打开「外观 → 设置外观」填写并保存站点设置。
3. 如需短代码、特色内容管理或本地搜索，获取 `SolitudeX-1.0.0.zip` 后，将解出的 `SolitudeX/` 放入 Typecho 的 `usr/plugins/`，再到「控制台 → 插件」启用。
4. 启用配套插件后，可在主题的「页面设置 → 页面初始化」补建特色页面。

已有站点升级时，先备份数据库和上传文件，再覆盖同名目录。

## 包含的功能

- 首页推荐区、文章卡片、侧栏、文章目录、原生评论回复与分页、归档、分类、标签、搜索、系列和 404。
- 关于、友链、装备、即刻、音乐馆、留言板和最近评论模板。
- Typecho 原生风格的七类主题设置，支持搜索、自定义下拉、二级分类原地切换与未保存提醒。在「管理」菜单中直接打开关于、友链、装备和即刻，各页使用对应的原生标题，切换时保留草稿并分别保存，支持筛选、折叠、排序与撤销删除。
- 41 个短代码组件，原生编辑器插入工具与隔离的静态组件预览。
- 本地搜索、Algolia / DocSearch 适配器；只保留 Typecho 原生评论。
- 明暗模式、四种前台界面语言、图片灯箱、键盘快捷键、右键菜单和连续导航保留音乐胶囊。
- 浏览器按需加载 Shiki、KaTeX、图表、乐谱及媒体资源；原生 RSS、站点地图与 PWA 应用清单。

## 源码目录

| 路径 | 用途 |
| --- | --- |
| `index.php`、`functions.php`、`page-*.php` | Typecho 主题入口、设置注册和页面模板 |
| `assets/`、`core/`、`pages/`、`parts/`、`resources/` | 静态资源、主题逻辑和模板部件 |

主题设置由 Typecho 原生外观表单保存到 `theme:Solitude`。SolitudeX 的四类内容独立保存到 `solitude:data:<type>`，停用插件会保留数据。文章、附件和评论使用 Typecho 原生数据。

## 许可证

项目采用 Apache-2.0 许可证。上游署名和第三方资源许可随主题提供。[LICENSE](LICENSE) · [上游署名](assets/licenses/UPSTREAM-NOTICE)
