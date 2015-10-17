
<form class="widget-form">
    <div class="item">
        <label class="label w110">序号：</label>
        <label class="label"><?php echo $this->solutionInfo['contest_submit_order']; ?></label>
        <label class="label w110">题目：</label>
        <label class="label">
            <a href="/problem_detail/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $this->contestInfo['problem_hash'][$this->solutionInfo['problem_global_id']]; ?>" >
                <?php echo $this->contestInfo['problem_hash'][$this->solutionInfo['problem_global_id']]; ?>
            </a>
        </label>
        <label class="label w110">结果：</label>
        <label class="label">
            <font class="<?php echo StatusVars::$RESULT_CLASS[$this->solutionInfo['result']]; ?>"><?php echo StatusVars::$RESULT_FORMAT[$this->solutionInfo['result']]; ?></font>
            <?php if ($this->solutionInfo['result'] >= 4 && $this->solutionInfo['has_log']) { ?>
                <a name="show-judge-log" contest-id="<?php echo $this->contestInfo['id']; ?>" solution-id="<?php echo $this->solutionInfo['id']; ?>" href="#"><img src="//sta.hqoj.net/image/www/oj/show_log.png" /></a>
            <?php } ?>
        </label>
        <label class="label w110">时间：</label>
        <label class="label"><?php echo $this->solutionInfo['time_cost']; ?>MS</label>
        <label class="label w110">内存：</label>
        <label class="label"><?php echo $this->solutionInfo['memory_cost']; ?>KB</label>
    </div>
</form>

<div class="f12 mt10">
    <link type="text/css" rel="stylesheet" href="//sta.hqoj.net/js/plugin/syntaxhighlighter/css/SyntaxHighlighter.css"/>
    <script type="text/javascript" src="//sta.hqoj.net/js/plugin/syntaxhighlighter/js/shCore.js"></script>
    <script type="text/javascript" src="//sta.hqoj.net/js/plugin/syntaxhighlighter/js/shBrushCpp.js"></script>
    <script type="text/javascript" src="//sta.hqoj.net/js/plugin/syntaxhighlighter/js/shBrushJava.js"></script>
    <?php if ($this->solutionInfo['language'] == StatusVars::JAVA) { ?>
        <pre name="code" class="java"><?php echo $this->solutionInfo['source_format']; ?></pre>
    <?php } else { ?>
        <pre name="code" class="cpp"><?php echo $this->solutionInfo['source_format']; ?></pre>
    <?php } ?>
    
    <script language="javascript">
    dp.SyntaxHighlighter.HighlightAll('code');
    </script>
</div>

<script>
    seajs.use(['jquery',  'layer'], function($, notice, layer) {
        
        var flag = false;
        $('a[name=show-judge-log]').click(function(e) {
            e.preventDefault();
            if (flag) {
                return false;
            }
            flag = true;
            var url = '/status_judgeLog/iframeShow/?solution-id='+$(this).attr('solution-id')+'&contest-id='+$(this).attr('contest-id');
            $.layer({
                type: 2,
                title: 'JUDGE LOG',
                border: [1, 1, '#ddd'],
                shade: [0],
                iframe: {src : url },
                area: ['800px' , '450px'],
                shift: 'top',
                close: function(index){
                    flag = false;
                }
            });
        });
        
    });
</script>