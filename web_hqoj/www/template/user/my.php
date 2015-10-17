<style>
    .user-info-div { margin: 0 auto;  }
    .user-info-div .title { margin-left: 10px; font-size: 28px; font-weight: 700; }
    .user-info-div .info { margin-top: 10px; width: 100%; }
    .user-info-div .col1 { width: 15%; padding: 20px; text-align: center; background-color: #f5f5f5; }
    .user-info-div th,
    .user-info-div td { border: 1px solid #ddd; }
    .user-info-div .main-info { width: 800px; }
    .user-info-div .main-info th,
    .user-info-div .main-info td { text-align: center; border: 1px solid #ddd; }
</style>

<div class="user-info-div">
    <div>
        <span class="title"><?php echo $this->userInfo['nickname']; ?></span>&nbsp;&nbsp;&nbsp;
        <span>注册时间：<?php echo date('Y-m-d H:i:s', $this->userInfo['create_time']); ?></span>
    </div>
    <table class="info">
        <tbody>
            <tr>
                <td class="col1">帐号</td>
                <td><?php echo $this->userInfo['username']; ?></td>
            </tr>
            <tr>
                <td class="col1">头像</td>
                <td>
                    <img style="border: 1px solid #ddd;" src="<?php echo OjCommonHelper::getHeadUrl($this->userInfo['head_img'], $this->userInfo['sex']); ?>" width="256px" height="256px" />
                </td>
            </tr>
            <tr>
                <td class="col1">性别</td>
                <td>
                    <?php
                        if ($this->userInfo['sex']) {
                            echo $this->userInfo['sex'] == 1 ? '男' : '女';
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="col1">座右铭</td>
                <td><?php echo empty($this->userInfo['motto']) ? '这家伙没有留下任何痕迹！' : $this->userInfo['motto']; ?></td>
            </tr>
            <tr>
                <td class="col1">概述</td>
                <td>
                    <table class="main-info">
                        <tr>
                            <th width="16%">总排名</th>
                            <th width="14%">HQU题数</th>
                            <th width="14%">HDU题数</th>
                            <th width="14%">POJ题数</th>
                            <th width="14%">ZOJ题数</th>
                            <th width="14%">总解决题数</th>
                            <th width="14%">总提交次数</th>
                        </tr>
                        <tr>
                            <td><?php echo $this->rank ? $this->rank : '-'; ?></td>
                            <td><a href="<?php echo OjCommonHelper::getStatusUrl($this->userInfo['username'], 0, '', StatusVars::ACCEPTED); ?>"><?php echo $this->userInfo['solved_hqu']; ?></a></td>
                            <td><a href="<?php echo OjCommonHelper::getStatusUrl($this->userInfo['username'], 1, '', StatusVars::ACCEPTED); ?>"><?php echo $this->userInfo['solved_hdu']; ?></a></td>
                            <td><a href="<?php echo OjCommonHelper::getStatusUrl($this->userInfo['username'], 2, '', StatusVars::ACCEPTED); ?>"><?php echo $this->userInfo['solved_poj']; ?></a></td>
                            <td><a href="<?php echo OjCommonHelper::getStatusUrl($this->userInfo['username'], 3, '', StatusVars::ACCEPTED); ?>"><?php echo $this->userInfo['solved_zoj']; ?></a></td>
                            <td><a href="<?php echo OjCommonHelper::getStatusUrl($this->userInfo['username'], -1, '', StatusVars::ACCEPTED); ?>"><?php echo $this->userInfo['solved_all']; ?></a></td>
                            <td><a href="<?php echo OjCommonHelper::getStatusUrl($this->userInfo['username'], -1, '', -1); ?>"><?php echo $this->userInfo['submit_all']; ?></a></td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col1">
                    <p>题库中已解决的题目</p>
                    <p>（不包括竞赛中的提交）</p>
                </td>
                <td>
                    <?php   foreach ($this->solvedProblemList as $problemInfo) {
                                $remote = $problemInfo['remote'];
                                $problemCode = $problemInfo['problem_code'];
                    ?>
                        <div class="fl ml5 w60">
                            <a href="<?php echo OjCommonHelper::getStatusUrl($this->userInfo['username'], $remote, $problemCode, StatusVars::ACCEPTED); ?>">
                                <?php echo $remote ? StatusVars::$REMOTE_SCHOOL[$remote] : ''; echo $problemCode; ?>
                            </a>
                        </div>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td class="col1">解决中</td>
                <td>
                    <?php   foreach ($this->unSolvedProblemList as $problemInfo) {
                                $remote = $problemInfo['remote'];
                                $problemCode = $problemInfo['problem_code'];
                    ?>
                        <div class="fl ml5 w60"><a href="<?php echo OjCommonHelper::getStatusUrl($this->userInfo['username'], $remote, $problemCode, -1); ?>"><?php echo $remote ? StatusVars::$REMOTE_SCHOOL[$remote] : ''; echo $problemCode; ?></a></div>
                    <?php } ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
