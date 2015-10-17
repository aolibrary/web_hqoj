<style>
    .module-wrap2 {
        width: 100%;
        min-width: 1200px;
    }
    th, td {
        padding-left: 0;
        padding-right: 0;
        text-align: center;
    }
    .mylink {
        padding: 10px;
        width: 1200px;
        margin: 0 auto;
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

<div class="mylink"><a href="/set_problem/?set-id=<?php echo $this->setInfo['id']; ?>">&lt;&lt; 返回专题</a></div>

<table class="widget-table">
    <thead>
        <tr>
            <th width="4%">排名</th>
            <th width="12%">用户</th>
            <th width="4%">题数</th>
            <?php
                    $width = count($this->setInfo['global_ids']) > 0 ? 80/count($this->setInfo['global_ids']) : 0;
                    foreach($this->setInfo['global_ids'] as $i => $globalId) {
            ?>
                <th width="<?php echo $width; ?>%" >
                    <a href="/problem_detail/?global-id=<?php echo $globalId; ?>" style="color: #333;"><?php echo sprintf('%02d', $i+1); ?></a>
                </th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php   $rank = 1;
                foreach ($this->rankHash as $userId => $rankInfo) {
                    $userInfo = $this->userHash[$userId];
        ?>
        <tr>
            <td><?php echo $rank++; ?></td>
            <td>
                <a href="/user_my/?username=<?php echo $userInfo['username']; ?>"><?php echo OjCommonHelper::getColorName($userInfo); ?></a>
            </td>
            <td><?php echo $rankInfo['solved']; ?></td>
            <?php
            foreach($this->setInfo['global_ids'] as $globalId) {
                $class = '';
                if (isset($this->mat[$userId][$globalId])) {
                    $block = $this->mat[$userId][$globalId];
                    if (isset($block['accepted']) && $block['accepted']) {
                        $class = 'td-green';
                    } else if (isset($block['fail_count']) && $block['fail_count']) {
                        $class = 'td-red';
                    }
                    if (isset($block['first_blood']) && $block['first_blood']) {
                        $class = 'td-blood';
                    }
                }

            ?>
                <td class="<?php echo $class; ?>" username="<?php echo $userInfo['username']; ?>" global-id="<?php echo $globalId; ?>" >
                    <?php
                    if (!empty($class)) {
                        $failCount = Arr::get('fail_count', $this->mat[$userId][$globalId], 0);
                        echo $failCount > 0 ? $failCount : '';
                    }
                    ?>
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
            var url = '/status_list/?username='+$(this).attr('username')+'&global-id='+$(this).attr('global-id');
            location.href = url;
        });
    });
</script>

