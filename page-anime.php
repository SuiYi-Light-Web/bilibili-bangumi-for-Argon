<?php

/**
 * Template Name: B站追番页面
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

    html.darkmode body {

        --bili-banner-bg: linear-gradient(135deg, #3a3a3a 0%, #2c2c2c 100%);
        --bili-overlay-bg: rgba(66, 66, 66, 0.97);
        --bili-overlay-text: #ccc;
        --bili-overlay-meta: #bbb;
        --bili-overlay-border: #555;
        --bili-title-color: #eee;
        --bili-content-border: #555;
        --bili-badge-bg: #2c2e33;
        --bili-badge-color: #99a2aa;

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

    .bilibili-Anime-container .loading-text {
        color: var(--bili-muted);
        font-style: italic;
    }

    .bilibili-Anime-container .load-more-btn {
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

    .bilibili-Anime-container .load-more-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(var(--themecolor-rgbstr), 0.45);
    }

    .bilibili-Anime-container .load-more-btn.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .bilibili-Anime-container .no-more-text {
        text-align: center;
        color: var(--bili-muted);
        padding: 20px;
        font-size: 14px;
    }

    .bilibili-Anime-container .loading-overlay {
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

    .bilibili-Anime-container .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid var(--bili-spinner-bg);
        border-top: 3px solid var(--themecolor);
        border-radius: 50%;
        animation: bili-spin 1s linear infinite;
    }

    @keyframes bili-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .bilibili-Anime-container {
        position: relative;
        min-height: 300px;
    }

    .bilibili-Anime-container .error-message {
        width: 100%;
        text-align: center;
        color: var(--bili-error-color);
        padding: 20px;
        background: var(--bili-error-bg);
        border-radius: 8px;
        margin: 10px 0;
    }

    .bilibili-Anime-container .anime-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin: 10px 0;
    }

    .bilibili-Anime-container .list-item {
        display: flex;
        gap: 16px;
        padding: 16px;
        background: var(--bili-card-bg);
        border: 1px solid var(--bili-content-border);
        border-radius: 12px;
        box-shadow: var(--bili-shadow);
        transition: box-shadow .2s, transform .2s;
    }

    .bilibili-Anime-container .list-item:hover {
        box-shadow: var(--bili-shadow-hover);
        transform: translateY(-2px);
    }

    .bilibili-Anime-container .cover-link {
        flex: 0 0 auto;
        display: block;
    }

    .bilibili-Anime-container .cover {
        width: 150px;
        aspect-ratio: 3 / 4;
        object-fit: cover;
        border-radius: 8px;
        display: block;
        background: var(--bili-badge-bg);
    }

    .bilibili-Anime-container .item-body {
        flex: 1;
        min-width: 0;
    }

    .bilibili-Anime-container .title-row {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        column-gap: 8px;
        row-gap: 2px;
    }

    .bilibili-Anime-container .item-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.4;
        color: var(--bili-title-color);
    }

    .bilibili-Anime-container .item-title a {
        color: inherit;
        text-decoration: none;
    }

    .bilibili-Anime-container .item-title a:hover {
        text-decoration: underline;
    }

    html.darkmode .bilibili-Anime-container .item-title,
    html.darkmode .bilibili-Anime-container .item-title a,
    html.darkmode .bilibili-Anime-container .item-title a:hover {
        color: #fff;
    }

    .bilibili-Anime-container .score {
        font-size: 13px;
        font-weight: 700;
        color: #ffa726;
    }

    .bilibili-Anime-container .rating-count {
        font-size: 12px;
        color: var(--bili-muted);
    }

    .bilibili-Anime-container .badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .bilibili-Anime-container .anime-list .badge {
        font-size: 11px;
        line-height: 1;
        padding: 4px 9px;
        border-radius: 999px;
        font-weight: 500;
        white-space: nowrap;
    }

    .bilibili-Anime-container .anime-list .badge.type {
        background: var(--bili-type-bg);
        color: var(--bili-type-color);
    }

    .bilibili-Anime-container .anime-list .badge.finish {
        background: var(--bili-finish-bg);
        color: var(--bili-finish-color);
    }

    .bilibili-Anime-container .anime-list .badge.follow {
        background: var(--bili-follow-bg);
        color: var(--bili-follow-color);
    }

    html.darkmode .bilibili-Anime-container .anime-list .badge.type {
        color: var(--bili-type-color);
    }

    html.darkmode .bilibili-Anime-container .anime-list .badge.finish {
        color: var(--bili-finish-color);
    }

    html.darkmode .bilibili-Anime-container .anime-list .badge.follow {
        color: var(--bili-follow-color);
    }

    .bilibili-Anime-container .index-show {
        margin-top: 8px;
        font-size: 12px;
        color: var(--bili-text);
        opacity: .85;
    }

    .bilibili-Anime-container .desc {
        margin: 10px 0 0;
        font-size: 13px;
        line-height: 1.65;
        color: var(--bili-muted);
        white-space: pre-line;
        overflow-wrap: break-word;
    }

    .bilibili-Anime-container .area {
        margin-top: 4px;
        font-size: 12px;
        color: var(--bili-muted);
    }

    .bilibili-Anime-container .page-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--themecolor);
    }

    .bilibili-Anime-container .page-header h2 {
        margin: 0;
        color: var(--bili-title-color);
        font-size: 22px;
    }

    .bilibili-Anime-container .page-header small {
        color: var(--bili-overlay-meta);
        font-size: 13px;
        margin-left: 8px;
        font-weight: normal;
    }

    @media (max-width: 520px) {
        .bilibili-Anime-container .cover {
            width: 108px;
        }

        .bilibili-Anime-container .item-title {
            font-size: 16px;
        }

        .bilibili-Anime-container .list-item {
            gap: 12px;
            padding: 12px;
        }
    }

    @media (max-width: 400px) {
        .bilibili-Anime-container .page-header h2 {
            font-size: 18px;
        }

        .bilibili-Anime-container .page-header small {
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
    <div id="bilibiliAnime" class="anime-list"></div>
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

                this.loadMoreBtn.on('click', function() {
                    if (!self.isLoading && self.hasMore) {
                        self.loadData();
                    }
                });

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

                if (typeof data.total !== 'undefined' && data.total !== null) {
                    this.totalSpan.text(data.total).removeClass('loading-text');
                }

                this.renderItems(data.data);

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
                const scoreHtml = item.rating_score ? `<span class="score">${item.rating_score}</span>` : '';
                const ratingCountHtml = item.rating_count ? `<span class="rating-count">${item.rating_count}人</span>` : '';
                const type = item.type ? this.escapeHtml(item.type) : '未知';
                const finish = item.finish ? this.escapeHtml(item.finish) : '未知';
                const followStatus = item.follow_status ? this.escapeHtml(item.follow_status) : '未知';
                const indexShow = item.index_show ? this.escapeHtml(item.index_show) : '暂无剧集信息';
                const viewCount = (item.view && String(item.view) !== '暂无播放量') ? this.escapeHtml(String(item.view)) + '次观看' : '';
                const rightArea = this.escapeHtml(item.right_area || '未知');
                const img = (item.image_url || '').indexOf('//') === 0 ? 'https:' + item.image_url : item.image_url;

                return `
                <article class="list-item">
                    <a class="cover-link" href="${item.url}" target="_blank" rel="noopener noreferrer" title="${title}">
                        <img class="cover" referrerpolicy="no-referrer" src="${img}" alt="${title}" loading="lazy" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJub25lIi8+PGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNzUsMTUwKSI+PGNpcmNsZSByPSIyNSIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjOGE4YThhIiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNLTEwLTEwaDIwbS0xMCAxMGgyMG0tMTAgMTBoMjAiIHN0cm9rZT0iIzhhOGE4YSIgc3Ryb2tlLXdpZHRoPSIyIiBmaWxsPSJub25lIi8+PC9nPjx0ZXh0IHg9IjEwMCIgeT0iMjEwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTIiIGZpbGw9IiM4YThhOGEiIHRleHQtYW5jaG9yPSJtaWRkbGUiPuWbvueJh+WKoOi9veWksei0pTwvdGV4dD48L3N2Zz4='">
                    </a>
                    <div class="item-body">
                        <div class="title-row">
                            <h3 class="item-title"><a href="${item.url}" target="_blank" rel="noopener noreferrer" title="${title}">${title}</a></h3>
                            ${scoreHtml}
                            ${ratingCountHtml}
                        </div>
                        <div class="badges">
                            <span class="badge type" title="类型">${type}</span>
                            <span class="badge finish" title="更新状态">${finish}</span>
                            <span class="badge follow" title="追番状态">${followStatus}</span>
                        </div>
                        <div class="index-show">${indexShow}${viewCount ? ' · ' + viewCount : ''}</div>
                        <p class="desc">${evaluate}</p>
                        <div class="area">版权地区：${rightArea}</div>
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
