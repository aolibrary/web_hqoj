<?php if ($this->judgeInfo['ce']) { ?>
<p style="padding: 10px 10px 0 10px; font-weight: 700;">Compilation Error:</p>
<pre class="p10"><?php echo htmlspecialchars($this->judgeInfo['ce']); ?></pre>
<?php } ?>

<?php if ($this->judgeInfo['re']) { ?>
<p style="padding: 10px 10px 0 10px; font-weight: 700;">Runtime Error:</p>
<pre class="p10"><?php echo htmlspecialchars($this->judgeInfo['re']); ?></pre>
<?php } ?>

<?php if ($this->judgeInfo['detail']) { ?>
<p style="padding: 10px 10px 0 10px; font-weight: 700;">Detail:</p>
<pre class="p10"><?php echo htmlspecialchars($this->judgeInfo['detail']); ?></pre>
<?php } ?>
