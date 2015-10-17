<div style="padding: 10px 200px;">
    <div class="tc f18 mb10"><?php echo $this->contestInfo['title']; ?></div>
    <div class="tc mb10">比赛时间：<?php echo date('Y-m-d H:i:s', $this->contestInfo['begin_time']) . ' - ' . date('Y-m-d H:i:s', $this->contestInfo['end_time']); ?></div>
    <div class="tc" style="margin-bottom: 10px;">
        <?php if (time() < $this->contestInfo['begin_time']) { ?>
            <span class="green">比赛未开始</span>
        <?php } else if (time() > $this->contestInfo['end_time']) { ?>
            <span class="gray">比赛已结束</span>
        <?php } else { ?>
            <span class="red">比赛进行中</span>
        <?php } ?>
    </div>
    
    <div class="tc orange" style="margin-bottom: 20px;">当前时间：<?php echo date('Y-m-d H:i:s', time()); ?></div>
    
    <table class="widget-table widget-table-hover">
        <thead>
            <tr>
                <th class="tc" width="7%">&nbsp;</th>
                <th class="tc" width="8%">&nbsp;</th>
                <th width="55%">标题</th>
                <th class="tc" width="15%">通过人数</th>
                <th class="tc" width="15%">提交次数</th>
            </tr>
        </thead>
        <tbody>
            <?php   foreach ($this->contestInfo['global_ids'] as $globalId) {
                        $problemInfo = $this->problemHash[$globalId];
                        $solutionInfo = Arr::get($globalId, $this->userSolution, array());
                        $hashKey = $this->contestInfo['problem_hash'][$globalId];
            ?>
            <tr>
                <td class="tr">
                    <?php if (!empty($solutionInfo) && $solutionInfo['result'] == StatusVars::ACCEPTED) { ?>
                        <img src="//sta.hqoj.net/image/www/oj/problem-ac.png" height="16px" />
                    <?php } else if (!empty($solutionInfo)) { ?>
                        <img src="//sta.hqoj.net/image/www/oj/problem-no.png" height="16px" />
                    <?php } ?>
                </td>
                <td class="tl"><?php echo $this->contestInfo['problem_hash'][$globalId]; ?></td>
                <td>
                    <?php if ($this->contestInfo['problem_hidden']) { ?>
                        <a href="/problem_detail/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $hashKey; ?>">Problem <?php echo $hashKey; ?></a>
                    <?php } else { ?>
                        <a href="/problem_detail/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $hashKey; ?>"><?php echo $problemInfo['title']; ?></a>
                    <?php }?>
                </td>
                <td class="tc">
                    <a href="/status_list/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $hashKey; ?>&result=<?php echo StatusVars::ACCEPTED; ?>">
                        <?php echo $problemInfo['contest_solved']; ?>
                    </a>
                </td>
                <td class="tc">
                    <a href="/status_list/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $hashKey; ?>">
                        <?php echo $problemInfo['contest_submit']; ?>
                    </a>
                </td>
            </tr>
            <?php   } ?>
        </tbody>
    </table>

</div>
