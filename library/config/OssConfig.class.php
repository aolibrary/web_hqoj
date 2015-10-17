<?php

class OssConfig {

    // OSS访问地址，SDK中的default.options.php调用了这个变量，开发时使用外网，上线使用内网
    const ENDPOINT          = 'http://oss.aliyuncs.com';

    // 阿里云 API ACCESS
    const ACCESS_KEY_ID     = '#';
    const ACCESS_KEY_SECRET = '#';

    // 阿里云OSS上用来存储文件的bucket
    const BUCKET_NAME       = 'hqoj-private';

}