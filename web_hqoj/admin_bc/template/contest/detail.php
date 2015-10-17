<link rel="stylesheet" href="//sta.hqoj.net/js/plugin/kindeditor/themes/default/default.css" />

<div class="p10 f14">
    <a href="/contest_edit/?contest-id=<?php echo $this->contestInfo['id']; ?>">【编辑】</a>
    <a href="/contest_setProblem/?contest-id=<?php echo $this->contestInfo['id']; ?>">【管理题目】</a>
</div>
<form class="widget-form bg-white">
    <div class="item">
        <label class="label w80">标题：</label>
        <label class="label"><?php echo $this->contestInfo['title']; ?></label>
    </div>
    <div class="item">
        <label class="label w80">是否激活：</label>
        <label class="label"><?php echo $this->contestInfo['is_active'] ? '<span class="red">已激活</span>' : '未激活'; ?></label>
    </div>
    <div class="item">
        <label class="label w80">权限：</label>
        <label class="label"><?php echo ContestVars::$TYPE_FORMAT[$this->contestInfo['type']]; ?></label>
    </div>
    <div class="item">
        <label class="label w80">密码：</label>
        <label class="label"><?php echo $this->contestInfo['password']; ?></label>
    </div>
    <div class="item">
        <label class="label w80">提示：</label>
        <label class="label"><?php echo $this->contestInfo['notice']; ?></label>
    </div>
    <div class="item">
        <label class="label w80">比赛时间：</label>
        <label class="label"><?php echo date('Y-m-d H:i:s', $this->contestInfo['begin_time']); ?></label> ～
        <label class="label"><?php echo date('Y-m-d H:i:s', $this->contestInfo['end_time']); ?></label>
    </div>
    <div class="item">
        <label class="label w80">隐藏题目：</label>
        <label class="label"><?php echo $this->contestInfo['problem_hidden'] ? 'Yes' : 'No'; ?></label>
    </div>
    <div class="item">
        <label class="label w80">描述：</label>
    </div>
    <div class="item" style="padding: 10px 20px;">
        <?php echo $this->contestInfo['description']; ?>
    </div>
</form>
