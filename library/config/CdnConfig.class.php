<?php

class CdnConfig {

    // OSS访问地址，SDK中的default.options.php调用了这个变量，开发时使用外网，上线使用内网
    const ENDPOINT          = 'http://oss-internal.aliyuncs.com';

    // 阿里云 API ACCESS
    const ACCESS_KEY_ID     = '#';
    const ACCESS_KEY_SECRET = '#';

    // 阿里云OSS上用来做CDN源的bucket
    const BUCKET_NAME       = 'hqoj-cdn';

    // cdn域名
    const CDN_URL           = 'http://cdn.hqoj.net';

}