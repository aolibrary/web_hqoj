<style>
    .status-link:hover {
        text-decoration: underline;
    }
</style>

<form class="widget-form">
    <div class="item">
        <label class="label w100">RUN-ID：</label>
        <label class="label"><?php echo $this->judgeInfo['id']; ?></label>
        <label class="label w100">题目：</label>
        <label class="label">
            <a href="/problem_detail/?problem-id=<?php echo $this->judgeInfo['problem_id']; ?>" >
                <?php echo 'HQU' . $this->judgeInfo['problem_id']; ?>
            </a>
        </label>
        <label class="label w100">结果：</label>
        <label class="label">
            <font class="<?php echo StatusVars::$RESULT_CLASS[$this->judgeInfo['result']]; ?>"><?php echo StatusVars::$RESULT_FORMAT[$this->judgeInfo['result']]; ?></font>
            <a name="show-judge-log" judge-id="<?php echo $this->judgeInfo['id']; ?>" href="#"><img src="//sta.hqoj.net/image/www/oj/show_log.png" /></a>
        </label>
        <label class="label w100">时间：</label>
        <label class="label"><?php echo $this->judgeInfo['time_cost']; ?>MS</label>
        <label class="label w100">内存：</label>
        <label class="label"><?php echo $this->judgeInfo['memory_cost']; ?>KB</label>
    </div>
</form>

<div class="f12 mt10">
    <link type="text/css" rel="stylesheet" href="//sta.hqoj.net/js/plugin/syntaxhighlighter/css/SyntaxHighlighter.css"/>
    <script type="text/javascript" src="//sta.hqoj.net/js/plugin/syntaxhighlighter/js/shCore.js"></script>
    <script type="text/javascript" src="//sta.hqoj.net/js/plugin/syntaxhighlighter/js/shBrushCpp.js"></script>
    <script type="text/javascript" src="//sta.hqoj.net/js/plugin/syntaxhighlighter/js/shBrushJava.js"></script>
    <?php if ($this->judgeInfo['language'] == StatusVars::JAVA) { ?>
        <pre name="code" class="java"><?php echo $this->judgeInfo['source_format']; ?></pre>
    <?php } else { ?>
        <pre name="code" class="cpp"><?php echo $this->judgeInfo['source_format']; ?></pre>
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
            var url = '/problem_judgeLog/iframeShow/?judge-id='+$(this).attr('judge-id');
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