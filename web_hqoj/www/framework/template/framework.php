<link type="text/css" href="//sta.hqoj.net/css/www/oj/framework_oj.page.css" rel="stylesheet">

<div class="module-header">
    <div class="module-header2">
        <img src="//sta.hqoj.net/image/www/oj/banner1200x100.jpg" width="1200px" height="100px" />
    </div>
</div>

<div class="module-nav">
    <div class="module-nav2">
        <table class="nav-table">
            <thead>
            <tr>
                <th width="25%">首页</th>
                <th width="25%">题库</th>
                <th width="25%">竞赛</th>
                <th width="25%">用户</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <div>
                        <a href="/">主页</a> |
                        <a href="/doc_list/" <?php echo Url::getPath() == '/doc_list/' ? 'class="selected"' : ''; ?>>F.A.Q</a>
                    </div>
                </td>
                <td>
                    <div>

                        <a href="/problem_list/" <?php echo Router::$CONTROLLER == 'problem_list' && Request::getGET('remote', 0) == 0 ? 'class="selected"' : ''; ?> >HQU</a>
                        <a href="/problem_list/?remote=1" <?php echo Router::$CONTROLLER == 'problem_list' && Request::getGET('remote', 0) == 1 ? 'class="selected"' : ''; ?> >HDU</a>
                        <a href="/problem_list/?remote=2" <?php echo Router::$CONTROLLER == 'problem_list' && Request::getGET('remote', 0) == 2 ? 'class="selected"' : ''; ?> >POJ</a>
                        <a href="/problem_list/?remote=3" <?php echo Router::$CONTROLLER == 'problem_list' && Request::getGET('remote', 0) == 3 ? 'class="selected"' : ''; ?> >ZOJ</a>
                    </div>
                    <div>
                        <a href="/set_list/?from=nav"><span class="<?php echo Cookie::get('current_set') ? 'red ' : ''; ?>fw">专题训练</span></a> |
                        <a href="/status_list/" <?php echo Url::getPath() == '/status_list/' ? 'class="selected"' : ''; ?>>状态</a>
                        <a href="/rank_list/" <?php echo Url::getPath() == '/rank_list/' ? 'class="selected"' : ''; ?>>排名</a>
                    </div>
                </td>
                <td>
                    <div>
                        <a href="/contest_list/" <?php echo Url::getPath() == '/contest_list/' ? 'class="selected"' : ''; ?>>竞赛</a> |
                        <a href="/diy_list/" <?php echo Url::getPath() == '/diy_list/' ? 'class="selected"' : ''; ?>>DIY</a>
                    </div>
                </td>
                <td>
                    <?php if ($this->loginUserInfo) { ?>
                        <div>
                            <a href="/user_my/?username=<?php echo $this->loginUserInfo['username']; ?>">
                                <?php echo $this->loginUserInfo['username']; ?>
                            </a>
                        </div>
                        <div>
                            <?php if ($this->isOjAdmin) { ?>
                                <a href="//admin.hqoj.net">后台</a> |
                            <?php } ?>
                            <a href="/setup_uc_update/">会员中心</a> |
                            <a href="<?php echo Url::make('//uc.hqoj.net/logout/', array('back-url' => Url::getCurrentUrl(array('back-url' => null)))); ?>">退出</a>
                        </div>
                    <?php } else { ?>
                        <div>
                            <a href="<?php echo Url::make('//uc.hqoj.net/login/', array('back-url' => Url::getCurrentUrl(array('back-url' => null)))); ?>">登录</a> |
                            <a href="<?php echo Url::make('//uc.hqoj.net/register/', array('back-url' => Url::getCurrentUrl(array('back-url' => null)))); ?>">注册</a>
                        </div>
                    <?php } ?>

                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="module-wrap">
    <div class="module-wrap2">
        <?php echo $this->frameworkContent; ?>
        <div style="clear:both;"></div>
    </div>
</div>

<div class="module-footer">
    <div class="footer-content">
        <p>Copyright © 2013-<?php echo date('Y', time()); ?> HQU ACM Team. All Rights Reserved.</p>
        <p>开发&设计：敖忠旭</p>
    </div>
</div>
