
<style>
    body {
        background-color: #E7E7E7;
        font-size: 12px;
    }
    .widget-table td, .widget-table th {
        border: 0;
    }
    .widget-table th {
        padding-bottom: 0;
    }
    .folder .selected {
        background-color: #ddd;
    }
</style>
<div class="tc bg-white" style="padding: 10px 10px 0 10px;">
    <div style="border: 1px solid #ddd; padding: 10px;">上传到所有数据 -> 将数据复制到左边 -> 数据生效，注意：上传相同文件名的数据将会覆盖，可以根据文件修改时间来判断是否成功上传</div>
</div>
<table class="widget-table no-border">
    <thead>
    <tr>
        <th class="tc" width="42%">数据目录（<span id="file-size" filesize="<?php echo $this->fileSize; ?>"><?php echo sprintf('%.2lf', $this->fileSize/1024); ?></span>KB/5000KB）</th>
        <th class="tc" width="16%"></th>
        <th class="tc" width="42%">上传目录（<span id="upload-file-size" filesize="<?php echo $this->uploadFileSize; ?>"><?php echo sprintf('%.2lf', $this->uploadFileSize/1024); ?></span>KB/5000KB）</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tc">
            <div id="folder1" class="folder" style="height:300px; border: 1px solid #ddd;">
                <?php   foreach ($this->fileList as $key => $fileInfo) { ?>
                    <p name="data" filesize="<?php echo $fileInfo['filesize']; ?>" file="<?php echo $fileInfo['filename']; ?>" style="cursor:pointer; font-family:Courier New,Courier,monospace;"><?php echo $fileInfo['format']; ?></p>
                <?php   } ?>
            </div>
        </td>
        <td class="tc">
            <?php if ($this->problemInfo['hidden']) { ?>
                <input name="copy" type="button" class="btn w100" value="<<复制" />
            <?php } ?>
        </td>
        <td class="tc">
            <div id="folder2" class="folder" style="height:300px; border: 1px solid #ddd;">
                <?php   foreach ($this->uploadFileList as $key => $fileInfo) { ?>
                    <p name="data" dir="upload" filesize="<?php echo $fileInfo['filesize']; ?>" file="upload/<?php echo $fileInfo['filename']; ?>" style="cursor:pointer; font-family:Courier New,Courier,monospace;"><?php echo $fileInfo['format']; ?></p>
                <?php   } ?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="3" class="tr" style="background-color: #E7E7E7; border: 0;">
            <label id="progress" class="f14"></label>
            <input id="uploader-btn" type="button" class="btn btn-blue w120" value="批量上传文件" />
            <input name="delete" type="button" class="btn btn-red w120" value="删除文件" />
            <input type="button" name="watch" class="btn w120" value="查看数据" />
            <input type="button" name="cancel" class="btn w120" value="关闭窗口" />
            <input type="hidden" name="problem-id" value="<?php echo $this->problemInfo['problem_id']; ?>" />
        </td>
    </tr>
    </tbody>
</table>

<script>
    seajs.use(['jquery', 'notice', 'layer', 'js/util/upload/Upload.js'], function($, notice, layer, Uploader) {

        var index = parent.layer.getFrameIndex(window.name);

        $('input[name=cancel]').click(function() {
            parent.dataIframeOpen = false;
            parent.layer.close(index);
        });

        // 修改当前文件大小显示
        var updateSize = function(size, isUpload) {
            var used = (parseFloat(size)/1024).toFixed(2);
            isUpload === true ? $('#upload-file-size').text(used) : $('#file-size').text(used);
            isUpload === true ? $('#upload-file-size').attr('filesize', size) : $('#file-size').attr('filesize', size);
        }
        var reduceSize = function($p) {
            if ('upload' == $p.attr('dir')) {
                var size = parseInt($('#upload-file-size').attr('filesize'))-parseInt($p.attr('filesize'));
                updateSize(size, true);
            } else {
                var size = parseInt($('#file-size').attr('filesize'))-parseInt($p.attr('filesize'));
                updateSize(size, false);
            }
        }

        var pClickFn = function() {
            var $this = $(this);
            if ($this.hasClass('selected')) {
                $this.removeClass('selected');
            } else {
                $('p[name=data]').removeClass('selected');
                $this.addClass('selected');
            }
        };
        $('p[name=data]').bind('click', pClickFn);

        $('input[name=copy]').click(function(e) {
            e.preventDefault();
            var $item = $('#folder2 p[class=selected]');
            if ($item.length != 1) {
                notice('error', '请从上传目录选择需要复制的文件！');
                return false;
            }
            var file = $item.attr('file');
            $.ajax({
                url:    '/problem_dataManager/ajaxCopy/',
                type: 'post',
                dataType: 'json',
                data: {
                    'problem-id': $('input[name=problem-id]').val(),
                    'file': file
                },
                success: function(result) {
                    if (0 === result.errorCode) {
                        var i, str = '';
                        for (i = 0; i < result.fileList.length; i++) {
                            var fileInfo = result.fileList[i];
                            str += '<p name="data" filesize="'
                                +fileInfo['filesize']+'" file="'
                                +fileInfo['filename']+'" style="cursor:pointer; font-family:Courier New,Courier,monospace;">'
                                +fileInfo['format']+'</p>';
                        }
                        $('#folder1').html(str);
                        $('#folder1 p[name=data]').bind('click', pClickFn);
                        updateSize(result.fileSize, false);
                        notice('success', '复制成功成功！');
                    } else {
                        notice('error', result.errorMessage);
                    }
                },
                error: function() {
                    notice('error', '服务器请求失败！');
                }
            });
        });

        $('input[name=delete]').click(function(e) {
            e.preventDefault();
            var $item = $('.folder p[class=selected]');
            if ($item.length != 1) {
                notice('error', '请选择需要删除的文件！');
                return false;
            }
            var file = $item.attr('file');
            var index2 = $.layer({
                shade: [0],
                area: ['auto','auto'],
                dialog: {
                    msg: '你确定要删除文件：'+file+' ?',
                    btns: 2,
                    type: 4,
                    btn: ['删除', '取消'],
                    yes: function() {
                        $.ajax({
                            url:    '/problem_dataManager/ajaxRemove/',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'problem-id': $('input[name=problem-id]').val(),
                                'file': file
                            },
                            success: function(result) {
                                if (0 === result.errorCode) {
                                    layer.close(index2);
                                    reduceSize($item);
                                    $item.remove();
                                    notice('success', '删除成功！');
                                } else {
                                    notice('error', result.errorMessage);
                                }
                            },
                            error: function() {
                                notice('error', '服务器请求失败！');
                            }
                        });
                    }, no: function() {

                    }
                }
            });
        });

        $('input[name=watch]').click(function(e) {
            e.preventDefault();
            var $item = $('.folder p[class=selected]');
            if ($item.length != 1) {
                notice('error', '请选择需要查看的文件！');
                return false;
            }
            var file = $item.attr('file');
            var url = '/problem_dataManager/watch/?problem-id='+$('input[name=problem-id]').val()+'&file='+file;
            window.open(url);
        });

        var uploader = new Uploader({

            trigger: '#uploader-btn',
            action: '/problem_dataManager/ajaxUpload/',
            name: 'file[]',
            multiple: true,
            progress: function(e, position, total, percent, files) {
                $('#progress').html(' <img src="//sta.hqoj.net/image/common/loading/loading03.gif" height="22px" />上传中，请勿操作...' + percent + '%');
            },
            data: {
                'problem-id': $('input[name=problem-id]').val()
            },

            change: function(fileList) {

                // 前端校验是否超出大小
                var filesize = parseInt($('#upload-file-size').attr('filesize'));
                var i = 0;
                for (i = 0; i < fileList.length; i++) {
                    filesize += fileList[i].size;
                }
                if (filesize > 5*1024*1024) {
                    notice('error', '上传目录空间限制5M，你已经超出范围啦！');
                    return false;
                }

                this.submit();
                this.refreshInput();
            },

            success: function(response) {

                $('#progress').html('');

                var result = '';
                try {
                    result = $.parseJSON(response);
                } catch (e) {
                    notice('error', '服务器请求失败！');
                    return false;
                }

                if (0 === result.errorCode) {
                    var i, str = '';
                    for (i = 0; i < result.uploadFileList.length; i++) {
                        var fileInfo = result.uploadFileList[i];
                        str += '<p name="data" dir="upload" filesize="'
                            +fileInfo['filesize']+'" file="upload/'
                            +fileInfo['filename']+'" style="cursor:pointer; font-family:Courier New,Courier,monospace;">'
                            +fileInfo['format']+'</p>';
                    }
                    $('#folder2').html(str);
                    $('#folder2 p[name=data]').bind('click', pClickFn);
                    updateSize(result.uploadFileSize, true);
                    notice('success', '上传成功！');
                } else {
                    notice('error', result.errorMessage);
                }
            },

            error: function(data) {
                $('#progress').html('');
                notice('error', '服务器请求失败！' + data.statusText);
            }
        });
    })
</script>