<div class="mb10 p10 tc f16 fw"><?php echo StatusVars::$REMOTE_SCHOOL_NAME[Request::getGET('remote', 0)]; ?>题库</div>

<form class="mb10 bg-gray widget-form widget-form-toolbar" method="get">
    <label class="label">查找题目：</label>
    <input class="w400 input" type="text" name="keyword" value="<?php echo Request::getGET('keyword'); ?>" />
    <select name="search-type" class="select w100">
        <option value="1" <?php echo Request::getGET('search-type') == 1 ? 'selected' : ''; ?> >按标题</option>
        <option value="2" <?php echo Request::getGET('search-type') == 2 ? 'selected' : ''; ?> >按分类</option>
    </select>
    <input class="w80 btn" type="submit" value="OK" />
    <input name="remote" type="hidden" value="<?php echo Request::getGET('remote'); ?>" />
</form>

<?php echo $this->html['pager']; ?>

<table class="mt10 mb10 widget-table widget-table-hover">
    <thead>
        <tr>
        <?php if (empty($this->loginUserInfo)) { ?>
            <th class="tc" width="12%">题号</th>
            <th width="37%">标题</th>
            <th width="37%">分类</th>
            <th class="tc" width="7%">解决人数</th>
            <th class="tc" width="7%">总提交</th>
        <?php } else { ?>
            <th class="tr" width="5%"></th>
            <th width="7%">题号</th>
            <th width="37%">标题</th>
            <th width="37%">分类</th>
            <th class="tc" width="7%">解决人数</th>
            <th class="tc" width="7%">总提交</th>
        <?php }?>
        </tr>
    </thead>
    <tbody>
        <?php   foreach ($this->problemList as $problemInfo) {
                    $solutionInfo = Arr::get($problemInfo['id'], $this->userSolution, array());
        ?>
        <tr>
            <?php if (!empty($this->loginUserInfo)) { ?>
                <td class="tr">
                    <?php if (Arr::get('result', $solutionInfo, 0) == StatusVars::ACCEPTED) { ?>
                        <img src="//sta.hqoj.net/image/www/oj/problem-ac.png" height="16px" />
                    <?php } else if (!empty($solutionInfo)) { ?>
                        <img src="//sta.hqoj.net/image/www/oj/problem-no.png" height="16px" />
                    <?php } ?>
                </td>
            <?php } ?>
            <td class="<?php echo $this->loginUserInfo ? '' : 'tc'; ?>"><?php echo $problemInfo['remote'] ? StatusVars::$REMOTE_SCHOOL[$problemInfo['remote']] : ''; echo $problemInfo['problem_code']; ?></td>
            <td>
                <a href="/problem_detail/?global-id=<?php echo $problemInfo['id']; ?>"><?php echo $problemInfo['title']; ?></a>
            </td>
            <td><?php echo OjCommonHelper::getSourceUrls($problemInfo); ?></td>
            <td class="tc"><a href="<?php echo OjCommonHelper::getStatusUrl('', $problemInfo['remote'], $problemInfo['problem_code'], StatusVars::ACCEPTED); ?>"><?php echo $problemInfo['solved']; ?></a></td>
            <td class="tc"><a href="<?php echo OjCommonHelper::getStatusUrl('', $problemInfo['remote'], $problemInfo['problem_code'], -1); ?>"><?php echo $problemInfo['submit']; ?></a></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php echo $this->html['pager']; ?>

<script>
    seajs.use(['jquery'], function($) {
        
        $('select[name=remote]').change(function() {
            var url = '/problem_list/?remote=' + $(this).val();
            location.href = url;
            
        });
    });
</script>