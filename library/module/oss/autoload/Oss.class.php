<?php

require_once __DIR__ . '/../sdk/aliyun-php-sdkv2-20130815/aliyun.php';

use \Aliyun\OSS\OSSClient;

class Oss {

    private static function getOssClient() {
        $ossClient = OSSClient::factory(array(
            'AccessKeyId'       => OssConfig::ACCESS_KEY_ID,
            'AccessKeySecret'   => OssConfig::ACCESS_KEY_SECRET,
        ));
        return $ossClient;
    }

    private static function makeKey($id, $ext) {
        $storageKey = date('Ymd', time()) . '/' . Time::ms()
            . '-' . sprintf('%06d', rand()%100000)
            . '-' . sprintf('%011d', $id*3+1)
            . (empty($ext) ? '' : '.' . $ext);
        return $storageKey;
    }

    /**
     * 将本地文件上传到OSS
     *
     * @param       string  $file       本地文件绝对路径
     * @param       int     $id         标识用户id，默认0
     * @param       string  $ext        文件的扩展名，$ext = 'jpg'，如果不指定的话，会从file的扩展名中获取
     * @return      string  $key
     * @exception   Exception
     */
    public static function uploadLocalFile($file, $id = 0, $ext = '') {

        // 获取扩展名
        if (empty($ext) && strpos($file, '.')) {
            $arr = explode('.', $file);
            $ext = end($arr);
        }

        $ossClient = self::getOssClient();
        $key       = self::makeKey($id, $ext);

        // 获取content type
        $contentType = '';
        if (!empty($ext)) {
            $mimeTypes = Response::getMimeTypes();
            $contentType = $mimeTypes[$ext];
        }

        $ossClient->putObject(array(
            'Bucket'             => OssConfig::BUCKET_NAME,
            'Key'                => $key,
            'Content'            => fopen($file, 'r'),
            'ContentLength'      => filesize($file),
            'ContentEncoding'    => GlobalConfig::CONTENT_CHARSET,
            'ContentType'        => $contentType,
        ));
        return $key;
    }

    /**
     * @biref       将网络文件上传到CDN源，非UTF-8的文本页面可能会出现乱码
     * @param       string  $url        网络文件的url
     * @param       int     $id         标识用户id，默认0
     * @param       string  $ext        文件的扩展名，$ext = 'jpg'，如果不指定的话，会从url的扩展名中获取
     * @return      string  $key
     * @exception   Exception
     */
    public static function uploadRemoteFile($url, $id = 0, $ext = '') {

        // 获取扩展名
        $path = Url::getPath($url);
        if (empty($ext) && strpos($path, '.')) {
            $arr = explode('.', $path);
            $ext = end($arr);
        }

        $ossClient = self::getOssClient();
        $key       = self::makeKey($id, $ext);

        // 获取content type
        $contentType = '';
        if (!empty($ext)) {
            $mimeTypes = Response::getMimeTypes();
            $contentType = $mimeTypes[$ext];
        }

        $curl = new Curl();
        $ossClient->putObject(array(
            'Bucket'             => OssConfig::BUCKET_NAME,
            'Key'                => $key,
            'Content'            => $curl->get($url),
            'ContentEncoding'    => GlobalConfig::CONTENT_CHARSET,
            'ContentType'        => $contentType,
        ));
        return $key;
    }

    /**
     * 从OSS上下载一个obj，保存到本地
     *
     * @param       string  $key 键
     * @param       string  $saveAs     需要保存的新文件名
     * @Exception   Exception
     */
    public static function download($key, $saveAs) {

        // 如果$saveAs是目录，那么将obj下载到这个目录下
        if (is_dir($saveAs)) {
            $saveAs = rtrim($saveAs, '/') . '/' . $key;

        }

        // 如果目录不存在，那么创建
        $dir = dirname($saveAs);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $ossClient = self::getOssClient();
        $ossClient->getObject(array(
            'Bucket'    => OssConfig::BUCKET_NAME,
            'Key'       => $key,
            'SaveAs'    => $saveAs,
        ));
    }

}