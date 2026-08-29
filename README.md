# WordPress B站追番/追剧页面模板

### 本项目为 [bilibili](https://github.com/Fog-Forest/bilibili) 的修改版
注意：特殊适配于[Argon主题](https://github.com/solstice23/argon-theme)~

已使用AI辅助重构前端（2025/12/23）

### 更新日志

**2026/8/29**

- 追番 / 追剧页面适配 Argon 主题暗黑 / 夜间 / 沉浸模式，夜间浏览不再出现刺眼的白底卡片
- API 引入本地文件缓存（默认 6 小时），缓存有效期内请求零出站；配合 ETag/304，重复访问几乎不消耗服务器资源
- API 增加参数校验与强制刷新（`?refresh=1`），公共逻辑统一封装到 `bilibili-api/common.php`，追番 / 追剧接口去重
- 图片加载失败占位图改为透明中性灰，深浅模式下均不刺眼

  * 本次更新由 DeepSeek-V4-Flash 强力支持

### 使用说明
1. 下载本项目，将 `biliibili-api` 整个目录扔到你的站点根路径，将 `page-anime.php` 和 `page-movie.php` 文件放到你的主题根路径。

2. 按照注释，修改 `bilibili-api` 里的 `bilibiliAcconut.php` 文件，填入你的信息。

3. 最后在 WP后台 新建页面时选择相应的模板，创建页面即可。

### 获取信息
#### 1. 获取B站UID
打开[](https://www.bilibili.com/)，登入后进入个人空间，红框处为你的 UID，不要忘记把番剧设置成公开哦~





### 预览

[https://blog.suiyil.cn/bilibili-bangumi](https://blog.suiyil.cn/bilibili-bangumi)

已经把原代码改的乱七八糟了，当前在我的博客可正常运行，有问题请发issue谢谢。
