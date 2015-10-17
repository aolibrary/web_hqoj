<?php

class DeleteController extends ProjectController {

    public function ajaxDeleteAction() {

        $ids = Request::getPOST('ids');

        // 过滤
        $ids = json_decode($ids, true);
        foreach ($ids as $i => $id) {
            if (!is_numeric($id) || $id <= 0) {
                unset($ids[$i]);
                continue;
            }
        }

        // 校验
        if (empty($ids)) {
            $this->renderAjax(1, '参数错误！');
        }

        RootPermissionInterface::deleteMultiByIds(array('ids' => $ids));

        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '删除成功！');
        $this->renderAjax(0);
    }

}
