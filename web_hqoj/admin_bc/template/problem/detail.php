<div class="mt10 f14">
    <a href="/problem_edit/?global-id=<?php echo $this->problemInfo['id']; ?>">【编辑】</a>&nbsp;
    <a href="/problem_submit/?global-id=<?php echo $this->problemInfo['id']; ?>">【提交】</a>&nbsp;
    <a href="/problem_judgeList/?problem-id=<?php echo $this->problemInfo['problem_id']; ?>">【状态】</a>
</div>
<div class="mt10 tc">
    <h3 class="f20"><?php echo "{$this->problemInfo['problem_id']} {$this->problemInfo['title']}"; ?></h3>
    <br />
    <p><span>时间限制: <?php echo $this->problemInfo['time_limit']; ?>MS</span>
        &nbsp;|&nbsp;&nbsp;<span>内存限制：<?php echo $this->problemInfo['memory_limit']; ?>KB</span>
    </p>
    <p>状态：<?php echo $this->problemInfo['hidden'] ? '<span class="green">私有</span>' : '<span class="red">公开</span>'; ?></p>
</div>
<?php if (!empty($this->problemInfo['src_url'])) { ?>
<h5 class="mt10 f14">原题链接</h5>
<div class="mt5 p10"><a href="<?php echo $this->problemInfo['src_url']; ?>" target="_blank"><?php echo $this->problemInfo['src_url']; ?></a></div>
<?php } ?>
<h5 class="mt10 f14">题目描述</h5>
<div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['description']; ?></div>
<h5 class="mt10 f14">输入描述</h5>
<div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['input']; ?></div>
<h5 class="mt10 f14">输出描述</h5>
<div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['output']; ?></div>
<h5 class="mt10 f14">输入样例</h5>
<textarea class="textarea" readonly="true" style="font-family:Courier New,Courier,monospace; font-size: 12px; padding: 10px; width: 950px; height: 250px; resize:none;"><?php echo $this->problemInfo['sample_input']; ?></textarea>
<h5 class="mt10 f14">输出样例</h5>
<textarea class="textarea" readonly="true" style="font-family:Courier New,Courier,monospace; font-size: 12px; padding: 10px; width: 950px; height: 250px;resize:none;"><?php echo $this->problemInfo['sample_output']; ?></textarea>
<h5 class="mt10 f14">来源</h5>
<div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['source']; ?></div>
<h5 class="mt10 f14">提示</h5>
<div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['hint']; ?></div>