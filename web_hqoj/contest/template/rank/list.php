<?php

$rank = 1;

?>

<style>
    .module-wrap2 {
        width: 100%;
        min-width: 1200px;
    }
    th, td {
        padding: 5px 0;
        text-align: center;
    }
    td {
        height: 36px;
    }
    .td-blood {
        cursor: pointer;
        background-color: #8BDE76;
    }
    .td-green {
        cursor: pointer;
        background-color: #E1FFB5;
    }
    .td-red {
        cursor: pointer;
        background-color: #FFDADA;
    }
</style>
<table class="widget-table">
    <thead>
        <tr>
            <th width="4%">排名</th>
            <th width="12%">用户</th>
            <th width="3%">题数</th>
            <th width="6%">时间</th>
            <?php
                    $width = count($this->contestInfo['problem_hash']) > 0 ? 75/count($this->contestInfo['problem_hash']) : 0;
                    foreach($this->contestInfo['problem_hash'] as $globalId => $no) {
            ?>
                <th width="<?php echo $width; ?>%" >
                    <a href="/status_list/?problem-hash=<?php echo $no; ?>&contest-id=<?php echo $this->contestInfo['id']; ?>" style="color: #333;"><?php echo $no; ?></a>
                </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->rankHash as $userId => $rankInfo) {
                    $userInfo = $this->userHash[$userId];
                    $applyInfo = empty($this->applyHash) ? array() : $this->applyHash[$userInfo['id']];
        ?>
        <tr>
            <td><?php echo $rank++; ?></td>
            <td>
                <a href="/status_list/?username=<?php echo $userInfo['username']; ?>&contest-id=<?php echo $this->contestInfo['id']; ?>">
                    <?php if ($this->isContestAdmin && $this->contestInfo['type'] == ContestVars::TYPE_APPLY) {
                                $tmpUserInfo = $userInfo;
                                $tmpUserInfo['nickname'] = sprintf('<p>%s</p><p>%s</p>', $applyInfo['xuehao'], $applyInfo['real_name']);
                    ?>
                        <?php echo OjCommonHelper::getColorName($tmpUserInfo); ?>
                    <?php } else { ?>
                        <?php echo OjCommonHelper::getColorName($userInfo); ?>
                    <?php } ?>
                </a>
            </td>
            <td><?php echo $rankInfo['solved']; ?></td>
            <td><?php echo $rankInfo['cost_second'] > 9999*60 ? intval($rankInfo['cost_second']/86400).'d' : intval($rankInfo['cost_second']/60); ?></td>
            <?php
            foreach($this->contestInfo['problem_hash'] as $globalId => $no) {
                $class = '';
                if (isset($this->mat[$userId][$globalId])) {
                    if ($this->mat[$userId][$globalId]['accepted']) {
                        $class = 'td-green';
                    } else if ($this->mat[$userId][$globalId]['fail_count']) {
                        $class = 'td-red';
                    }
                    if ($this->mat[$userId][$globalId]['first_blood']) {
                        $class = 'td-blood';
                    }
                }
            ?>
                <td class="<?php echo $class; ?>" username="<?php echo $userInfo['username']; ?>" contest-id=<?php echo $this->contestInfo['id']; ?> problem-hash="<?php echo $no; ?>" >
                    <?php if (!empty($class)) { ?>
                        <?php if ($this->mat[$userId][$globalId]['accepted']) { ?>
                            <p><?php echo $this->mat[$userId][$globalId]['pass_second'] > 86400 ? (int)($this->mat[$userId][$globalId]['pass_second']/86400).'d' : intval($this->mat[$userId][$globalId]['pass_second']/60); ?></p>
                        <?php } ?>
                        <p><?php echo $this->mat[$userId][$globalId]['fail_count']>0 ? '-'.$this->mat[$userId][$globalId]['fail_count'] : ''; ?></p>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    seajs.use(['jquery'], function($) {
        $('.td-blood,.td-green,.td-red').click(function(e) {
            e.preventDefault();
            var url = '/status_list/?username='+$(this).attr('username')+'&problem-hash='+$(this).attr('problem-hash')+'&contest-id='+$(this).attr('contest-id');
            location.href = url;
        });
    });
</script>