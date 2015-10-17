<div class="mt10 tc">
    <h3 class="f20">
        <?php echo $this->contestInfo['problem_hash'][$this->problemInfo['id']] . ' 题: '; ?>
        <?php echo $this->contestInfo['problem_hidden'] ? 'Problem ' . $this->contestInfo['problem_hash'][$this->problemInfo['id']] : $this->problemInfo['title']; ?>
    </h3>
</div>

<h5 class="mt10 f14">原题链接</h5>
<div class="mt5"><a style="display: inline-block; border: 1px dashed #ddd; padding: 10px;" name="src-link" href="#"><?php echo $this->srcUrl['problem_url']; ?></a></div>

<h5 class="mt10 f14">题目描述</h5>
<div class="mt5 p10 bg-gray">
    <p>该题目为网络上的题目，你可以通过点击上面的链接，来查看题目描述，并且提交代码。</p>
</div>

<div class="tc f14" style="margin-top: 20px;">
    <a class="f18 fw" href="/problem_submit/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $this->contestInfo['problem_hash'][$this->problemInfo['id']]; ?>">提交代码</a>&nbsp;&nbsp;|&nbsp;
    <a href="/status_list/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $this->contestInfo['problem_hash'][$this->problemInfo['id']]; ?>">状态</a>
</div>

<input name="problem-show" type="hidden" value="<?php echo StatusVars::$REMOTE_SCHOOL[$this->problemInfo['remote']] . $this->problemInfo['problem_code']; ?>" />
<input name="problem-url" type="hidden" value="<?php echo $this->srcUrl['problem_url']; ?>" />
<input name="statis-url" type="hidden" value="<?php echo $this->srcUrl['statis_url']; ?>" />
<input name="status-url" type="hidden" value="<?php echo $this->srcUrl['status_url']; ?>" />
<input name="submit-url" type="hidden" value="/problem_submit/?contest-id=<?php echo $this->contestInfo['id']; ?>&problem-hash=<?php echo $this->contestInfo['problem_hash'][$this->problemInfo['id']]; ?>" />

<style>
    .submit-btn {
        font-size: 14px;
        color: #fff;
        background-color: #eb4141;
        border: 0;
        padding: 5px 25px;
        border-radius: 4px;
    }
    .submit-btn:hover { background-color: #eb5050; }
</style>

<script>
    seajs.use(['jquery', 'layer', 'layer.ext'], function($, layer, fn1) {
        
        fn1($, layer);
        
        $('a[name=src-link]').click(function(e) {
            e.preventDefault();
            var tabIframe = function(src){
                return '<iframe frameborder="0" src="'+ src +'" style="width:1180px; height:465px;"></iframe>';
            };
            var getBottomHtml = function() {
                return '<div class="tc" style="padding:10px; background-color:#eee;"><a name="submit" class="submit-btn f14" href="#"><font color="white">提交代码</font></a></div>';
            };
            layer.tab({
                area: ['1180px', '550px'],
                data: [
                    {title: $('input[name=problem-show]').val(), content: tabIframe($('input[name=problem-url]').val()) + getBottomHtml() }
                ],
                shade: [0]
            });
            $('a[name=submit]').click(function(e) {
                e.preventDefault();
                location.href = $('input[name=submit-url]').val();
            })
        });
        
    });
</script>