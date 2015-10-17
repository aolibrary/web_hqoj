<div class="p10">
    <div class="fl"><a href="/set_list/">&lt;&lt; 所有专题列表</a> | <a href="/set_rank/?set-id=<?php echo $this->setInfo['id']; ?>">排名统计</a></div>
    <?php if ($this->loginUserInfo) { ?>
    <div class="fr"><a href="/setup_set_list/">我的专题列表 &gt;&gt;</a></div>
    <?php } ?>
    <div style="clear:both;"></div>
</div>
<div style="padding: 20px; text-align: center;">
    <p style="font-size: 16px; font-weight: 700;"><?php echo $this->setInfo['title']; ?></p>
    <br/>
    <p><?php echo $this->userInfo['username']; ?></p>
</div>

<table class="mt10 mb10 widget-table widget-table-hover">
    <thead>
        <tr>
        <?php if (empty($this->loginUserInfo)) { ?>
            <th class="tc" width="14%">Pro.</th>
            <th width="36%">Title</th>
            <th width="36%">Source</th>
            <th class="tc" width="7%">Solved</th>
            <th class="tc" width="7%">Submit</th>
        <?php } else { ?>
            <th class="tr" width="6%"></th>
            <th width="8%">Pro.</th>
            <th width="36%">Title</th>
            <th width="36%">Source</th>
            <th class="tc" width="7%">Solved</th>
            <th class="tc" width="7%">Submit</th>
        <?php }?>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->problemList as $problemInfo) {
                    $globalId = $problemInfo['id'];
                    $solutionInfo = Arr::get($globalId, $this->userSolution, array());
        ?>
        <tr>
            <?php if (!empty($this->loginUserInfo)) { ?>
                <td class="tr">
                    <?php if (Arr::get('result', $solutionInfo, -1) == StatusVars::ACCEPTED) { ?>
                        <img src="//sta.hqoj.net/image/www/oj/problem-ac.png" height="16px" />
                    <?php } else if (!empty($solutionInfo)) { ?>
                        <img src="//sta.hqoj.net/image/www/oj/problem-no.png" height="16px" />
                    <?php } ?>
                </td>
            <?php } ?>
            <td class="<?php echo $this->loginUserInfo ? '' : 'tc'; ?>">
                <?php echo $problemInfo['remote'] ? StatusVars::$REMOTE_SCHOOL[$problemInfo['remote']] : ''; echo $problemInfo['problem_code']; ?>
            </td>
            <td>
                <a href="/problem_detail/?global-id=<?php echo $problemInfo['id']; ?>"><?php echo $problemInfo['title']; ?></a>
            </td>
            <td>
                <a href="/problem_list/?remote=<?php echo $problemInfo['remote']; ?>&type=2&keyword=<?php echo $problemInfo['source']; ?>"><?php echo $problemInfo['source']; ?></a>
            </td>
            <td class="tc"><a href="<?php echo OjCommonHelper::getStatusUrl('', $problemInfo['remote'], $problemInfo['problem_code'], StatusVars::ACCEPTED); ?>"><?php echo $problemInfo['solved']; ?></a></td>
            <td class="tc"><a href="<?php echo OjCommonHelper::getStatusUrl('', $problemInfo['remote'], $problemInfo['problem_code'], -1); ?>"><?php echo $problemInfo['submit']; ?></a></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
