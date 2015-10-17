<div class="mt10 tc">
    <h3 class="f20"><?php echo "{$this->problemInfo['problem_id']} {$this->problemInfo['title']}"; ?></h3>
    <br />
    <p><span>时间限制: <?php echo $this->problemInfo['time_limit']; ?>MS</span>
        &nbsp;|&nbsp;&nbsp;<span>内存限制：<?php echo $this->problemInfo['memory_limit']; ?>KB</span>
    </p>
</div>

<h5 class="mt10 f14">题目描述</h5>
<article><div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['description']; ?></div></article>

<?php if (!empty($this->problemInfo['input'])) { ?>
<h5 class="mt10 f14">输入描述</h5>
<article><div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['input']; ?></div></article>
<?php } ?>

<?php if (!empty($this->problemInfo['output'])) { ?>
<h5 class="mt10 f14">输出描述</h5>
<article><div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['output']; ?></div></article>
<?php } ?>

<?php if (!empty($this->problemInfo['sample_input'])) { ?>
<h5 class="mt10 f14">输入样例</h5>
<textarea class="textarea" readonly="true" style="font-family:Courier New,Courier,monospace; font-size: 12px; padding: 10px; width: 950px; height: 250px; resize:none;"><?php echo $this->problemInfo['sample_input']; ?></textarea>
<?php } ?>

<?php if (!empty($this->problemInfo['sample_output'])) { ?>
<h5 class="mt10 f14">输出样例</h5>
<textarea class="textarea" readonly="true" style="font-family:Courier New,Courier,monospace; font-size: 12px; padding: 10px; width: 950px; height: 250px;resize:none;"><?php echo $this->problemInfo['sample_output']; ?></textarea>
<?php } ?>

<?php if (!empty($this->problemInfo['source'])) { ?>
<h5 class="mt10 f14">分类</h5>
<div class="mt5 p10 bg-gray"><?php echo OjCommonHelper::getSourceUrls($this->problemInfo); ?></div>
<?php } ?>

<?php if (!empty($this->problemInfo['hint'])) { ?>
<h5 class="mt10 f14">提示</h5>
<article><div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['hint']; ?></div></article>
<?php } ?>

<div class="mt10 tc f14">
    <a class="f18 fw" href="/problem_submit/?global-id=<?php echo $this->problemInfo['id']; ?>">提交代码</a>&nbsp;&nbsp;|&nbsp;
    <a href="/status_list/?remote=<?php echo $this->problemInfo['remote']; ?>&problem-code=<?php echo $this->problemInfo['problem_code']; ?>">状态</a>
    <?php if ($this->isOjAdmin) { ?>
        &nbsp;|&nbsp;&nbsp;<a target="_blank" href="http://bc.hqoj.net/problem_edit/?id=<?php echo $this->problemInfo['id']; ?>">编辑</a>
    <?php } ?>
</div>