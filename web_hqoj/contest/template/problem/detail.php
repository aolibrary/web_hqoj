<div class="mt10 tc">
    <h3 class="f20">
        <?php echo $this->contestInfo['problem_hash'][$this->problemInfo['id']] . ' 题: '; ?>
        <?php echo $this->contestInfo['problem_hidden'] ? 'Problem ' . $this->contestInfo['problem_hash'][$this->problemInfo['id']] : $this->problemInfo['title']; ?>
    </h3>
    <br />
    <p><span>时间限制: <?php echo $this->problemInfo['time_limit']; ?>MS</span>
        &nbsp;|&nbsp;&nbsp;<span>内存限制：<?php echo $this->problemInfo['memory_limit']; ?>KB</span>
    </p>
</div>

<h5 class="mt10 f14">题目描述</h5>
<div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['description']; ?></div>

<?php if (!empty($this->problemInfo['input'])) { ?>
<h5 class="mt10 f14">输入描述</h5>
<div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['input']; ?></div>
<?php } ?>

<?php if (!empty($this->problemInfo['output'])) { ?>
<h5 class="mt10 f14">输出描述</h5>
<div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['output']; ?></div>
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
<div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['source']; ?></div>
<?php } ?>

<?php if (!empty($this->problemInfo['hint'])) { ?>
<h5 class="mt10 f14">提示</h5>
<div class="mt5 p10 bg-gray"><?php echo $this->problemInfo['hint']; ?></div>
<?php } ?>

<div class="mt10 tc f14">
    <a class="f18 fw" href="/problem_submit/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $this->contestInfo['problem_hash'][$this->problemInfo['id']]; ?>">提交代码</a>&nbsp;&nbsp;|&nbsp;
    <a href="/status_list/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $this->contestInfo['problem_hash'][$this->problemInfo['id']]; ?>">状态</a>
</div>