<?php

/**
 Template Name: B站追番页面
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
        --bili-content-border: #f0f0f0;
        --bili-badge-bg: #f5f5f5;
        --bili-badge-color: #666;
        --bili-type-bg: #e3f2fd;
        --bili-type-color: #1976d2;
        --bili-finish-bg: #e8f5e9;
        --bili-finish-color: #388e3c;
        --bili-follow-bg: #fff3e0;
        --bili-follow-color: #f57c00;
        --bili-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        --bili-shadow-hover: 0 5px 20px rgba(0, 0, 0, 0.15);
        --bili-scrollbar-track: #f1f1f1;
        --bili-spinner-bg: #f3f3f3;
        --bili-loading-bg: rgba(255, 255, 255, 0.8);
        --bili-muted: #999;
        --bili-error-bg: #ffebee;
        --bili-error-color: #f44336;
    }

    /* 暗黑模式（与主题变量同元素定义，var() 引用自动解析为暗色值） */
    html.darkmode body {
        /* --bili-link-bg / --bili-link-color / --bili-link-color-hover / --bili-title-color
           无需覆盖：这些变量在 body 上引用 var(--color-foreground) / var(--color-text-deeper)，
           暗黑模式下自动解析为 #424242 / #eee，OLED 下自动为 #000 / #eee */
        --bili-banner-bg: linear-gradient(135deg, #3a3a3a 0%, #2c2c2c 100%);
        --bili-overlay-bg: rgba(66, 66, 66, 0.97);
        --bili-overlay-text: #ccc;
        --bili-overlay-meta: #bbb;
        --bili-overlay-border: #555;
        --bili-title-color: #eee;
        --bili-content-border: #555;
        --bili-badge-bg: #555;
        --bili-badge-color: #ccc;
        --bili-type-bg: rgba(25, 118, 210, 0.28);
        --bili-type-color: #82b1ff;
        --bili-finish-bg: rgba(56, 142, 60, 0.28);
        --bili-finish-color: #81c784;
        --bili-follow-bg: rgba(245, 124, 0, 0.28);
        --bili-follow-color: #ffb74d;
        --bili-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
        --bili-shadow-hover: 0 6px 20px rgba(0, 0, 0, 0.55);
        --bili-scrollbar-track: #333;
        --bili-spinner-bg: #666;
        --bili-loading-bg: rgba(66, 66, 66, 0.8);
        --bili-muted: #999;
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

    .bilibili-Anime-container {
        position: relative;
        min-height: 300px;
    }

    .row {
        margin: 0 10px;
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

    /* 追番卡片样式 */
    .bangumi-item {
        margin: 20px 0;
        padding: 0;
        border: none;
        transition: transform 0.3s ease;
    }

    .bangumi-item:hover {
        transform: translateY(-5px);
    }

    .bangumi-link {
        display: block;
        padding: 0;
        border: none;
        text-decoration: none;
        color: var(--bili-link-color);
        border-radius: 8px;
        overflow: hidden;
        background: var(--bili-link-bg);
        box-shadow: var(--bili-shadow);
        transition: box-shadow 0.3s ease, color 0.3s ease;
        height: 100%;
    }

    .bangumi-link:hover {
        box-shadow: var(--bili-shadow-hover);
        color: var(--bili-link-color-hover);
    }

    /* 暗黑模式下主题自带 a 标签配色优先级更高，此处显式覆盖 */
    html.darkmode .bangumi-link {
        color: var(--bili-link-color);
    }

    html.darkmode .bangumi-link:hover {
        color: var(--bili-link-color-hover);
    }

    .bangumi-banner {
        position: relative;
        overflow: hidden;
        background: var(--bili-banner-bg);
    }

    .bangumi-banner img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .bangumi-link:hover .bangumi-banner img {
        transform: scale(1.05);
    }

    .bangumi-des {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--bili-overlay-bg);
        padding: 15px;
        opacity: 0;
        transition: all 0.3s ease;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        z-index: 2;
    }

    .bangumi-banner:hover .bangumi-des {
        opacity: 1;
    }

    .bangumi-des p {
        margin: 0 0 15px 0;
        font-size: 13px;
        line-height: 1.6;
        color: var(--bili-overlay-text);
        overflow-y: auto;
        flex: 1;
        text-align: left;
        padding-right: 5px;
        max-height: calc(100% - 60px);
        word-wrap: break-word;
        white-space: normal;
    }

    .bangumi-des p::-webkit-scrollbar {
        width: 4px;
    }

    .bangumi-des p::-webkit-scrollbar-track {
        background: var(--bili-scrollbar-track);
        border-radius: 2px;
    }

    .bangumi-des p::-webkit-scrollbar-thumb {
        background: #ffa726;
        border-radius: 2px;
    }

    .bangumi-des p::-webkit-scrollbar-thumb:hover {
        background: var(--themecolor);
    }

    .bangumi-des .score-info {
        font-size: 13px;
        color: var(--bili-overlay-meta);
        text-align: left;
        padding-top: 10px;
        border-top: 1px solid var(--bili-overlay-border);
        margin-top: auto;
    }

    .bangumi-des .score-info .score {
        color: #ff9800;
        font-weight: bold;
        margin-left: 5px;
    }

    .bangumi-content {
        padding: 15px 10px 5px;
        border-bottom: solid 1px var(--bili-content-border);
    }

    .bangumi-title {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        color: var(--bili-title-color);
        line-height: 1.4;
    }

    .bangumi-title .score {
        margin-left: 8px;
        color: #ffa726;
        font-weight: bold;
        font-size: 13px;
    }

    .bangumi-status {
        padding: 10px;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
        min-height: 50px;
    }

    .bangumi-follow_status,
    .bangumi-type,
    .bangumi-finish {
        font-size: 11px;
        height: 22px;
        padding: 0 8px;
        line-height: 22px;
        border-radius: 11px;
        background: var(--bili-badge-bg);
        color: var(--bili-badge-color);
        font-weight: 500;
        white-space: nowrap;
    }

    .bangumi-type {
        background: var(--bili-type-bg);
        color: var(--bili-type-color);
    }

    .bangumi-finish {
        background: var(--bili-finish-bg);
        color: var(--bili-finish-color);
    }

    .bangumi-follow_status {
        background: var(--bili-follow-bg);
        color: var(--bili-follow-color);
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
    @media (max-width: 1199px) {
        .bangumi-item {
            margin: 15px 0;
        }

    }

    @media (max-width: 991px) {
        .bangumi-des p {
            font-size: 12.5px;
            line-height: 1.5;
        }


        .bangumi-title {
            font-size: 14px;
        }
    }

    @media (max-width: 767px) {
        .row {
            margin: 0 5px;
        }

        .bangumi-item {
            margin: 10px 0;
        }



        .bangumi-title {
            font-size: 14px;
        }

        .page-header h2 {
            font-size: 20px;
        }

        .bangumi-des {
            padding: 10px;
        }

        .bangumi-des p {
            font-size: 12px;
            max-height: calc(100% - 50px);
        }
    }

    @media (max-width: 575px) {


        .bangumi-des {
            padding: 8px;
        }

        .bangumi-des p {
            font-size: 11.5px;
            line-height: 1.4;
            max-height: calc(100% - 45px);
        }

        .bangumi-des .score-info {
            font-size: 12px;
        }

        .bangumi-status {
            min-height: 45px;
            padding: 8px;
        }

        .bangumi-follow_status,
        .bangumi-type,
        .bangumi-finish {
            font-size: 10px;
            height: 20px;
            line-height: 20px;
            padding: 0 6px;
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

<div class="bilibili-Anime-container">
    <div class="page-header">
        <h2>我的追番 <small>当前已追<span id="total" class="loading-text">加载中...</span>部，继续加油！</small></h2>
    </div>
    <div id="bilibiliAnime" class="row"></div>
    <div id="loadMore" class="load-more-btn">加载更多</div>
    <div id="noMoreData" class="no-more-text" style="display: none;">已经到底了哦~</div>
</div>

<script type="text/javascript">
    (function($) {
        'use strict';

        class bilibiliAnime {
            constructor() {
                this.pageNum = 0;
                this.limit = 24;
                this.isLoading = false;
                this.hasMore = true;
                this.container = $('#bilibiliAnime');
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
                    url: "/bilibili-api/GetAnimeData.php",
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
                const ratingScore = item.rating_score || '暂无评分';
                const ratingCount = item.rating_count || '不足/未开放';
                const type = item.type || '未知';
                const finish = item.finish || '未知';
                const followStatus = item.follow_status || '未知';
                const index_show = item.index_show || '暂无剧集信息';

                return `
                <div class="bangumi-item col-md-4 col-lg-3 col-sm-6">
                    <a class="no-line bangumi-link" href="${item.url}" target="_blank" rel="noopener noreferrer" title="${title}">
                        <div class="bangumi-banner">
                            <img referrerpolicy="no-referrer" src="${item.image_url}" alt="${title}" loading="lazy" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJub25lIi8+PGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNzUsMTUwKSI+PGNpcmNsZSByPSIyNSIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjOGE4YThhIiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNLTEwLTEwaDIwbS0xMCAxMGgyMG0tMTAgMTBoMjAiIHN0cm9rZT0iIzhhOGE4YSIgc3Ryb2tlLXdpZHRoPSIyIiBmaWxsPSJub25lIi8+PC9nPjx0ZXh0IHg9IjEwMCIgeT0iMjEwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM4YThhOGEiIHRleHQtYW5jaG9yPSJtaWRkbGUiPuWbvueJh+WKoOi9veWksei0pTwvdGV4dD48L3N2Zz4='">
                            <div class="bangumi-des">
                                <p>《${title}》【${index_show}】
                                <br>${evaluate}</p>
                                <div class="score-info">
                                    B站评分<span class="score">${ratingScore}</span>
                                    <br>评分人数 ${ratingCount}
                                    <br>版权区域 ${item.right_area}
                                </div>
                            </div>
                        </div>
                        <div class="bangumi-content">
                            <div class="bangumi-title" title="${title}">
                                ${title}
                                <span class="score"><small>${item.rating_score || ''}</small></span>
                            </div>
                        </div>
                        <div class="bangumi-status">
                            <span class="bangumi-type" title="类型">${type}</span>
                            <span class="bangumi-finish" title="更新状态">${finish}</span>
                            <span class="bangumi-follow_status" title="追番状态">${followStatus}</span>
                        </div>
                    </a>
                </div>`;
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
            new bilibiliAnime();
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
