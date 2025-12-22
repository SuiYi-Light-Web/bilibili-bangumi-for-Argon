<?php

/**
 Template Name: B站追剧页面
 Template author: 随意之光&DeepSeek重构，原作 阿肾、蘑菇君
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
    /* 布局和加载样式 */
    .loading-text {
        color: #999;
        font-style: italic;
    }
    
    .load-more-btn {
        margin: 30px auto;
        padding: 12px 30px;
        background: linear-gradient(var(--themecolor));
        color: white;
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
        box-shadow: 0 5px 15px var(--themecolor);
    }
    
    .load-more-btn.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .no-more-text {
        text-align: center;
        color: #999;
        padding: 20px;
        font-size: 14px;
    }
    
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
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
    
    .row {
        margin: 0 10px;
    }
    
    /* 追剧卡片样式 */
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
        color: #000;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
        height: 100%;
    }
    
    .bangumi-link:hover {
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        color: #000;
    }
    
    .bangumi-banner {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
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
        background: rgba(255, 255, 255, 0.97);
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
        color: #444;
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
        background: #f1f1f1;
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
        color: #666;
        text-align: left;
        padding-top: 10px;
        border-top: 1px solid #eee;
        margin-top: auto;
    }
    
    .bangumi-des .score-info .score {
        color: #ff9800;
        font-weight: bold;
        margin-left: 5px;
    }
    
    .bangumi-content {
        padding: 15px 10px 5px;
        border-bottom: solid 1px #f0f0f0;
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
        color: #333;
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
        background: #f5f5f5;
        color: #666;
        font-weight: 500;
        white-space: nowrap;
    }
    
    .bangumi-type {
        background: #e3f2fd;
        color: #1976d2;
    }
    
    .bangumi-finish {
        background: #e8f5e9;
        color: #388e3c;
    }
    
    .bangumi-follow_status {
        background: #fff3e0;
        color: #f57c00;
    }
    
    .page-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--themecolor);
    }
    
    .page-header h2 {
        margin: 0;
        color: #333;
        font-size: 22px;
    }
    
    .page-header small {
        color: #666;
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

<div class="bilibili-movie-container">
    <div class="page-header">
        <h2>我的追剧 <small>当前已追<span id="total" class="loading-text">加载中...</span>部，继续加油！</small></h2>
    </div>
    <div id="bilibiliMovie" class="row"></div>
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
                if (data.total) {
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
                        <div class="error-message" style="
                            width: 100%;
                            text-align: center;
                            color: #f44336;
                            padding: 20px;
                            background: #ffebee;
                            border-radius: 8px;
                            margin: 10px 0;
                        ">
                            ${message}
                        </div>
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
                const ratingScore = item.rating_score || '暂无';
                const ratingCount = item.rating_count || 0;
                const type = item.type || '未知';
                const finish = item.finish || '未知';
                const followStatus = item.follow_status || '未知';
                
                return `
                <div class="bangumi-item col-md-4 col-lg-3 col-sm-6">
                    <a class="no-line bangumi-link" href="https://www.bilibili.com/bangumi/play/ss${item.id}/" target="_blank" rel="noopener noreferrer" title="${title}">
                        <div class="bangumi-banner">
                            <img referrerpolicy="no-referrer" src="${item.image_url}" alt="${title}" loading="lazy" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjVmNWY1Ii8+PGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNzUsMTUwKSI+PGNpcmNsZSByPSIyNSIgZmlsbD0iI2UwZTBlMCIvPjxwYXRoIGQ9Ik0tMTAtMTBoMjBtLTEwIDEwaDIwbS0xMCAxMGgyMCIgc3Ryb2tlPSIjY2NjIiBzdHJva2Utd2lkdGg9IjIiIGZpbGw9Im5vbmUiLz48L2c+PHRleHQgeD0iMTAwIiB5PSIyMDAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzg4OCIgdGV4dC1hbmNob3I9Im1pZGRsZSI+5Zu+54mH5paH5pysPC90ZXh0Pjwvc3ZnPg=='">
                            <div class="bangumi-des">
                                <p>《${title}》【${item.index_show}】
                                <br>${evaluate}</p>
                                <div class="score-info">
                                    B站评分<span class="score">${ratingScore}</span>
                                    <br>评分人数 ${ratingCount}
                                </div>
                            </div>
                        </div>
                        <div class="bangumi-content">
                            <div class="bangumi-title" title="${title}">
                                ${title}
                                <span class="score"><small>${item.rating_score || '0.0'}</small></span>
                            </div>
                        </div>
                        <div class="bangumi-status">
                            <span class="bangumi-type" title="类型">${type}</span>
                            <span class="bangumi-finish" title="更新状态">${finish}</span>
                            <span class="bangumi-follow_status" title="追剧状态">${followStatus}</span>
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