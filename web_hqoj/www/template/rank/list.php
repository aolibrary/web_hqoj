<?php

$rank = $this->beginRank;

?>

<div class="mb10 p10 tc f16 fw">排行榜</div>

<?php echo $this->html['pager']; ?>

<table class="mt10 widget-table widget-table-hover">
    <thead>
        <tr>
            <th width="10%" class="tc">排名</th>
            <th width="10%">头像</th>
            <th width="15%">昵称</th>
            <th width="45%">个性签名</th>
            <th width="10%" class="tc">Solved</th>
            <th width="10%" class="tc">Submit</th>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->userList as $userInfo) { ?>
            <tr style="height: 69px;">
                <td class="tc"><?php echo $rank++; ?></td>
                <td>
                    <a href="/user_my/?username=<?php echo $userInfo['username']; ?>">
                        <img style="border: 0;" src="//sta.hqoj.net/image/common/loading/loading04.gif" data-original="<?php echo OjCommonHelper::getHeadUrl($userInfo['head_img'], $userInfo['sex']); ?>" width="48px" height="48px" />
                    </a>
                </td>
                <td><a href="/user_my/?username=<?php echo $userInfo['username']; ?>"><?php echo OjCommonHelper::getColorName($userInfo); ?></a></td>
                <td><?php echo $userInfo['motto']; ?></td>
                <td class="tc">
                    <a href="<?php echo OjCommonHelper::getStatusUrl($userInfo['username'], -1, '', StatusVars::ACCEPTED); ?>"><?php echo $userInfo['solved_all']; ?></a>
                </td>
                <td class="tc">
                    <a href="<?php echo OjCommonHelper::getStatusUrl($userInfo['username'], -1, '', -1); ?>"><?php echo $userInfo['submit_all']; ?></a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>



