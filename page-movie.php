<?php

/**
 Template Name: B站追剧页面
 Template author: 蘑菇君
 */

get_header(); ?>
<div class="page-information-card-container"></div>

<?php get_sidebar(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
	<article class="post post-full card bg-white shadow-sm border-0" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="post-header text-center">
		<a class="post-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
<hr>
</header>
<?php the_content(); ?>

<style>
    /* ==================== 配色变量（适配 Argon 主题色 / 暗黑 / OLED 夜间 / 沉浸模式） ====================
       注意：--bili-* 变量统一定义在 body 元素上，而非 :root / html。
       原因：Argon 主题的模式变量（--color-foreground / --color-text-deeper 等）定义在
       body 元素上并按模式切换（html.darkmode body、html.darkmode.amoled-dark body、
       html.immersion-color body）。CSS 自定义属性的 var() 引用是在"定义该自定义属性的
       元素"上解析的：若把 --bili-* 定义在 html 元素上，var(--color-foreground) 只会取到
       :root 中的浅色值 #fff（暗黑值在 body 上，html 元素上没有），导致暗黑/夜间模式下
       卡片背景仍是白色——即本问题（--bili-link-bg 解析为 #fff）。定义在 body 上后与
       主题变量同元素解析：暗黑自动取 #424242、OLED 取 #000、沉浸模式自动跟随。 */
    body {
        --bili-link-bg: var(--color-foreground, #fff);
        --bili-link-color: var(--color-text-deeper, #212529);
        --bili-link-color-hover: var(--color-text-deeper, #212529);
        --bili-banner-bg: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
        --bili-overlay-bg: rgba(255, 255, 255, 0.97);
        --bili-overlay-text: #444;
        --bili-overlay-meta: #666;
        --bili-overlay-border: #eee;
        --bili-title-color: var(--color-text-deeper, #333);
        --bili-card-bg: var(--color-foreground, #fff);
        --bili-text: var(--color-text-deeper, #303133);
        --bili-content-border: #e3e5e7;
        --bili-badge-bg: #f1f2f3;
        --bili-badge-color: #6c757d;
        --bili-type-bg: #e6f4fc;
        --bili-type-color: #00a1d6;
        --bili-finish-bg: #e8f8ef;
        --bili-finish-color: #00b96b;
        --bili-follow-bg: #fdeef3;
        --bili-follow-color: #fb7299;
        --bili-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        --bili-shadow-hover: 0 6px 20px rgba(0, 0, 0, 0.10);
        --bili-scrollbar-track: #f1f1f1;
        --bili-spinner-bg: #f3f3f3;
        --bili-loading-bg: rgba(255, 255, 255, 0.8);
        --bili-muted: #99a2aa;
        --bili-error-bg: #ffebee;
        --bili-error-color: #f44336;
    }

    /* 暗黑模式（与主题变量同元素定义，var() 引用自动解析为暗色值） */
    html.darkmode body {
        /* --bili-link-bg / --bili-link-color / --bili-link-color-hover / --bili-title-color
           --bili-card-bg / --bili-text
           无需覆盖：这些变量在 body 上引用 var(--color-foreground) / var(--color-text-deeper)，
           暗黑模式下自动解析为 #424242 / #eee，OLED 下自动为 #000 / #eee */
        --bili-banner-bg: linear-gradient(135deg, #3a3a3a 0%, #2c2c2c 100%);
        --bili-overlay-bg: rgba(66, 66, 66, 0.97);
        --bili-overlay-text: #ccc;
        --bili-overlay-meta: #bbb;
        --bili-overlay-border: #555;
        --bili-title-color: #eee;
        --bili-content-border: #555;
        --bili-badge-bg: #2c2e33;
        --bili-badge-color: #99a2aa;
        /* 三大标签背景色：夜间使用专用深色底（恢复原值）
           标签文字不定义夜间值，回落到 body 浅色值（#00a1d6 / #00b96b / #fb7299），
           即夜间保持与普通模式相同的文字颜色（配合下方 html.darkmode .badge.* 覆盖，防止主题刷白） */
        --bili-type-bg: #16304a;
        --bili-finish-bg: #123426;
        --bili-follow-bg: #3d1f2b;
        --bili-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
        --bili-shadow-hover: 0 6px 20px rgba(0, 0, 0, 0.6);
        --bili-scrollbar-track: #333;
        --bili-spinner-bg: #666;
        --bili-loading-bg: rgba(66, 66, 66, 0.8);
        --bili-muted: #7d858c;
        --bili-error-bg: rgba(244, 67, 54, 0.22);
        --bili-error-color: #ef9a9a;
    }

    /* OLED 夜间模式（--bili-link-bg 在 body 上引用 var(--color-foreground) 自动取 #000，无需覆盖） */
    html.darkmode.amoled-dark body {
        --bili-banner-bg: linear-gradient(135deg, #1d1d1d 0%, #0f0f0f 100%);
        --bili-overlay-bg: rgba(0, 0, 0, 0.97);
        --bili-overlay-text: #bdbdbd;
        --bili-overlay-meta: #9e9e9e;
        --bili-overlay-border: #252525;
        --bili-content-border: #222;
        --bili-loading-bg: rgba(0, 0, 0, 0.8);
        --bili-scrollbar-track: #1a1a1a;
        --bili-spinner-bg: #333;
    }

    /* 布局和加载样式 */
    .loading-text {
        color: var(--bili-muted);
        font-style: italic;
    }

    .load-more-btn {
        margin: 30px auto;
        padding: 12px 30px;
        background: linear-gradient(135deg, var(--themecolor-light) 0%, var(--themecolor) 60%, var(--themecolor-dark) 100%);
        color: #fff;
        border: none;
        border-radius: 25px;
        font-size: 16px;
        cursor: pointer;
        text-align: center;
        max-width: 200px;
        transition: all 0.3s ease;
        position: relative;
        display: block;
    }

    .load-more-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(var(--themecolor-rgbstr), 0.45);
    }

    .load-more-btn.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .no-more-text {
        text-align: center;
        color: var(--bili-muted);
        padding: 20px;
        font-size: 14px;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--bili-loading-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid var(--bili-spinner-bg);
        border-top: 3px solid var(--themecolor);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .bilibili-movie-container {
        position: relative;
        min-height: 300px;
    }

    /* 错误提示（原内联样式改为类，随暗黑模式自动适配） */
    .error-message {
        width: 100%;
        text-align: center;
        color: var(--bili-error-color);
        padding: 20px;
        background: var(--bili-error-bg);
        border-radius: 8px;
        margin: 10px 0;
    }

    /* ==================== 追剧列表（列表布局，参照「番剧列表布局测试 · dev-1」，与追番页一致） ====================
       结构：封面 | 标题+评分+评分人数 / 三大标签 / index_show / 简介（追剧数据无版权区域，故不含 .area） */
    .anime-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin: 10px 0;
    }

    .list-item {
        display: flex;
        gap: 16px;
        padding: 16px;
        background: var(--bili-card-bg);
        border: 1px solid var(--bili-content-border);
        border-radius: 12px;
        box-shadow: var(--bili-shadow);
        transition: box-shadow .2s, transform .2s;
    }

    .list-item:hover {
        box-shadow: var(--bili-shadow-hover);
        transform: translateY(-2px);
    }

    .cover-link {
        flex: 0 0 auto;
        display: block;
    }

    .cover {
        width: 150px;
        aspect-ratio: 3 / 4;
        object-fit: cover;
        border-radius: 8px;
        display: block;
        background: var(--bili-badge-bg);
    }

    .item-body {
        flex: 1;
        min-width: 0;
    }

    .title-row {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        column-gap: 8px;
        row-gap: 2px;
    }

    .item-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.4;
        color: var(--bili-title-color);
    }

    .item-title a {
        color: inherit;
        text-decoration: none;
    }

    .item-title a:hover {
        text-decoration: underline;
    }

    /* 夜间模式：主题规则 html.darkmode a { color: var(--themecolor-light) } / a:hover { color: var(--themecolor) }
       会把标题链接刷成主题色，此处用更高优先级强制标题（含 hover）为白色 */
    html.darkmode .item-title,
    html.darkmode .item-title a,
    html.darkmode .item-title a:hover {
        color: #fff;
    }

    .score {
        font-size: 13px;
        font-weight: 700;
        color: #ffa726;
    }

    .rating-count {
        font-size: 12px;
        color: var(--bili-muted);
    }

    .badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .badge {
        font-size: 11px;
        line-height: 1;
        padding: 4px 9px;
        border-radius: 999px;
        background: var(--bili-badge-bg);
        color: var(--bili-badge-color);
        font-weight: 500;
        white-space: nowrap;
    }

    .badge.type {
        background: var(--bili-type-bg);
        color: var(--bili-type-color);
    }

    .badge.finish {
        background: var(--bili-finish-bg);
        color: var(--bili-finish-color);
    }

    .badge.follow {
        background: var(--bili-follow-bg);
        color: var(--bili-follow-color);
    }

    /* 夜间模式：主题规则 html.darkmode .badge { color:#eee } 会强制把标签文字刷白，
       此处用更高优先级（html.darkmode .badge.type 等，specificity 高于主题）恢复普通模式的文字颜色 */
    html.darkmode .badge.type {
        color: var(--bili-type-color);
    }

    html.darkmode .badge.finish {
        color: var(--bili-finish-color);
    }

    html.darkmode .badge.follow {
        color: var(--bili-follow-color);
    }

    .index-show {
        margin-top: 8px;
        font-size: 12px;
        color: var(--bili-text);
        opacity: .85;
    }

    .desc {
        margin: 10px 0 0;
        font-size: 13px;
        line-height: 1.65;
        color: var(--bili-muted);
        white-space: pre-line;
        overflow-wrap: break-word;
    }

    .page-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--themecolor);
    }

    .page-header h2 {
        margin: 0;
        color: var(--bili-title-color);
        font-size: 22px;
    }

    .page-header small {
        color: var(--bili-overlay-meta);
        font-size: 13px;
        margin-left: 8px;
        font-weight: normal;
    }

    /* 响应式设计 */
    @media (max-width: 520px) {
        .cover {
            width: 108px;
        }

        .item-title {
            font-size: 16px;
        }

        .list-item {
            gap: 12px;
            padding: 12px;
        }
    }

    @media (max-width: 400px) {
        .page-header h2 {
            font-size: 18px;
        }

        .page-header small {
            font-size: 12px;
            display: block;
            margin-left: 0;
            margin-top: 5px;
        }
    }
</style>

<div class="bilibili-movie-container">
    <div class="page-header">
        <h2>我的追剧 <small>当前已追<span id="total" class="loading-text">加载中...</span>部，继续加油！</small></h2>
    </div>
    <div id="bilibiliMovie" class="anime-list"></div>
    <div id="loadMore" class="load-more-btn">加载更多</div>
    <div id="noMoreData" class="no-more-text" style="display: none;">已经到底了哦~</div>
</div>

<script type="text/javascript">
    (function($) {
        'use strict';

        class BilibiliMovie {
            constructor() {
                this.pageNum = 0;
                this.limit = 12;
                this.isLoading = false;
                this.hasMore = true;
                this.container = $('#bilibiliMovie');
                this.loadMoreBtn = $('#loadMore');
                this.noMoreText = $('#noMoreData');
                this.totalSpan = $('#total');

                this.init();
            }

            init() {
                this.loadData();
                this.bindEvents();
            }

            bindEvents() {
                const self = this;

                // 点击加载更多
                this.loadMoreBtn.on('click', function() {
                    if (!self.isLoading && self.hasMore) {
                        self.loadData();
                    }
                });

                // 滚动加载
                $(window).on('scroll', this.debounce(() => {
                    if (this.shouldLoadMore()) {
                        this.loadData();
                    }
                }, 200));
            }

            loadData() {
                if (this.isLoading || !this.hasMore) return;

                this.isLoading = true;
                this.showLoading();

                $.ajax({
                    type: "GET",
                    url: "/bilibili-api/GetMovieData.php",
                    data: {
                        limit: this.limit,
                        page: this.pageNum
                    },
                    dataType: "json",
                    success: (data) => this.handleSuccess(data),
                    error: (xhr, status, error) => this.handleError(error),
                    complete: () => {
                        this.isLoading = false;
                        this.hideLoading();
                    }
                });
            }

            handleSuccess(data) {
                if (!data || !data.data) {
                    console.error('数据格式错误');
                    return;
                }

                // 更新总数
                if (typeof data.total !== 'undefined' && data.total !== null) {
                    this.totalSpan.text(data.total).removeClass('loading-text');
                }

                // 渲染数据
                this.renderItems(data.data);

                // 检查是否还有更多
                this.pageNum++;
                if (this.pageNum > data.total_page || data.data.length < this.limit) {
                    this.hasMore = false;
                    this.loadMoreBtn.hide();
                    this.noMoreText.show();
                }
            }

            handleError(error) {
                console.error('加载失败:', error);
                this.showErrorMessage('加载失败，请稍后重试');
            }

            showErrorMessage(message) {
                if (!$('.error-message').length) {
                    this.container.prepend(`
                        <div class="error-message">${message}</div>
                    `);
                }
            }

            renderItems(items) {
                items.forEach(item => {
                    const itemHtml = this.createItemHtml(item);
                    this.container.append(itemHtml);
                });
            }

            createItemHtml(item) {
                const evaluate = item.evaluate ? this.escapeHtml(item.evaluate) : '暂无简介';
                const title = item.title ? this.escapeHtml(item.title) : '未知标题';
                // 无评分 / 无评分人数时，前端直接不渲染对应元素（无占位提示词）
                const scoreHtml = item.rating_score ? `<span class="score">${item.rating_score}</span>` : '';
                const ratingCountHtml = item.rating_count ? `<span class="rating-count">${item.rating_count}人</span>` : '';
                const type = item.type ? this.escapeHtml(item.type) : '未知';
                const finish = item.finish ? this.escapeHtml(item.finish) : '未知';
                const followStatus = item.follow_status ? this.escapeHtml(item.follow_status) : '未知';
                const indexShow = item.index_show ? this.escapeHtml(item.index_show) : '暂无剧集信息';
                // index_show 后以 '·' 分隔追加播放量（"xx次观看"），无播放量则不显示
                const viewCount = (item.view && String(item.view) !== '暂无播放量') ? this.escapeHtml(String(item.view)) + '次观看' : '';
                // 追剧接口无版权区域字段，故列表不渲染 .area；链接由 season_id 拼接
                const url = 'https://www.bilibili.com/bangumi/play/ss' + (item.id || '') + '/';
                // 协议相对地址补全 https:（与 dev-1 测试页一致）
                const img = (item.image_url || '').indexOf('//') === 0 ? 'https:' + item.image_url : item.image_url;

                return `
                <article class="list-item">
                    <a class="cover-link" href="${url}" target="_blank" rel="noopener noreferrer" title="${title}">
                        <img class="cover" referrerpolicy="no-referrer" src="${img}" alt="${title}" loading="lazy" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJub25lIi8+PGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNzUsMTUwKSI+PGNpcmNsZSByPSIyNSIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjOGE4YThhIiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNLTEwLTEwaDIwbS0xMCAxMGgyMG0tMTAgMTBoMjAiIHN0cm9rZT0iIzhhOGE4YSIgc3Ryb2tlLXdpZHRoPSIyIiBmaWxsPSJub25lIi8+PC9nPjx0ZXh0IHg9IjEwMCIgeT0iMjEwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM4YThhOGEiIHRleHQtYW5jaG9yPSJtaWRkbGUiPuWbvueJh+WKoOi9veWksei0pTwvdGV4dD48L3N2Zz4='">
                    </a>
                    <div class="item-body">
                        <div class="title-row">
                            <h3 class="item-title"><a href="${url}" target="_blank" rel="noopener noreferrer" title="${title}">${title}</a></h3>
                            ${scoreHtml}
                            ${ratingCountHtml}
                        </div>
                        <div class="badges">
                            <span class="badge type" title="类型">${type}</span>
                            <span class="badge finish" title="更新状态">${finish}</span>
                            <span class="badge follow" title="追剧状态">${followStatus}</span>
                        </div>
                        <div class="index-show">${indexShow}${viewCount ? ' · ' + viewCount : ''}</div>
                        <p class="desc">${evaluate}</p>
                    </div>
                </article>`;
            }

            escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                    '\n': '<br>'
                };
                return text.replace(/[&<>"'\n]/g, m => map[m]);
            }

            showLoading() {
                this.container.append(`
                    <div class="loading-overlay">
                        <div class="loading-spinner"></div>
                    </div>
                `);
                this.loadMoreBtn.addClass('loading').text('加载中...');
            }

            hideLoading() {
                $('.loading-overlay').remove();
                this.loadMoreBtn.removeClass('loading').text('加载更多');
            }

            shouldLoadMore() {
                if (this.isLoading || !this.hasMore) return false;

                const scrollTop = $(window).scrollTop();
                const windowHeight = $(window).height();
                const documentHeight = $(document).height();

                return (scrollTop + windowHeight) >= (documentHeight - 100);
            }

            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        }

        // 初始化
        $(document).ready(function() {
            new BilibiliMovie();
        });

    })(jQuery);
</script>

</article>
		<?php
			if (comments_open() || get_comments_number()) {
				comments_template();
			}
			?>

<?php
get_footer();
