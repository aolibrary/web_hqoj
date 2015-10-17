<form class="widget-form widget-form-toolbar bg-gray mb10">
    <label class="label">级别：</label>
    <select name="level" class="select">
        <option value="">全部</option>
        <?php   foreach (LogVars::$levelText as $key => $value) {
            $selected = Request::getGET('level') == $key ? 'selected' : '';
            ?>
            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
        <?php   } ?>
    </select>
    <input type="hidden" name="url" value="<?php echo Url::getCurrentUrl(); ?>" />
</form>

<style>
    .log-table td {
        word-break: break-all;
    }
    .log-table .trace {
        background-color: #f5f5f5;
    }
    .log-table .trace pre {
        word-break: break-all;
    }
    .log-table .row td {
        border-bottom: 0;
    }
    .log-table .detail td {
        border-top: 0;
    }
</style>

<?php echo $this->html['pager']; ?>

<table class="widget-table log-table mt10 f12">
    <thead>
    <tr>
        <th width="10%">ID</th>
        <th width="20%">时间</th>
        <th width="25%">TAG</th>
        <th width="15%">级别</th>
        <th width="30%">IP</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->logList as $logInfo) { ?>
        <tr class="row">
            <td><?php echo $logInfo['id']; ?></td>
            <td><?php echo date('Y-m-d H:i:s', $logInfo['create_time']); ?></td>
            <td>
                <?php echo $logInfo['tag']; ?>
            </td>
            <td>
                <span class="fw <?php echo LogVars::$levelColor[$logInfo['level']]; ?>">
                    <?php echo LogVars::$levelText[$logInfo['level']]; ?>
                </span>
            </td>
            <td>
                <p><span class="red">客户端IP：</span><?php echo Http::long2ip($logInfo['client_ip']).':'.$logInfo['client_port']; ?></p>
                <p><span class="red">服务端IP：</span><?php echo Http::long2ip($logInfo['server_ip']).':'.$logInfo['server_port']; ?></p>
            </td>
        </tr>
        <tr class="detail">
            <td colspan="6">
                <p><span class="red">Msg：</span><?php echo htmlspecialchars($logInfo['message']); ?></p>
                <p><span class="red">Loc：</span><?php echo $logInfo['loc']; ?></p>
                <p><span class="red">Url：</span><?php echo htmlspecialchars($logInfo['url']); ?></p>
                <br/><p><a href="#" name="trace-info" log-id="<?php echo $logInfo['id']; ?>" >查看更多 >></a></p>
            </td>
        </tr>
        <tr class="trace" style="display: none;">
            <td colspan="5"><pre><?php print_r (json_decode($logInfo['trace'], true)); ?></pre></td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<script>
    seajs.use(['jquery'], function($) {

        $('select[name=level]').change(function() {
            var url = $('input[name=url]').val() + '?level=' + $('select[name=level]').val();
            location.href = url;
        });

        $('a[name=trace-info]').click(function(e) {
            e.preventDefault();
            $(this).parent().parent().parent().next().toggle();
        });

    })
</script>
