<?php

class ProblemVars {

    const AUDIT_NONE        = 0; // 未申请公开
    const AUDIT_QUEUE       = 1; // 审核中
    const AUDIT_ACCEPTED    = 2; // 审核通过
    const AUDIT_REJECTED    = 3; // 拒绝

    public static $AUDIT_HTML = array(
        self::AUDIT_NONE        => '<span class="gray">未申请</span>',
        self::AUDIT_QUEUE       => '<span class="green">审核中</span>',
        self::AUDIT_ACCEPTED    => '<span class="red">通过</span>',
        self::AUDIT_REJECTED    => '<span class="orange">拒绝</span>',
    );
}
