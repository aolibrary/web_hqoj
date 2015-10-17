<?php

class DocHelper {

    public static function getDocUrl($docId) {

        return sprintf('http://doc.hqoj.net/detail/%d.html', $docId);
    }
}