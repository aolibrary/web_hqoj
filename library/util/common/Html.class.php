<?php

class Html {

    /**
     * 过滤指定标签，仅仅是标签，不过滤其中的内容
     *
     * @param   string  $str
     * @param   array   $tagsArr    e.x. array('script', '?/php', 'html')
     * @return  mixed
     */
    public static function stripTags($str, $tagsArr) {
        $str = ''.$str;
        $p = array();
        foreach ($tagsArr as $tag) {
            $p[]="/(<(?:\/{$tag}|{$tag})[^>]*>)/si";
        }
        $ret = preg_replace($p, '', $str);
        return $ret;
    }

}