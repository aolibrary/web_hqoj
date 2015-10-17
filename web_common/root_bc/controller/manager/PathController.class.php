<?php

class PathController extends ProjectController {

    public function iframeAddAction() {

        $managerId = Request::getGET('manager-id', 0);
        $managerInfo = RootManagerInterface::getById(array('id' => $managerId));
        if (empty($managerInfo)) {
            $this->renderIframeError('管理员不存在！');
        }
        $userInfo = UserCommonInterface::getById(array('id' => $managerInfo['user_id']));
        $this->renderIframe(array(
            'userInfo'  => $userInfo,
        ), 'manager/iframe/add_path.php');
    }

    public function ajaxAddAction() {

        $managerId = Request::getPOST('manager-id', 0);
        $path = Request::getPOST('path', '');

        if (empty($managerId) || empty($path)) {
            $this->renderAjax(1, '参数错误！');
        }

        if (!RootPermissionInterface::isValidPath(array('path' => $path))) {
            $this->renderAjax(1, '路径不合法！');
        }

        // 判断manager是否存在
        $managerInfo = RootManagerInterface::getById(array('id' => $managerId));
        if (empty($managerInfo)) {
            $this->renderAjax(1, '管理员不存在！');
        }

        // 判断路径是否存在
        if (!RootPermissionInterface::findPath(array('path' => $path))) {
            if (rtrim($path, '/') == $path) {
                $this->renderAjax(1, '权限不存在！');
            } else {
                $this->renderAjax(1, '权限文件夹不存在！');
            }
        }

        // 判断是否已经被包含
        $include = RootManagerInterface::checkPermission(array(
            'id'    => $managerId,
            'path'  => $path,
        ));
        if ($include) {
            $this->renderAjax(1, '权限已经拥有！');
        }

        // 添加
        RootRelationInterface::save(array(
            'manager_id'    => $managerId,
            'path'          => $path,
        ));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '添加权限成功！');
        $this->renderAjax(0);
    }

    public function iframeRemoveAction() {

        $managerId = Request::getGET('manager-id', 0);
        $managerInfo = RootManagerInterface::getById(array('id' => $managerId));
        if (empty($managerInfo)) {
            $this->renderIframeError('管理员不存在！');
        }
        $userInfo = UserCommonInterface::getById(array('id' => $managerInfo['user_id']));
        $this->renderIframe(array(
            'userInfo'  => $userInfo,
        ), 'manager/iframe/remove_path.php');
    }

    public function ajaxRemoveAction() {

        $managerId = Request::getPOST('manager-id', 0);
        $path = Request::getPOST('path', '');

        if (empty($managerId) || empty($path)) {
            $this->renderAjax(1, '参数错误！');
        }

        if (!RootPermissionInterface::isValidPath(array('path' => $path))) {
            $this->renderAjax(1, '路径不合法！');
        }

        // 路径不存在
        $where = array(
            array('manager_id', '=', $managerId),
            array('path', '=', $path),
        );
        $rowInfo = RootRelationInterface::getRow(array(
            'where' => $where,
        ));
        if (empty($rowInfo)) {
            $this->renderAjax(1, '管理员权限路径不存在！');
        }

        // 删除
        RootRelationInterface::deleteById(array('id' => $rowInfo['id']));
        $this->setNotice(FrameworkVars::NOTICE_SUCCESS, '移除权限成功！');
        $this->renderAjax(0);
    }

}