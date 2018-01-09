<?php
/**
 * @Author:      wj
 * @DateTime:    2017-2-27 14:19:42
 * @Description: 评审模块 -- A man of words and not of deeds is like a garden full of weeds
 */
class review_Json extends iJson {

        public public function aaa($value='')
        {
            # 增加一个方法
        }



        public public function ccc($value='')
        {
            # 增加一个方法
        }





        public function __construct() {
                $this->business = review_Business::instance();
        }

        // 审核推荐结束状态
        private $iPartOneStatus = 200;
        // 结果审批结束状态
        private $iPartTwoStatus = 100;
        // 审核通过成功状态
        private $iPartThreeStatus = 1;
        /**
         *                    .::::.
         *                  .::::::::.
         *                 :::::::::::
         *             ..:::::::::::'
         *           '::::::::::::'
         *             .::::::::::
         *        '::::::::::::::..
         *             ..::::::::::::.
         *           ``::::::::::::::::
         *            ::::``:::::::::'        .:::.
         *           ::::'   ':::::'       .::::::::.
         *         .::::'      ::::     .:::::::'::::.
         *        .:::'       :::::  .:::::::::' ':::::.
         *       .::'        :::::.:::::::::'      ':::::.
         *      .::'         ::::::::::::::'         ``::::.
         *  ...:::           ::::::::::::'              ``::.
         * ```` ':.          ':::::::::'                  ::::..
         *                    '.:::::'                    ':'````..
         */
#================================================================================#
#==========================↓↓↓        private       ↓↓↓==========================#
#================================================================================#
        /**
         * [此模块专用数据字典]
         * @author  [wj]
         * @date    [2017-4-12 10:44:21]
         * @version [1.0.0]
         * @param   string   种类
         * @return  array    kv
         */
        private function myDictionary($sType, $mValue) {
            $aResult = array();
            switch ($sType){
                case 'review_status':
                    $aResult[1] = '<font style="color: red;">未提交</font>';
                    $aResult[2] = '<font style="color: green;">等待申报</font>';
                    $aResult[3] = '<font style="color: violet;">申报中</font>';
                    $aResult[4] = '<font style="color: indigo;">评审中</font>';
                    $aResult[5] = '<font style="color: brown;">公示中</font>';
                    $aResult[6] = '已结束';
                    break;
                case 'declare_status':
                    $aResult[9] = '未提交';
                    $aResult[0] = '<font style="color: blue;">审核中</font>';
                    $aResult[2] = '<font style="color: brown;">暂缓通过</font>';
                    $aResult[3] = '<font style="color: red;">审核不通过</font>';
                    $aResult[8] = '<font style="color: indigo;">专家评审或管理员复审中</font>';
                    $aResult[7] = '<font style="color: green;">复审通过</font>';
                    $aResult[6] = '<font style="color: green;">通过</font>';
                    break;
                case 'expert_group_status':
                    $aResult[1] = '<font style="color: red;">未配置</font>';
                    $aResult[2] = '<font style="color: blue;">已配置</font>';
                    $aResult[3] = '<font style="color: green;">配置完成</font>';
                    break;
                case 'expert_allot_status':
                    $aResult[1] = '<font style="color: red;">专家组专家分配</font>';
                    $aResult[2] = '<font style="color: blue;">分配学员阶段</font>';
                    $aResult[3] = '<font style="color: green;">完成分配阶段</font>';
                    break;
                case 'file_type':
                    $aResult[1] = '政策文件';
                    $aResult[2] = '申报指南';
                    $aResult[3] = '常见问题';
                    $aResult[4] = '通知公告';
                    $aResult[5] = '展示图片';
                    $aResult[6] = '申报材料模板';
                    break;
                default:
                    break;
            }
            return $aResult[$mValue];
        }
        private function getDeclareStatusKeyByStatus($iStatus) {
            $iKey = $iStatus % 100;
            if ($iStatus == 0) {
                return 9;
            }
            if ($iStatus == $this->iPartOneStatus) {
                return 8;
            }
            if ($iStatus == $this->iPartTwoStatus) {
                return 7;
            }
            if ($iStatus == $this->iPartThreeStatus) {
                return 6;
            }
            return $iKey;
        }


        /**
         * [根据类别获取需要得到的参数]
         * @author  [wj]
         * @date    [2017-7-18 09:43:52]
         * @version [1.0.0]
         * @param   string   种类
         * @return  array    kv
         */
        private function getParameterByManager($sType=NULL, $sStatus=NULL) {
            # 通过登录者code取出转换为区域信息
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aManager = organ_Api::instance()->getIsSchoolLevelByCode($aAdminInfo['area_code']);
            $iLevel = $aManager['level'];
            $aResult = array();
            switch ($sType){
                case 'select':
                    $aResult['level'] = $iLevel;
                    $aResult['status'] = ($iLevel + 1) * 100;
                    break;
                case 'auditing':
                    if ($aManager['isSchool']) {
                        $aResult['status'] = ($sStatus == 1) ? 0 : ($iLevel * 100) + $sStatus;
                    } else {
                        $aResult['status'] = ($sStatus == 1) ? (($iLevel+2) * 100) : ($iLevel * 100) + $sStatus;
                    }
                    $aResult['level'] = $aManager['level'];
                    $aResult['auditing_status'] = ($iLevel * 100) + $sStatus;
                    $aResult['isSchool'] = $aManager['isSchool'];
                    break;
                case 'report':
                    $aResult['status'] = $iLevel * 100;
                    break;
                case 'allot':
                    $aResult['status'] = $this->iPartOneStatus;
                    break;
                case 'auditing_again':
                    $aResult['status'] = $this->iPartTwoStatus + $sStatus;
                    break;
                case 'publicity':
                    $aResult['status'] = $this->iPartTwoStatus;
                    break;
                default:
                    break;
            }
            return $aResult;
        }
#================================================================================#
#==========================↑↑↑        private       ↑↑↑==========================#
#================================================================================#


#================================================================================#
#==========================↓↓↓     评审项目管理     ↓↓↓==========================#
#================================================================================#
        /**
         * 评审模块列表
         */
        public function ajaxReviewList(){
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            $oResult = $this->business->getReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $psxl = dic_Api::instance()->getDicKVListByCode('psxl');
            $xmxn = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['declare_start_date'] = date('Y-m-d H:i:s',$value['declare_start_date']);
                $oResult['aList'][$key]['declare_end_date'] = date('Y-m-d H:i:s',$value['declare_end_date']);
                $oResult['aList'][$key]['year'] = $xmxn[$value['year']];
                $oResult['aList'][$key]['series'] = $psxl[$value['series']];
                // 修改提交后的评审记录
                if ($value['status'] > 1) {
                    // 如果大于申报开始时间修改状态为申报中
                    if ($value['declare_start_date'] <= time()) {
                        $this->business->editReviewByID($value['id'], array('status'=>3));
                        $oResult['aList'][$key]['status'] = $value['status'] = 3;
                    }
                    // 如果大于申报结束时间修改状态为审核中
                    if (($value['declare_end_date']+86400) <= time()) {
                        $this->business->editReviewByID($value['id'], array('status'=>4));
                        $oResult['aList'][$key]['status'] = $value['status'] = 4;
                    }
                }
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('review_status', $value['status']);
                $oResult['aList'][$key]['user_id'] = $this->iManagerID;
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 评审模块写操作
         */
        public function ajaxReviewPage() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aError = $aProject = $aResult = $aDeclare = array();
            // 接收参数
            if (http::isPOST()) {
                // 修改标识
                $iID = safe::instance()->getSafeValue(http::POST('id', NULL));
                $aProject['name'] = safe::instance()->getSafeValue(http::POST('name', NULL));
                $aProject['year'] = safe::instance()->getSafeValue(http::POST('year', NULL));
                $aProject['series'] = safe::instance()->getSafeValue(http::POST('series', NULL));
                $aProject['review_rule_id'] = safe::instance()->getSafeValue(http::POST('review_rule_id', NULL));
                if (empty($aProject['review_rule_id'])) {
                    unset($aProject['review_rule_id']);
                }
                $aProject['declare_rule'] = safe::instance()->getSafeValue(http::POST('declare_rule', NULL));
                $aProject['declare_start_date'] = strtotime(safe::instance()->getSafeValue(http::POST('declare_start_date', NULL)));
                $aProject['declare_end_date'] = strtotime(safe::instance()->getSafeValue(http::POST('declare_end_date', NULL)));
                $aDeclareMaterial = http::POST('declare_material', NULL);
                if (empty($aDeclareMaterial)) {
                    $aError['material'] = true;
                }
                // 满足条件后进行写操作
                if (empty($aError)) {
                    // 添加操作
                    if (empty($iID)) {
                        $aProject['code'] = $aAdminInfo['area_code'];
                        $aProject['status'] = 1;
                        $aProject['expert_group_status'] = 1;
                        $aProject['expert_allot_status'] = 1;
                        $aProject['create_id'] = $this->iManagerID;
                        $aProject['create_date'] = time();
                        $iID = $this->business->addReview($aProject);
                        manager_Api::instance()->addLog($this->iManagerID, 'review', 'ajaxReviewPage', 2, '添加:id=' . $iID);
                        $aResult['message'] = '添加成功';
                        $aResult['success'] = true;
                    // 修改操作
                    } else {
                        $aProject['modify_id'] = $this->iManagerID;
                        $aProject['modify_date'] = time();
                        $this->business->editReviewByID($iID, $aProject);
                        manager_Api::instance()->addLog($this->iManagerID, 'review', 'ajaxReviewPage', 3, '修改:id=' . $iID);
                        $aResult['message'] = '修改成功';
                        $aResult['success'] = true;
                    }
                    //获取附件信息
                    $oFile = http::POST('jFile', NULL);
                    // 附件入库
                    file_Api::instance()->uploadBigFileAttachment('review', $iID, $oFile);
                    // 先清掉，再插入
                    $this->business->deleteReviewDeclareMaterialByID($iID);
                    if (!empty($aDeclareMaterial)) {
                        foreach ($aDeclareMaterial as $key => $value) {
                            $aDeclare['review_id'] = $iID;
                            $aDeclare['material_id'] = $value;
                            $aDeclare['create_id'] = $this->iManagerID;
                            $aDeclare['create_date'] = time();
                            $this->business->addReviewDeclareMaterial($aDeclare);
                        }
                    }
                } else {
                    $aResult['aError'] = $aError;
                    $aResult['message'] = $aError['material'] ? '请至少选择一项申报材料.' : '操作失败';
                    $aResult['success'] = false;
                }
            }
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * Ajax删除
         */
        public function ajaxDeleteReview() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aResult = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxDeleteReview');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                if (!empty($iID) && is_numeric($iID)) {
                    $aResult['result'] = $this->business->deleteReviewByID($iID);
                    $aResult['success'] = true;
                    $aResult['message'] = '删除信息:ID=' . $iID . '成功';
                    manager_Api::instance()->addLog($this->iManagerID, 'review', 'ajaxDeleteReview', 1, '移除评审项目:id=' . $iID);
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '删除信息:ID=' . $iID . '失败';
                    manager_Api::instance()->addLog($this->iManagerID, 'review', 'ajaxDeleteReview', 1, '移除评审项目失败:id=' . $iID);
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无删除权限!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }


        /**
         * Ajax提交
         */
        public function ajaxReviewSubmit() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxReviewSubmit');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                if (!empty($iID) && is_numeric($iID)) {
                    $aProject['status'] = 2;
                    $aProject['modify_id'] = $this->iManagerID;
                    $aProject['modify_date'] = time();
                    $aResult['result'] = $this->business->editReviewByID($iID, $aProject);
                    $aResult['success'] = true;
                    $aResult['message'] = '提交成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '提交失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }
#================================================================================#
#==========================↑↑↑     评审项目管理     ↑↑↑==========================#
#================================================================================#


#================================================================================#
#==========================↓↓↓     项目文件管理     ↓↓↓==========================#
#================================================================================#
        /**
         * 项目文件管理
         */
        public function ajaxReviewFileList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            $oResult = $this->business->getReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $psxl = dic_Api::instance()->getDicKVListByCode('psxl');
            $xmxn = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['declare_start_date'] = date('Y-m-d H:i:s',$value['declare_start_date']);
                $oResult['aList'][$key]['declare_end_date'] = date('Y-m-d H:i:s',$value['declare_end_date']);
                $oResult['aList'][$key]['year'] = $xmxn[$value['year']];
                $oResult['aList'][$key]['series'] = $psxl[$value['series']];
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('review_status', $value['status']);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 项目文件管理文件列表
         */
        public function ajaxReviewFileAttachmentList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['type'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('type', NULL)));
            $aSearch['review_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_id', NULL)));
            $oResult = $this->business->getReviewFileListPage($iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['create_date'] = date('Y-m-d H:i:s',$value['create_date']);
                $oResult['aList'][$key]['type'] = $this->myDictionary('file_type', $value['type']);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * [上传文件写操作]
         * @author  [wj]
         * @date    [2017-7-24 09:35:01]
         * @version [1.0.0]
         * @param   array  表单数据
         * @return  json   是否成功
         */
        public function ajaxReviewFileAttachmentPage() {
            manager_Api::instance()->checkRight('review', 'reviewFileUploadList');
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aError = $aProject = $aResult = array();
            if (http::isPOST()) {
                $iID = intval(http::POST('id', 0));
                // 文件类别
                $aProject['type'] = safe::instance()->getSafeValue(http::POST('type', NULL));
                $aProject['review_id'] = safe::instance()->getSafeValue(http::POST('review_id', NULL));
                // 获取附件信息
                $oFile = http::POST('jFile', NULL);
                if (empty($oFile)) {
                    $aResult['message'] = '操作失败';
                    $aResult['success'] = false;
                } else {
                    foreach ($oFile as $key => $value) {
                        $oF = '';
                        $oF = json_decode(stripslashes($value));
                        $aProject['name'] = $oF[0]->fileName;
                        $aProject['url'] = $oF[0]->url;
                        $aProject['create_id'] = $this->iManagerID;
                        $aProject['create_date'] = time();
                        $this->business->addReviewFile($aProject);
                    }
                    $aResult['message'] = '添加成功';
                    $aResult['success'] = true;
                }
            }
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * Ajax删除
         */
        public function ajaxDeleteReviewFileAttachment() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aResult = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'reviewFileUploadList');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                if (!empty($iID) && is_numeric($iID)) {
                    $aResult['result'] = $this->business->deleteReviewFileByID($iID);
                    $aResult['success'] = true;
                    $aResult['message'] = '删除信息:ID=' . $iID . '成功';
                    manager_Api::instance()->addLog($this->iManagerID, 'review', 'ajaxDeleteReview', 1, '移除评审项目:id=' . $iID);
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '删除信息:ID=' . $iID . '失败';
                    manager_Api::instance()->addLog($this->iManagerID, 'review', 'ajaxDeleteReview', 1, '移除评审项目失败:id=' . $iID);
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无删除权限!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }
#================================================================================#
#==========================↑↑↑     项目文件管理     ↑↑↑==========================#
#================================================================================#


#================================================================================#
#==========================↓↓↓       审核推荐       ↓↓↓==========================#
#================================================================================#
        /**
         * 审核推荐列表
         */
        public function ajaxReviewNominateList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            // 已提交
            $aSearch['gt_eq_status'] = 2;
            $oResult = $this->business->getReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $psxl = dic_Api::instance()->getDicKVListByCode('psxl');
            $xmxn = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['declare_start_date'] = date('Y-m-d H:i:s',$value['declare_start_date']);
                $oResult['aList'][$key]['declare_end_date'] = date('Y-m-d H:i:s',$value['declare_end_date']);
                $oResult['aList'][$key]['year'] = $xmxn[$value['year']];
                $oResult['aList'][$key]['series'] = $psxl[$value['series']];
                // 如果大于申报开始时间修改状态为申报中
                if ($value['declare_start_date'] <= time()) {
                    $aProject = array();
                    $aProject['status'] = 3;
                    $this->business->editReviewByID($value['id'], $aProject);
                    $oResult['aList'][$key]['status'] = $value['status'] = 3;
                }
                // 如果大于申报结束时间修改状态为审核中
                if (($value['declare_end_date']+86400) <= time()) {
                    $aProject = array();
                    $aProject['status'] = 4;
                    $this->business->editReviewByID($value['id'], $aProject);
                    $oResult['aList'][$key]['status'] = $value['status'] = 4;
                }
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('review_status', $value['status']);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 审核推荐教师列表
         */
        public function ajaxReviewAuditingFirstList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['idcardno'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('idcardno', NULL)));
            $aSearch['sex_code'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('sex_code', NULL)));
            $aSearch['review_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_id', NULL)));
            $iTab = urldecode(safe::instance()->getSafeValue(http::REQUEST('tabs', NULL)));
            $aSearch['code'] = $aAdminInfo['area_code'];
            // 通过查询矩阵获取状态
            $aMatrix = $this->getParameterByManager('select');
            // 根据不同的权限的设置状态
            switch ($iTab) {
                // 已审核
                case 2:
                    $aSearch['lt_status'] = $aMatrix['status'];
                    break;
                // 待审核
                default:
                    $aSearch['status'] = $aMatrix['status'];
                    $aSearch['pending'] = 1;
                    break;
            }
            $oResult = $this->business->getReviewDeclareUserListPage($iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('declare_status', $this->getDeclareStatusKeyByStatus($value['status']));
                if ($value['retreat'] == 1) {
                    $oResult['aList'][$key]['status_name'] = '<font style="color: indigo;">退回补报</font>';
                }
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 审核推荐审核操作
         */
        public function ajaxReviewAuditingFirst() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'reviewAuditingFirstList');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                $sStatus = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
                $aMatrix = $this->getParameterByManager('auditing', $sStatus);
                $aProject['status'] = $aMatrix['status'];
                $aProject['pending'] = ($sStatus == 1) ? 1 : 0;
                $aProject['retreat'] = ($sStatus == 1) ? 1 : 0;
                $aLog['auditing_status'] = $aMatrix['auditing_status'];
                $aLog['auditing_suggestion'] = safe::instance()->getSafeValue(http::REQUEST('suggestion', NULL));
                if (!empty($iID) && is_numeric($iID)) {
                    $aProject['modify_id'] = $this->iManagerID;
                    $aProject['modify_date'] = time();
                    $this->business->editReviewDeclareByID($iID, $aProject);
                    $aRD = $this->business->getReviewDeclareInfoByID($iID);
                    $aLog['review_id'] = $aRD['review_id'];
                    $aLog['user_id'] = $aRD['user_id'];
                    $aLog['auditing_user_id'] = $this->iManagerID;
                    $aLog['auditing_area_code'] = $aAdminInfo['area_code'];
                    $aLog['create_id'] = $this->iManagerID;
                    $aLog['create_date'] = time();
                    $this->business->addReviewAuditingLog($aLog);
                    $aResult['success'] = true;
                    $aResult['message'] = '操作成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '操作失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }


        /**
         * 补报退回操作
         */
        public function ajaxReviewAuditingRetreat() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'reviewAuditingFirstList');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                $sStatus = 1;
                $aMatrix = $this->getParameterByManager('auditing', $sStatus);
                $aProject['status'] = $aMatrix['status'];
                $aProject['pending'] = !empty($aMatrix['isSchool']) ? 0 : 1;
                $aProject['retreat'] = 1;
                $aLog['auditing_status'] = $aMatrix['auditing_status'];
                if (!empty($iID) && is_numeric($iID)) {
                    $aProject['modify_id'] = $this->iManagerID;
                    $aProject['modify_date'] = time();
                    $this->business->editReviewDeclareByID($iID, $aProject);
                    $aRD = $this->business->getReviewDeclareInfoByID($iID);
                    $aLog['review_id'] = $aRD['review_id'];
                    $aLog['user_id'] = $aRD['user_id'];
                    $aLog['auditing_user_id'] = $this->iManagerID;
                    $aLog['auditing_area_code'] = $aAdminInfo['area_code'];
                    $aLog['create_id'] = $this->iManagerID;
                    $aLog['create_date'] = time();
                    // $this->business->addReviewAuditingLog($aLog);
                    $aResult['success'] = true;
                    $aResult['message'] = '操作成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '操作失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }


        /**
         * 审核推荐上报操作
         */
        public function ajaxReviewReport() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aResult = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxReviewReport');
            if ($bCompetence) {
                $aSearch['review_id'] = intval(http::instance()->POST('id', NULL));
                $aSearch['code'] = $aAdminInfo['area_code'];
                // 通过上报矩阵获取状态
                $aMatrix = $this->getParameterByManager('report');
                $aSearch['status'] = $aMatrix['status'];
                $oResult = $this->business->getReviewDeclareUserListPage(1, 'all', $aSearch);
                $bStatus = true;
                foreach ($oResult as $key => $value) {
                    $aProject = array();
                    $aProject['pending'] = 1;
                    $bTemp = $this->business->editReviewDeclareByID($value['id'], $aProject);
                    if ($bTemp != 0) {
                        $bStatus = $bTemp && $bStatus;
                    }
                }
                if ($bStatus) {
                    $aResult['success'] = true;
                    $aResult['message'] = '操作成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '操作失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }


        /**
         * 审核推荐查看教师列表
         */
        public function ajaxReviewNominateReadList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['idcardno'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('idcardno', NULL)));
            $aSearch['sex_code'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('sex_code', NULL)));
            $aSearch['review_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_id', NULL)));
            $aSearch['code'] = $aAdminInfo['area_code'];
            // 通过查询矩阵获取状态
            $aMatrix = $this->getParameterByManager('select');
            $aSearch['gt_status'] = $aMatrix['status'];
            $oResult = $this->business->getReviewDeclareUserListPage($iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }
#================================================================================#
#==========================↑↑↑       审核推荐       ↑↑↑==========================#
#================================================================================#


#================================================================================#
#==========================↓↓↓       结果审批       ↓↓↓==========================#
#================================================================================#
        /**
         * 结果审批列表
         */
        public function ajaxReviewAuditingList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            // 已提交
            $aSearch['gt_eq_status'] = 4;
            $oResult = $this->business->getReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $psxl = dic_Api::instance()->getDicKVListByCode('psxl');
            $xmxn = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['declare_start_date'] = date('Y-m-d H:i:s',$value['declare_start_date']);
                $oResult['aList'][$key]['declare_end_date'] = date('Y-m-d H:i:s',$value['declare_end_date']);
                $oResult['aList'][$key]['year'] = $xmxn[$value['year']];
                $oResult['aList'][$key]['series'] = $psxl[$value['series']];
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('review_status', $value['status']);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 结果审批教师列表
         */
        public function ajaxReviewAuditingSecondList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['idcardno'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('idcardno', NULL)));
            $aSearch['sex_code'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('sex_code', NULL)));
            $aSearch['review_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_id', NULL)));
            $iTab = urldecode(safe::instance()->getSafeValue(http::REQUEST('tabs', NULL)));
            $aSearch['code'] = $aAdminInfo['area_code'];
            // 通过查询矩阵获取状态
            $aMatrix = $this->getParameterByManager('allot');
            // 根据不同的权限的设置状态
            switch ($iTab) {
                // 已审核
                case 2:
                    $aSearch['lt_status'] = $aMatrix['status'];
                    break;
                // 待审核
                default:
                    $aSearch['status'] = $aMatrix['status'];
                    break;
            }
            $oResult = $this->business->getReviewDeclareUserListPage($iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('declare_status', $this->getDeclareStatusKeyByStatus($value['status']));
                $oResult['aList'][$key]['interview_mark'] = isset($value['interview_mark']) ? $value['interview_mark'] : 0;
                $aVariableColumns = $this->getExpertGroupAverage($value['review_id'], $value['user_id']);
                $oResult['aList'][$key] = array_merge($oResult['aList'][$key], $aVariableColumns);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * [ajaxReviewExpertHeaderList 获取列表头信息]
         * @author  [wj]
         * @date    [2017-11-7 16:43:13]
         * @version [1.0.0]
         * @param   [int]   评审项目id  [review_id]
         * @return  [json]  表头数据
         */
        public function ajaxReviewExpertHeaderList() {
            // 初始化
            $aResult = $aFirst = $aSecond = $aThird = array();
            // 获取评审项目id
            $iRID = safe::instance()->getSafeValue(http::REQUEST('review_id', 0));
            // 获取专家组列表
            $aGroupList = $this->business->getReviewGroupListPage(1, 'all', array('review_id'=>$iRID));
            // 自定义参数
            $aUserDefined = array('quantitative'=>'专家定量平均分', 'qualitative'=>'专家定性评分');
            // 拼凑数据
            foreach ($aUserDefined as $k1 => $v1) {
                // 第一层合并单元格
                $aFirstIndex = 0;
                foreach ($aGroupList as $k2 => $v2) {
                    $aGroupQuestList = $this->business->getReviewGroupQuestListByRGID($v2['id']);
                    // 第二层合并单元格
                    $aSecondIndex = 0;
                    foreach ($aGroupQuestList as $k3 => $v3) {
                        // 获取问卷类型
                        $aQuestList = quest_Api::instance()->getQuestList(array('id'=>$v3));
                        switch ($k1) {
                            // 定性
                            case 'qualitative':
                                if ($aQuestList['cate'] == 3) {
                                    $aTemp = array();
                                    $aTemp['title'] = $aQuestList['name'];
                                    $aTemp['field'] = 'group'.$v2['id'].$aQuestList['id'];
                                    $aTemp['colspan'] = 1;
                                    $aTemp['width'] = $aTemp['colspan']*100;
                                    $aThird[] = $aTemp;
                                    $aSecondIndex++;
                                }
                                break;
                            // 其他
                            default:
                                if ($aQuestList['cate'] != 3) {
                                    $aTemp = array();
                                    $aTemp['title'] = $aQuestList['name'];
                                    $aTemp['field'] = 'group'.$v2['id'].$aQuestList['id'];
                                    $aTemp['colspan'] = 1;
                                    $aTemp['width'] = $aTemp['colspan']*100;
                                    $aThird[] = $aTemp;
                                    $aSecondIndex++;
                                }
                                break;
                        }
                    }
                    if ($aSecondIndex > 0) {
                        $aTemp = array();
                        $aTemp['title'] = $v2['name'];
                        $aTemp['colspan'] = $aSecondIndex;
                        $aTemp['width'] = $aTemp['colspan']*100;
                        $aSecond[] = $aTemp;
                        $aFirstIndex += $aSecondIndex;
                    }
                }
                if ($aFirstIndex > 0) {
                    $aTemp = array();
                    $aTemp['title'] = $v1;
                    $aTemp['colspan'] = $aFirstIndex;
                    $aTemp['width'] = $aTemp['colspan']*100;
                    $aFirst[] = $aTemp;
                }
            }
            $aResult = array($aFirst, $aSecond, $aThird);
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 获取专家组平均分
         */
        private function getExpertGroupAverage($iRID, $iUID) {
            $aResult = array();
            // 1.通过评审id --> 获取专家组然后遍历
            $aGroupList = $this->business->getReviewGroupListPage(1, 'all', array('review_id'=>$iRID));
            $iTotalAverage = 0;
            foreach ($aGroupList as $k1 => $v1) {
                // 2.通过专家组id --> 获取问卷id
                $aGroupQuestList = $this->business->getReviewGroupQuestListByRGID($v1['id']);
                $aGroupExpertList = $this->business->getReviewGroupExpertByRGID($v1['id']);
                $iExpertNum = count($aGroupExpertList);
                foreach ($aGroupQuestList as $k2 => $v2) {
                    // 3.遍历问卷遍历专家 --> 获取平均分
                    // 获取问卷类型
                    $aQuestList = quest_Api::instance()->getQuestList(array('id'=>$v2));
                    // 已答人数
                    $iYepNum = 0;
                    // 答题分数
                    $iMark = 0;
                    // 定性
                    if ($aQuestList['cate'] == 3) {
                        $aResult['group'.$v1['id'].$v2] = '尚未启用';
                    // 定量
                    } else {
                        foreach ($aGroupExpertList as $k3 => $v3) {
                            // 查看答题分数
                            $aTemp = array();
                            $aTemp['expert_id'] = $v3['expert_id'];
                            $aTemp['teacher_id'] = $iUID;
                            $aTemp['quest_id'] = $aQuestList['quest_id'];
                            $aTemp['list_id'] = $v2;
                            $aAnswerMark = quest_Api::instance()->getQuestListIsAnswer($aTemp);
                            if (empty($aAnswerMark)) {
                                continue;
                            } else {
                                foreach ($aAnswerMark as $k4 => $v4) {
                                    $iMark += $v4['get_mark'];
                                }
                                $iYepNum++;
                            }
                        }
                        $iAverage = ($iYepNum != 0) ? ($iMark/$iYepNum) : 0;
                        $sRatio = '<font style="color: green;">'.$iYepNum.'</font>'.'/'.$iExpertNum;
                        $sSentence = $sRatio.'--<font style="color: red;">'.round($iAverage, 2).'</font>分';
                        $aResult['group'.$v1['id'].$v2] = $sSentence;
                        $iTotalAverage += round($iAverage, 2);
                    }
                }
            }
            $aResult['total_average'] = $iTotalAverage;
            return $aResult;
        }


        /**
         * 结果审批审核操作
         */
        public function ajaxReviewAuditingSecond() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxReviewAuditingSecond');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                $sStatus = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
                $aMatrix = $this->getParameterByManager('auditing_again', $sStatus);
                $aProject['status'] = $aMatrix['status'];
                $aProject['pending'] = 0;
                $aLog['auditing_status'] = $aMatrix['status'];
                $aLog['auditing_suggestion'] = safe::instance()->getSafeValue(http::REQUEST('suggestion', NULL));
                if (!empty($iID) && is_numeric($iID)) {
                    $aProject['modify_id'] = $this->iManagerID;
                    $aProject['modify_date'] = time();
                    $this->business->editReviewDeclareByID($iID, $aProject);
                    $aRD = $this->business->getReviewDeclareInfoByID($iID);
                    $aLog['review_id'] = $aRD['review_id'];
                    $aLog['user_id'] = $aRD['user_id'];
                    $aLog['auditing_user_id'] = $this->iManagerID;
                    $aLog['auditing_area_code'] = $aAdminInfo['area_code'];
                    $aLog['create_id'] = $this->iManagerID;
                    $aLog['create_date'] = time();
                    $this->business->addReviewAuditingLog($aLog);
                    $aResult['success'] = true;
                    $aResult['message'] = '操作成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '操作失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }
#================================================================================#
#==========================↑↑↑       结果审批       ↑↑↑==========================#
#================================================================================#


#================================================================================#
#==========================↓↓↓      专家组管理      ↓↓↓==========================#
#================================================================================#
        /**
         * 专家组评审项目列表
         */
        public function ajaxReviewExpertGroupList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            // 已提交
            $aSearch['gt_eq_status'] = 4;
            $oResult = $this->business->getReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $psxl = dic_Api::instance()->getDicKVListByCode('psxl');
            $xmxn = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['declare_start_date'] = date('Y-m-d H:i:s',$value['declare_start_date']);
                $oResult['aList'][$key]['declare_end_date'] = date('Y-m-d H:i:s',$value['declare_end_date']);
                $oResult['aList'][$key]['year'] = $xmxn[$value['year']];
                $oResult['aList'][$key]['series'] = $psxl[$value['series']];
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('review_status', $value['status']);
                $oResult['aList'][$key]['create_date'] = date('Y-m-d H:i:s',$value['create_date']);
                $oResult['aList'][$key]['expert_group_status_name'] = $this->myDictionary('expert_group_status', $value['expert_group_status']);
                $aMaterial = $this->business->getReviewDeclareMaterialByID($value['id']);
                $oResult['aList'][$key]['display'] = ((!empty($aMaterial)) && (!empty($value['review_rule_id']))) ? true : false;
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 专家组管理列表
         */
        public function ajaxReviewExpertGroupDetailList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['review_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_id', NULL)));
            $oResult = $this->business->getReviewGroupListPage($iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
                $aTemp = $this->declareOptionAndaQuestList($value['id']);
                $oResult['aList'][$key]['declare'] = $aTemp['declare'];
                $oResult['aList'][$key]['quest'] = $aTemp['quest'];
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }
        /**
         * 展示数据翻译
         */
        private function declareOptionAndaQuestList($iRGID) {
            // 数据字典
            $aSBCL = material_Api::instance()->materialKVList();
            // 被选中的部分
            $aSBCLOption = $this->business->getReviewGroupDeclareMaterialByRGID($iRGID);
            $aQuestOption = $this->business->getReviewGroupQuestListByRGID($iRGID);
            $aResult = $aDeclare = $aQuest = array();
            foreach ($aSBCLOption as $key => $value) {
                $aDeclare[] = $aSBCL[$value];
            }
            foreach ($aQuestOption as $key => $value) {
                $aQuestList = quest_Api::instance()->getQuestList(array('id'=>$value));
                $aQuest[] = $aQuestList['name'];
            }
            $aResult['declare'] = implode($aDeclare, ',');
            $aResult['quest'] = implode($aQuest, ',');
            return $aResult;
        }


        /**
         * 专家组管理写操作
         */
        public function ajaxReviewExpertGroupPage() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aError = $aProject = $aResult = $aDeclare = $aQuest = array();
            // 接收参数
            if (http::isPOST()) {
                // 修改标识
                $iID = safe::instance()->getSafeValue(http::POST('id', NULL));
                $aProject['name'] = safe::instance()->getSafeValue(http::POST('name', NULL));
                $aProject['review_id'] = safe::instance()->getSafeValue(http::POST('review_id', NULL));
                $aDeclareMaterial = http::POST('declare_material', NULL);
                $aQuestList = http::POST('quest_list', NULL);
                // 满足条件后进行写操作
                if (empty($aError)) {
                    // 添加操作
                    if (empty($iID)) {
                        $aProject['create_id'] = $this->iManagerID;
                        $aProject['create_date'] = time();
                        $iID = $this->business->addReviewGroup($aProject);
                        manager_Api::instance()->addLog($this->iManagerID, 'review', 'ajaxReviewExpertGroupPage', 2, '添加:id=' . $iID);
                        $aResult['message'] = '添加成功';
                        $aResult['success'] = true;
                    // 修改操作
                    } else {
                        $aProject['modify_id'] = $this->iManagerID;
                        $aProject['modify_date'] = time();
                        $this->business->editReviewGroupByID($iID, $aProject);
                        manager_Api::instance()->addLog($this->iManagerID, 'review', 'ajaxReviewExpertGroupPage', 3, '修改:id=' . $iID);
                        $aResult['message'] = '修改成功';
                        $aResult['success'] = true;
                    }
                    // 先清掉，再插入
                    $this->business->deleteReviewGroupDeclareMaterialByRGID($iID);
                    if (!empty($aDeclareMaterial)) {
                        foreach ($aDeclareMaterial as $key => $value) {
                            $aDeclare['review_id'] = $aProject['review_id'];
                            $aDeclare['review_group_id'] = $iID;
                            $aDeclare['material_id'] = $value;
                            $aDeclare['create_id'] = $this->iManagerID;
                            $aDeclare['create_date'] = time();
                            $this->business->addReviewGroupDeclareMaterial($aDeclare);
                        }
                    }
                    // 先清掉，再插入
                    $this->business->deleteReviewGroupQuestListByRGID($iID);
                    if (!empty($aQuestList)) {
                        foreach ($aQuestList as $key => $value) {
                            $aQuest['review_id'] = $aProject['review_id'];
                            $aQuest['review_group_id'] = $iID;
                            $aQuest['quest_list_id'] = $value;
                            $aQuest['create_id'] = $this->iManagerID;
                            $aQuest['create_date'] = time();
                            $this->business->addReviewGroupQuestList($aQuest);
                        }
                    }
                    // 如果项目当前的那个状态是1，就修改为2
                    $aReview = $this->business->getReviewByID($aProject['review_id']);
                    if ($aReview['expert_group_status'] == 1) {
                        // 修改项目的状态
                        $aGroup = array();
                        $aGroup['expert_group_status'] = 2;
                        $this->business->editReviewByID($aProject['review_id'], $aGroup);
                    }
                } else {
                    $aResult['aError'] = $aError;
                    $aResult['message'] = '操作失败';
                    $aResult['success'] = false;
                }
            }
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 专家组删除操作
         */
        public function ajaxDeleteReviewExpertGroup() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aResult = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxDeleteReviewExpertGroup');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                if (!empty($iID) && is_numeric($iID)) {
                    $aResult['result'] = $this->business->deleteReviewGroupByID($iID);
                    $this->business->deleteReviewGroupDeclareMaterialByRGID($iID);
                    $this->business->deleteReviewGroupQuestListByRGID($iID);
                    $aResult['success'] = true;
                    $aResult['message'] = '删除信息:ID=' . $iID . '成功';
                    manager_Api::instance()->addLog($this->iManagerID, 'review', 'ajaxDeleteReviewExpertGroup', 1, '移除评审项目:id=' . $iID);
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '删除信息:ID=' . $iID . '失败';
                    manager_Api::instance()->addLog($this->iManagerID, 'review', 'ajaxDeleteReviewExpertGroup', 1, '移除评审项目失败:id=' . $iID);
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无删除权限!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }


        /**
         * 专家组提交
         */
        public function ajaxReviewExpertGroupSubmit() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxReviewExpertGroupSubmit');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                if (!empty($iID) && is_numeric($iID)) {
                    $aProject['expert_group_status'] = 3;
                    $aProject['modify_id'] = $this->iManagerID;
                    $aProject['modify_date'] = time();
                    $aResult['result'] = $this->business->editReviewByID($iID, $aProject);
                    $aResult['success'] = true;
                    $aResult['message'] = '提交配置成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '提交配置失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }
#================================================================================#
#==========================↑↑↑      专家组管理      ↑↑↑==========================#
#================================================================================#


#================================================================================#
#==========================↓↓↓     专家分配管理     ↓↓↓==========================#
#================================================================================#
        /**
         * 专家分配管理评审项目列表
         */
        public function ajaxReviewExpertAllotList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            // 已提交
            $aSearch['gt_eq_status'] = 4;
            $aSearch['expert_group_status'] = 3;
            $oResult = $this->business->getReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $psxl = dic_Api::instance()->getDicKVListByCode('psxl');
            $xmxn = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['declare_start_date'] = date('Y-m-d H:i:s',$value['declare_start_date']);
                $oResult['aList'][$key]['declare_end_date'] = date('Y-m-d H:i:s',$value['declare_end_date']);
                $oResult['aList'][$key]['year'] = $xmxn[$value['year']];
                $oResult['aList'][$key]['series'] = $psxl[$value['series']];
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('review_status', $value['status']);
                $oResult['aList'][$key]['create_date'] = date('Y-m-d H:i:s',$value['create_date']);
                $oResult['aList'][$key]['expert_allot_status_name'] = $this->myDictionary('expert_allot_status', $value['expert_allot_status']);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 专家分配管理写操作
         */
        public function ajaxReviewExpertAllotPage() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aError = $aProject = $aResult = array();
            // 接收参数
            if (http::isPOST()) {
                // 修改标识
                $aProject['review_id'] = safe::instance()->getSafeValue(http::POST('rid', NULL));
                $aProject['review_group_id'] = safe::instance()->getSafeValue(http::POST('rgid', NULL));
                $aExpert = http::POST('expert', NULL);
                if (empty($aExpert) || !is_array($aExpert) || (count($aExpert) < 3)) {
                    $aError['expert'] = true;
                }
                // 满足条件后进行写操作
                if (empty($aError)) {
                    // 先清掉，再插入
                    $this->business->deleteReviewGroupExpertByRGID($aProject['review_group_id']);
                    foreach ($aExpert as $key => $value) {
                        $aProject['expert_id'] = $value;
                        $aProject['create_id'] = $this->iManagerID;
                        $aProject['create_date'] = time();
                        $this->business->addReviewGroupExpert($aProject);
                    }
                    $aResult['message'] = '操作成功';
                    $aResult['success'] = true;
                } else {
                    if (!empty($aError['expert'])) {
                        $aResult['message'] = '一个专家组至少分配3个专家~';
                    } else {
                        $aResult['message'] = '操作失败';
                    }
                    $aResult['success'] = false;
                }
            }
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 分配完学员的提交
         */
        public function ajaxReviewExpertAllotGroupSubmit() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxReviewExpertAllotGroupSubmit');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                if (!empty($iID) && is_numeric($iID)) {
                    $aProject['expert_allot_status'] = 2;
                    $aProject['modify_id'] = $this->iManagerID;
                    $aProject['modify_date'] = time();
                    $aResult['result'] = $this->business->editReviewByID($iID, $aProject);
                    $aResult['success'] = true;
                    $aResult['message'] = '完成分配成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '完成分配失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }


        /**
         * 专家分配学员列表
         */
        public function ajaxReviewExpertAllotTeacherList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['idcardno'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('idcardno', NULL)));
            $aSearch['review_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_id', NULL)));
            $iRGID = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_group_id', NULL)));
            $aSearch['code'] = $aAdminInfo['area_code'];
            // 通过查询矩阵获取状态
            $aMatrix = $this->getParameterByManager('allot');
            $aSearch['lt_eq_status'] = $aMatrix['status'];
            $oResult = $this->business->getReviewDeclareUserListPage($iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('declare_status', $this->getDeclareStatusKeyByStatus($value['status']));
                $aTemp = array();
                $aTemp['review_id'] = $value['review_id'];
                $aTemp['review_group_id'] = $iRGID;
                $aTemp['teacher_id'] = $value['user_id'];
                $aExpertInfo = $this->business->getReviewExpertTeacherBySearch($aTemp);
                $aEname = array();
                foreach ($aExpertInfo as $k => $v) {
                    $aEname[] = expertinfo_Api::instance()->getExpertinfoNameByUserIds($v['expert_id']);
                }
                $oResult['aList'][$key]['expert_num'] = count($aEname);
                $oResult['aList'][$key]['expert_name'] = implode($aEname, ',');
                // 显示分类信息，需要存在申报意向，['教研员', '一线教师']
                $aSBYX = $this->business->getReviewDeclareTableByRDID('au', $value['id']);
                $oResult['aList'][$key]['type'] = empty($aSBYX['nsbfl']) ? '' : $aSBYX['nsbfl'];
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 专家组随机分配
         */
        public function ajaxReviewExpertRandomAllotTeacher() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxReviewExpertRandomAllotTeacher');
            if ($bCompetence) {
                $iRID = intval(http::instance()->POST('rid', NULL));
                $iRGID = intval(http::instance()->POST('rgid', NULL));
                if (!empty($iRID) && is_numeric($iRID) && !empty($iRGID) && is_numeric($iRGID)) {
                    // 当前组的全删掉
                    $this->business->deleteReviewExpertTeacherByRGID($iRGID);
                    $aResult['result'] = $this->randomAllotAlgorithm($iRID, $iRGID);
                    $aResult['success'] = true;
                    $aResult['message'] = '随机分配操作成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '随机分配操作失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }
        /**
         * 专家组随机分配简单算法
         */
        private function randomAllotAlgorithm($iRID, $iRGID) {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 此专家组内的专家
            $aExpert = $this->business->getReviewGroupExpertByRGID($iRGID);
            // 此项目需要审核的
            $aSearch = array();
            $aSearch['review_id'] = $iRID;
            $aSearch['code'] = $aAdminInfo['area_code'];
            // 通过查询矩阵获取状态
            $aMatrix = $this->getParameterByManager('allot');
            $aSearch['status'] = $aMatrix['status'];
            $aTeacher = $this->business->getReviewDeclareUserListPage(1, 'all', $aSearch);
            # 算法开始
            # 计算专家人数
            $aResult = array();
            $iExpertNum = count($aExpert);
            $iTemp = 0;
            foreach ($aTeacher as $key => $value) {
                $aTemp = array();
                for ($i=0; $i < 3; $i++) {
                    $iMagic = $iTemp%$iExpertNum;
                    $aTemp[$i]['expert_id'] = $aExpert[$iMagic]['expert_id'];
                    $aTemp[$i]['teacher_id'] = $value['user_id'];
                    $iTemp++;
                }
                $aResult[] = $aTemp;
            }
            # 算法结束
            foreach ($aResult as $key => $value) {
                foreach ($value as $k => $v) {
                    $aProject = array();
                    $aProject['review_id'] = $iRID;
                    $aProject['review_group_id'] = $iRGID;
                    $aProject['expert_id'] = $v['expert_id'];
                    $aProject['teacher_id'] = $v['teacher_id'];
                    $aProject['create_id'] = $this->iManagerID;
                    $aProject['create_date'] = time();
                    $this->business->addReviewExpertTeacher($aProject);
                }
            }
        }


        /**
         * 专家组手动分配
         */
        public function ajaxReviewExpertHandAllotTeacher() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxReviewExpertRandomAllotTeacher');
            if ($bCompetence) {
                $iRID = intval(http::instance()->POST('rid', NULL));
                $iRGID = intval(http::instance()->POST('rgid', NULL));
                $iUserID = intval(http::instance()->POST('user_id', NULL));
                $aExpert = http::instance()->POST('expert', NULL);
                if (!empty($iRID) && is_numeric($iRID) && !empty($iRGID) && is_numeric($iRGID)) {
                    // 删除这个教师当前的专家
                    $this->business->deleteReviewExpertTeacherByRGIDAndTID($iRGID, $iUserID);
                    foreach ($aExpert as $k => $v) {
                        $aProject = array();
                        $aProject['review_id'] = $iRID;
                        $aProject['review_group_id'] = $iRGID;
                        $aProject['expert_id'] = $v;
                        $aProject['teacher_id'] = $iUserID;
                        $aProject['create_id'] = $this->iManagerID;
                        $aProject['create_date'] = time();
                        $this->business->addReviewExpertTeacher($aProject);
                    }
                    $aResult['success'] = true;
                    $aResult['message'] = '手动分配操作成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '手动分配操作失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }


        /**
         * 分配完学员的提交
         */
        public function ajaxReviewExpertAllotSubmit() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxReviewExpertAllotSubmit');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                if (!empty($iID) && is_numeric($iID)) {
                    $aProject['expert_allot_status'] = 3;
                    $aProject['modify_id'] = $this->iManagerID;
                    $aProject['modify_date'] = time();
                    $aResult['result'] = $this->business->editReviewByID($iID, $aProject);
                    $aResult['success'] = true;
                    $aResult['message'] = '完成分配成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '完成分配失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }
#================================================================================#
#==========================↑↑↑     专家分配管理     ↑↑↑==========================#
#================================================================================#


#================================================================================#
#==========================↓↓↓     面试成绩录入     ↓↓↓==========================#
#================================================================================#
        /**
         * 面试成绩录入评审项目列表
         */
        public function ajaxReviewInterviewMarkList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            // 已提交
            $aSearch['gt_eq_status'] = 4;
            $aSearch['expert_group_status'] = 3;
            $oResult = $this->business->getReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $psxl = dic_Api::instance()->getDicKVListByCode('psxl');
            $xmxn = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['declare_start_date'] = date('Y-m-d H:i:s',$value['declare_start_date']);
                $oResult['aList'][$key]['declare_end_date'] = date('Y-m-d H:i:s',$value['declare_end_date']);
                $oResult['aList'][$key]['year'] = $xmxn[$value['year']];
                $oResult['aList'][$key]['series'] = $psxl[$value['series']];
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('review_status', $value['status']);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 面试成绩录入评审项目列表
         */
        public function ajaxReviewInterviewMarkDetailList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['idcardno'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('idcardno', NULL)));
            $aSearch['sex_code'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('sex_code', NULL)));
            $aSearch['review_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_id', NULL)));
            $iTab = urldecode(safe::instance()->getSafeValue(http::REQUEST('tabs', NULL)));
            $aSearch['code'] = $aAdminInfo['area_code'];
            // 通过查询矩阵获取状态
            $aMatrix = $this->getParameterByManager('allot');
            $aSearch['lt_eq_status'] = $aMatrix['status'];
            // 根据不同的权限的设置状态
            switch ($iTab) {
                // 已录入
                case 2:
                    $aSearch['t_entering'] = true;
                    break;
                // 待录入
                default:
                    $aSearch['f_entering'] = true;
                    break;
            }
            $oResult = $this->business->getReviewDeclareUserListPage($iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('declare_status', $this->getDeclareStatusKeyByStatus($value['status']));
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 面试成绩录入操作
         */
        public function ajaxReviewInterviewMark() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxReviewInterviewMark');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('review_declare_id', NULL));
                $aProject['interview_mark'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('interview_mark', NULL)));
                if (!empty($iID) && is_numeric($iID)) {
                    $aProject['modify_id'] = $this->iManagerID;
                    $aProject['modify_date'] = time();
                    $aResult['result'] = $this->business->editReviewDeclareByID($iID, $aProject);
                    $aResult['success'] = true;
                    $aResult['message'] = '面试成绩录入成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '面试成绩录入失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }
#================================================================================#
#==========================↑↑↑     面试成绩录入     ↑↑↑==========================#
#================================================================================#


#================================================================================#
#==========================↓↓↓     专家评价部分     ↓↓↓==========================#
#================================================================================#
        /**
         * 专家评审列表
         */
        public function ajaxExpertReviewList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['review_name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_name', NULL)));
            $aSearch['review_group_name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_group_name', NULL)));
            $aSearch['teacher_name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('teacher_name', NULL)));
            $aSearch['review_status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_status', NULL)));
            if (empty($aSearch['review_status']) || ($aSearch['review_status'] < 4)) {
                // 评审项目状态 >= 评审中
                $aSearch['gt_eq_review_status'] = 4;
            }
            // 专家ID
            $aSearch['expert_id'] = $aAdminInfo['user_id'];
            // 专家分配 == 完成分配阶段
            $aSearch['expert_allot_status'] = 3;
            // 获取审核推荐的最后状态
            $aSearch['lt_eq_teacher_status'] = $this->iPartOneStatus;
            $oResult = $this->business->getExpertReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $aXL = dic_Api::instance()->getDicKVListByCode('psxl');
            $aXN = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['series'] = !empty($aXL[$value['series']]) ? $aXL[$value['series']] : '';
                $oResult['aList'][$key]['year'] = !empty($aXN[$value['year']]) ? $aXN[$value['year']] : '';
                $oResult['aList'][$key]['review_status_name'] = $this->myDictionary('review_status', $value['review_status']);
                $oResult['aList'][$key]['teacher_status_name'] = $this->myDictionary('declare_status', $this->getDeclareStatusKeyByStatus($value['teacher_status']));
                // 答卷链接
                $aParameters = array();
                $aReview = $this->business->getReviewByID($value['review_id']);
                $aParameters['quest_id'] = $aReview['review_rule_id'];
                $aParameters['teacher_id'] = $value['teacher_id'];
                $aParameters['review_group_id'] = $value['review_group_id'];
                $oResult['aList'][$key]['sAnswerURL'] = url::instance()->pageMakeURL('quest', 'expertAnswer', $aParameters);
                // 问卷组列表
                $aGroupQuestList = $this->business->getReviewGroupQuestListByRGID($value['review_group_id']);
                $iIndex = 0;
                $iNum = count($aGroupQuestList);
                foreach ($aGroupQuestList as $k => $v) {
                    // 查看是否已经答过
                    $aTemp = array();
                    $aTemp['expert_id'] = $this->iManagerID;
                    $aTemp['teacher_id'] = $value['teacher_id'];
                    $aTemp['quest_id'] = $aReview['review_rule_id'];
                    $aTemp['list_id'] = $v;
                    $aAnswerMark = quest_Api::instance()->getQuestListIsAnswer($aTemp);
                    if (!empty($aAnswerMark)) {
                        $iIndex++;
                    }
                }
                // $iNum == $iIndex为已经答完，其他为至少有一套问卷未答完
                $oResult['aList'][$key]['display'] = ($iNum == $iIndex) ? true : false;
                $sProgress = floor(($iIndex / $iNum) * 100) . '%';
                $oResult['aList'][$key]['display_name'] = '<font style="color: red;">'.$sProgress.'</font>';
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }
#================================================================================#
#==========================↑↑↑     专家评价部分     ↑↑↑==========================#
#================================================================================#


#================================================================================#
#==========================↓↓↓     评审公示管理     ↓↓↓==========================#
#================================================================================#
        /**
         * 公示教师列表
         */
        public function ajaxReviewPublicityTeacherList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['idcardno'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('idcardno', NULL)));
            $aSearch['sex_code'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('sex_code', NULL)));
            $aSearch['review_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_id', NULL)));
            $aSearch['code'] = $aAdminInfo['area_code'];
            // 复审通过的教师状态
            $aSearch['lt_status'] = $this->iPartOneStatus;
            $oResult = $this->business->getReviewDeclareUserListPage($iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('declare_status', $this->getDeclareStatusKeyByStatus($value['status']));
                $oResult['aList'][$key]['interview_mark'] = isset($value['interview_mark']) ? $value['interview_mark'] : 0;
                $aVariableColumns = $this->getExpertGroupAverage($value['review_id'], $value['user_id']);
                $oResult['aList'][$key] = array_merge($oResult['aList'][$key], $aVariableColumns);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 公示操作
         */
        public function ajaxReviewPublicity() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = manager_Api::instance()->checkRight('review', 'ajaxReviewPublicity');
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                $iStatus = intval(http::instance()->POST('status', NULL));
                if (!empty($iID) && is_numeric($iID) && !empty($iStatus)) {
                    if ($iStatus == 6) {
                        $aSearch['review_id'] = $iID;
                        $aSearch['code'] = $aAdminInfo['area_code'];
                        // 通过上报矩阵获取状态
                        $aMatrix = $this->getParameterByManager('publicity');
                        $aSearch['status'] = $aMatrix['status'];
                        $oResult = $this->business->getReviewDeclareUserListPage(1, 'all', $aSearch);
                        foreach ($oResult as $key => $value) {
                            $aProject = array();
                            $aProject['status'] = $this->iPartThreeStatus;
                            $this->business->editReviewDeclareByID($value['id'], $aProject);
                        }
                    }
                    $aProject['status'] = $iStatus;
                    $aProject['modify_id'] = $this->iManagerID;
                    $aProject['modify_date'] = time();
                    $aResult['result'] = $this->business->editReviewByID($iID, $aProject);
                    $aResult['success'] = true;
                    $aResult['message'] = '提交成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '提交失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }
#================================================================================#
#==========================↑↑↑     评审公示管理     ↑↑↑==========================#
#================================================================================#


#================================================================================#
#==========================↓↓↓     结果报批管理     ↓↓↓==========================#
#================================================================================#
        /**
         * 结果报批列表
         */
        public function ajaxReviewResultApprovalList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            // 已提交
            $aSearch['status'] = 6;
            $oResult = $this->business->getReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $psxl = dic_Api::instance()->getDicKVListByCode('psxl');
            $xmxn = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['declare_start_date'] = date('Y-m-d H:i:s',$value['declare_start_date']);
                $oResult['aList'][$key]['declare_end_date'] = date('Y-m-d H:i:s',$value['declare_end_date']);
                $oResult['aList'][$key]['year'] = $xmxn[$value['year']];
                $oResult['aList'][$key]['series'] = $psxl[$value['series']];
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('review_status', $value['status']);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 公示教师列表
         */
        public function ajaxReviewResultApprovalTeacherList() {
            $this->iManagerID = manager_Api::instance()->getManagerID();
            $aAdminInfo = manager_Api::instance()->getManagerByManagerID($this->iManagerID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['idcardno'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('idcardno', NULL)));
            $aSearch['sex_code'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('sex_code', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            $aSearch['review_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_id', NULL)));
            $aSearch['code'] = $aAdminInfo['area_code'];
            // 取出审核推荐后的所有学员
            $aSearch['lt_status'] = $this->iPartOneStatus;
            $oResult = $this->business->getReviewDeclareUserListPage($iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('declare_status', $this->getDeclareStatusKeyByStatus($value['status']));
                $oResult['aList'][$key]['interview_mark'] = isset($value['interview_mark']) ? $value['interview_mark'] : 0;
                $aVariableColumns = $this->getExpertGroupAverage($value['review_id'], $value['user_id']);
                $oResult['aList'][$key] = array_merge($oResult['aList'][$key], $aVariableColumns);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }
#================================================================================#
#==========================↑↑↑     结果报批管理     ↑↑↑==========================#
#================================================================================#


#===============================================up up M=====================================================#

#=============================================down down U===================================================#


        /**
         * 教师评审列表
         */
        public function ajaxUserReviewList(){
            $this->iUserID = user_Api::instance()->getCurrentUserID();
            $aUserInfo = user_Api::instance()->getUserByUserIDs($this->iUserID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            $aSearch['series'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('series', NULL)));
            $aSearch['gt_eq_status'] = 2;
            // $aSearch['declare_date'] = time();
            $oResult = $this->business->getReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $psxl = dic_Api::instance()->getDicKVListByCode('psxl');
            $xmxn = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['declare_start_date'] = date('Y-m-d H:i:s',$value['declare_start_date']);
                $oResult['aList'][$key]['declare_end_date'] = date('Y-m-d H:i:s',$value['declare_end_date']);
                $oResult['aList'][$key]['year'] = $xmxn[$value['year']];
                $oResult['aList'][$key]['series'] = $psxl[$value['series']];
                // 如果大于申报开始时间修改状态为申报中
                if ($value['declare_start_date'] <= time()) {
                    $this->business->editReviewByID($value['id'], array('status'=>3));
                    $oResult['aList'][$key]['status'] = $value['status'] = 3;
                }
                // 如果大于申报结束时间修改状态为审核中
                if (($value['declare_end_date']+86400) <= time()) {
                    $this->business->editReviewByID($value['id'], array('status'=>4));
                    $oResult['aList'][$key]['status'] = $value['status'] = 4;
                }
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('review_status', $value['status']);
                // 获取申报状态
                $aDeclare = $this->business->getReviewDeclareByID($value['id'], $this->iUserID);
                $oResult['aList'][$key]['declare_status'] = $aDeclare['status'];
                $oResult['aList'][$key]['retreat'] = $aDeclare['retreat'];
                $oResult['aList'][$key]['declare_status_name'] = $this->myDictionary('declare_status', $this->getDeclareStatusKeyByStatus($aDeclare['status']));
                if ($aDeclare['retreat'] == 1) {
                    $oResult['aList'][$key]['declare_status_name'] = '<font style="color: indigo;">退回补报</font>';
                }
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 教师评审附件列表
         */
        public function ajaxUserReviewFileList(){
            $this->iUserID = user_Api::instance()->getCurrentUserID();
            $aUserInfo = user_Api::instance()->getUserByUserIDs($this->iUserID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('name', NULL)));
            $aSearch['status'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('status', NULL)));
            $aSearch['series'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('series', NULL)));
            $aSearch['gt_eq_status'] = 4;
            $oResult = $this->business->getReviewListPage($iPageNow, $iRows, $aSearch);
            // 数据字典
            $psxl = dic_Api::instance()->getDicKVListByCode('psxl');
            $xmxn = dic_Api::instance()->getDicKVListByCode('xmxn');
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['declare_start_date'] = date('Y-m-d H:i:s',$value['declare_start_date']);
                $oResult['aList'][$key]['declare_end_date'] = date('Y-m-d H:i:s',$value['declare_end_date']);
                $oResult['aList'][$key]['year'] = $xmxn[$value['year']];
                $oResult['aList'][$key]['series'] = $psxl[$value['series']];
                $oResult['aList'][$key]['status_name'] = $this->myDictionary('review_status', $value['status']);
                // 获取申报状态
                $aDeclare = $this->business->getReviewDeclareByID($value['id'], $this->iUserID);
                $oResult['aList'][$key]['declare_status_name'] = $this->myDictionary('declare_status', $this->getDeclareStatusKeyByStatus($aDeclare['status']));
                $oResult['aList'][$key]['display'] = true;
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }
        public function ajaxUserReviewFileExcelList() {
            $this->iUserID = user_Api::instance()->getCurrentUserID();
            $aUserInfo = user_Api::instance()->getUserByUserIDs($this->iUserID);
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            $aSearch['type'] = 6;
            $aSearch['review_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('review_id', NULL)));
            $oResult = $this->business->getReviewFileListPage($iPageNow, $iRows, $aSearch);
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * 申报材料选项树
         */
        public function ajaxReviewDeclareMaterialTree(){
            $aResult = $oResult = $aSearch = array();
            $iID = intval(http::REQUEST('id', 0));
            // 申报材料选项
            $aSBCL = material_Api::instance()->materialKVList();
            // 申报材料选项
            $aMaterial = $this->business->getReviewDeclareMaterialByID($iID);
            foreach ($aMaterial as $k => $v) {
                $sLength = mb_strlen($aSBCL[$v], "utf-8");
                $sName = ($sLength > 20) ? mb_substr($aSBCL[$v], 0, 20, 'utf-8').'...' : $aSBCL[$v];
                $aResult[$k]['text'] = $sName;
                $aResult[$k]['id'] = $v;
                $aResult[$k]['isParent'] = false;
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }


        /**
         * 申报材料选项树
         */
        public function ajaxReviewDeclareExpertTree(){
            $aResult = $oResult = $aSearch = array();
            // 申报材料选项
            $aSBCL = material_Api::instance()->materialKVList();
            $iRGID = safe::instance()->getSafeValue(http::REQUEST('review_group_id', NULL));
            // 专家组可见申报材料选项
            $aMaterial = $this->business->getReviewGroupDeclareMaterialByRGID($iRGID);
            foreach ($aMaterial as $k => $v) {
                $sLength = mb_strlen($aSBCL[$v], "utf-8");
                $sName = ($sLength > 20) ? mb_substr($aSBCL[$v], 0, 20, 'utf-8').'...' : $aSBCL[$v];
                $aResult[$k]['text'] = $sName;
                $aResult[$k]['id'] = $v;
                $aResult[$k]['isParent'] = false;
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }


        /**
         * 单位列表
         */
        public function ajaxSchoolList() {
            // 初始化参数
            $aSearch = $aResult = array();
            // 分页
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            // 查询条件
            $aSearch['name'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('organ_name', NULL)));
            $aSearch['area_id'] = urldecode(safe::instance()->getSafeValue(http::REQUEST('area_id', NULL)));
            // 默认条件
            $aSearch['orgtype'] = 5;
            $aSearch['status'] = 1;
            // 学校列表
            $oResult = organ_Api::instance()->getTrainListPage($iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['area_name'] = area_Api::instance()->getAreaNamesBycode($value['region_code']);
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            unset($oResult);
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * Ajax提交申报
         */
        public function ajaxUserDeclareSubmit() {
            $this->iUserID = user_Api::instance()->getCurrentUserID();
            $aResult = $aProject = array();
            $aResult['status'] = false;
            $bCompetence = true;
            if ($bCompetence) {
                // 评审项目id
                $iID = intval(http::instance()->POST('id', NULL));
                // 申报信息
                $aDeclare = $this->business->getReviewDeclareByID($iID, $this->iUserID);
                $aDeclareAA = $this->business->getReviewDeclareAAByRDID($aDeclare['id']);
                $aManager = organ_Api::instance()->getIsSchoolLevelByCode($aDeclareAA['school_code']);
                $iLevel = $aManager['level'];
                if (!empty($iID) && is_numeric($iID)) {
                    $aProject['status'] = ($iLevel + 1) * 100;
                    $aProject['pending'] = 1;
                    $aProject['retreat'] = 0;
                    $this->business->editReviewDeclareByID($aDeclare['id'], $aProject);
                    $aResult['result'] = true;
                    $aResult['success'] = true;
                    $aResult['message'] = '提交申报成功';
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '提交申报失败';
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无权限操作!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }


#================================================================================#
#==========================↓↓↓     申报素材合集     ↓↓↓==========================#
#================================================================================#


        /**
         * [评审申报材料写操作]
         * @author  [wj]
         * @date    [2017-7-12 15:28:34]
         * @version [1.0.0]
         * @param   int    $iID    修改标识
         * @param   string $sTableName   表名称
         * @return  json   是否成功
         */
        public function ajaxUserDeclareSinglePage() {
            header("Content-type: application/json");
            # 参数初始化
            $aProject = $aResult = array();
            # 心灵捕手
            try {
                $aResult['success'] = false;
                # 登录标识
                $this->iUserID = user_Api::instance()->getCurrentUserID();
                if (empty($this->iUserID)) {
                    $aResult['message'] = '用户标识为空,请重新登录.';
                    throw new Exception(json::instance()->enCode($aResult));
                }
                # 表单提交
                if (http::isPOST()) {
                    # 模板信息
                    $sTableName = safe::instance()->getSafeValue(http::POST('tpl', NULL));
                    # token验证
                    $sToken = safe::instance()->getSafeValue(http::POST('token', NULL));
                    if (empty($sToken)) {
                        $aResult['message'] = 'Token值不存在,请刷新页面.';
                        throw new Exception(json::instance()->enCode($aResult));
                    }
                    if (user_Api::instance()->getToken() === $sToken) {
                        # token刷新
                        user_Api::instance()->setToken();
                    } else {
                        $aResult['message'] = 'Token值验证失败,请刷新页面.';
                        throw new Exception(json::instance()->enCode($aResult));
                    }
                    # 接收表单信息
                    $iID = intval(http::POST('id', 0));
                    // 根据模板名称，动态控制模板对应表字段
                    switch ($sTableName) {
                        // 基本情况
                        case 'aa':
                            $aProject['school'] = safe::instance()->getSafeValue(http::POST('school', NULL));
                            $aProject['school_code'] = safe::instance()->getSafeValue(http::POST('school_code', NULL));
                            $aProject['name'] = safe::instance()->getSafeValue(http::POST('name', NULL));
                            $aProject['idcardno'] = safe::instance()->getSafeValue(http::POST('idcardno', NULL));
                            $aProject['birthday'] = safe::instance()->getSafeValue(http::POST('birthday', NULL));
                            $aUser['sex_code'] = $aProject['sex_code'] = safe::instance()->getSafeValue(http::POST('sex_code', NULL));
                            $aUser['sex'] = $aProject['sex'] = dic_Api::instance()->getDicNameByCodeVlue('xb',$aProject['sex_code']);

                            $aProject['city_id'] = safe::instance()->getSafeValue(http::POST('city_id', NULL));
                            $aProject['county_id'] = safe::instance()->getSafeValue(http::POST('county_id', NULL));
                            $aCity = area_Api::instance()->getAreaByID($aProject['city_id']);
                            $aCounty = area_Api::instance()->getAreaByID($aProject['county_id']);
                            $aProject['city_name'] = $aCity['name'];
                            $aProject['county_name'] = $aCounty['name'];
                            $aProject['company'] = safe::instance()->getSafeValue(http::POST('company', NULL));

                            // 如果该教师个人资料未填写性别，此处补全
                            user_Api::instance()->editUserByUserID($this->iUserID, $aUser);
                            $aProject['rjxd'] = safe::instance()->getSafeValue(http::POST('rjxd', NULL));
                            $aProject['rjxk'] = safe::instance()->getSafeValue(http::POST('rjxk', NULL));
                            $aProject['xrzc'] = safe::instance()->getSafeValue(http::POST('xrzc', NULL));
                            $aProject['zw'] = safe::instance()->getSafeValue(http::POST('zw', NULL));
                            $aProject['csjygznx'] = safe::instance()->getSafeValue(http::POST('csjygznx', NULL));
                            $aProject['jszgzl'] = safe::instance()->getSafeValue(http::POST('jszgzl', NULL));
                            $aProject['jszgzbh'] = safe::instance()->getSafeValue(http::POST('jszgzbh', NULL));
                            // 头像图片
                            $oFile = http::POST('image', NULL);
                            $oImage = json_decode(stripslashes($oFile[0]));
                            $aProject['photo'] = empty($oImage[0]) ? '' : $oImage[0]->url;
                            if (empty($aProject['photo'])) {
                                unset($aProject['photo']);
                            }
                            break;
                        // 最高学历
                        case 'ab':
                            $aProject['zgxl'] = safe::instance()->getSafeValue(http::POST('zgxl', NULL));
                            $aProject['bysj'] = safe::instance()->getSafeValue(http::POST('bysj', NULL));
                            $aProject['byxx'] = safe::instance()->getSafeValue(http::POST('byxx', NULL));
                            $aProject['zy'] = safe::instance()->getSafeValue(http::POST('zy', NULL));
                            break;
                        // 师德考核结果
                        case 'ad':
                            $aProject['year2012'] = safe::instance()->getSafeValue(http::POST('year2012', NULL));
                            $aProject['year2013'] = safe::instance()->getSafeValue(http::POST('year2013', NULL));
                            $aProject['year2014'] = safe::instance()->getSafeValue(http::POST('year2014', NULL));
                            $aProject['year2015'] = safe::instance()->getSafeValue(http::POST('year2015', NULL));
                            $aProject['year2016'] = safe::instance()->getSafeValue(http::POST('year2016', NULL));
                            break;
                        // 年度考核结果
                        case 'ae':
                            $aProject['year2012'] = safe::instance()->getSafeValue(http::POST('year2012', NULL));
                            $aProject['year2013'] = safe::instance()->getSafeValue(http::POST('year2013', NULL));
                            $aProject['year2014'] = safe::instance()->getSafeValue(http::POST('year2014', NULL));
                            $aProject['year2015'] = safe::instance()->getSafeValue(http::POST('year2015', NULL));
                            $aProject['year2016'] = safe::instance()->getSafeValue(http::POST('year2016', NULL));
                            break;
                        // 何时获何级别何学科带头人
                        case 'ao':
                            $aProject['sj'] = safe::instance()->getSafeValue(http::POST('sj', NULL));
                            break;
                        // 何时获何级中小学骨干教师
                        case 'aw':
                            $aProject['sj'] = safe::instance()->getSafeValue(http::POST('sj', NULL));
                            break;
                        // 何时获何级中小学名师
                        case 'ax':
                            $aProject['sj'] = safe::instance()->getSafeValue(http::POST('sj', NULL));
                            break;
                        // 何时获特级教师
                        case 'ay':
                            $aProject['sj'] = safe::instance()->getSafeValue(http::POST('sj', NULL));
                            break;
                        // 何时获何级名师名校长工作室主持人
                        case 'ba':
                            $aProject['sj'] = safe::instance()->getSafeValue(http::POST('sj', NULL));
                            break;
                        // 省级乡村名师工作室主持人或省级名师、名校长工作室优秀成员（助理）
                        case 'ap':
                            $aProject['gzsmc'] = safe::instance()->getSafeValue(http::POST('gzsmc', NULL));
                            break;
                        // 任现有职务以来主要业绩
                        case 'bb':
                            $aProject['yj'] = safe::instance()->getSafeValue(http::POST('yj', NULL));
                            break;
                        // 申报意向
                        case 'au':
                            $aKeyWordA = array();
                            // 实验室系列
                            $aKeyWordA['sysxl'] = 'sysxlrzzg010';
                            $aKeyWordB['sysxl'] = 'nsbxkzxx';
                            // 中小学特级名师骨干评审
                            $aKeyWordA['zxxxl'] = 'zxxjsrzzg030';
                            $aKeyWordB['zxxxl'] = 'nsbxkzxx';
                            // 高校院校系列
                            $aKeyWordA['gxyxxl'] = 'gxjsxlrzzg020';
                            $aKeyWordB['gxyxxl'] = 'nsbxkzxx';
                            // 中等职业学校系列
                            $aKeyWordA['zdzyxxxl'] = 'zdzyxxxlrzzg040';
                            $aKeyWordB['zdzyxxxl'] = 'nsbxkzxx';
                            // 中小学职称评审
                            $aKeyWordA['zxxzcps'] = 'zxxjsrzzg030';
                            $aKeyWordB['zxxzcps'] = 'nsbxkzxx';
                            $iReviewID = safe::instance()->getSafeValue(http::POST('review_id', NULL));
                            $aReview = $this->business->getReviewByID($iReviewID);
                            $sKeyWordA = $aKeyWordA[$aReview['series']];
                            $sKeyWordB = $aKeyWordB[$aReview['series']];
                            $aProject['rzzg_code'] = safe::instance()->getSafeValue(http::POST('rzzg_code', NULL));
                            $aProject['rzzg'] = dic_Api::instance()->getDicNameByCodeVlue($sKeyWordA, $aProject['rzzg_code']);
                            $aProject['nsbzczg_code'] = safe::instance()->getSafeValue(http::POST('nsbzczg_code', NULL));
                            $aProject['nsbzczg'] = dic_Api::instance()->getDicNameByCodeVlue($sKeyWordA, $aProject['nsbzczg_code']);
                            $aProject['nsbfl_code'] = safe::instance()->getSafeValue(http::POST('nsbfl_code', NULL));
                            $aProject['nsbfl'] = dic_Api::instance()->getDicNameByCodeVlue('nsbfl', $aProject['nsbfl_code']);
                            $aProject['nsbxk_first_code'] = safe::instance()->getSafeValue(http::POST('nsbxk_first_code', NULL));
                            $aProject['nsbxk_first'] = dic_Api::instance()->getDicNameByCodeVlue($sKeyWordB, $aProject['nsbxk_first_code']);
                            $aProject['nsbxk_second_code'] = safe::instance()->getSafeValue(http::POST('nsbxk_second_code', NULL));
                            $aProject['nsbxk_second'] = dic_Api::instance()->getDicNameByCodeVlue($aProject['nsbxk_first_code'], $aProject['nsbxk_second_code']);
                            break;
                        default:
                            # code...
                            break;
                    }
                    if (empty($iID)) {
                        $iRID = safe::instance()->getSafeValue(http::POST('review_id', NULL));
                        $aProject['review_declare_id'] = $this->business->getReviewDeclareIDByID($iRID, $this->iUserID);
                        $aProject['review_id'] = $iRID;
                        $aProject['user_id'] = $this->iUserID;
                        $aProject['create_id'] = $this->iUserID;
                        $aProject['create_date'] = time();
                        $iID = $this->business->addReviewDeclareTable($sTableName, $aProject);
                        if (!isset($iID)) {
                            $aResult['message'] = '添加失败.';
                            throw new Exception(json::instance()->enCode($aResult));
                        }
                        manager_Api::instance()->addLog($this->iUserID, 'review', 'ajaxUserDeclareSinglePage', 2, '添加:id=' . $iID);
                    } else {
                        $aProject['modify_id'] = $this->iUserID;
                        $aProject['modify_date'] = time();
                        $iTemp = $this->business->editReviewDeclareTableByID($sTableName, $iID, $aProject);
                        if (!isset($iTemp)) {
                            $aResult['message'] = '修改失败.';
                            throw new Exception(json::instance()->enCode($aResult));
                        }
                        manager_Api::instance()->addLog($this->iUserID, 'review', 'ajaxUserDeclareSinglePage', 3, '修改:id=' . $iID);
                    }
                    // 获取附件信息
                    $oFile = http::POST('jFile', NULL);
                    // 附件入库
                    $sModular = 'declare'.strtoupper($sTableName);
                    file_Api::instance()->uploadBigFileAttachment($sModular, $iID, $oFile);
                }
                $aResult['success'] = true;
                $aResult['message'] = '操作成功';
                echo json::instance()->enCode($aResult);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            exit;
        }


        /**
         * [评审申报材料写操作]
         * @author  [wj]
         * @date    [2017-7-12 15:11:49]
         * @version [1.0.0]
         * @param   int    $iID    修改标识
         * @param   string $sTableName   表名称
         * @return  json   是否成功
         */
        public function ajaxUserDeclareMultiplePage() {
            header("Content-type: application/json");
            # 参数初始化
            $aProject = $aResult = array();
            # 心灵捕手
            try {
                $aResult['success'] = false;
                # 登录标识
                $this->iUserID = user_Api::instance()->getCurrentUserID();
                if (empty($this->iUserID)) {
                    $aResult['message'] = '用户标识为空,请重新登录.';
                    throw new Exception(json::instance()->enCode($aResult));
                }
                # 表单提交
                if (http::isPOST()) {
                    # 接收表单信息
                    $iID = intval(http::POST('id', 0));
                    # 模板信息
                    $sTableName = safe::instance()->getSafeValue(http::POST('tpl', NULL));
                    # token验证
                    $sToken = safe::instance()->getSafeValue(http::POST('token', NULL));
                    $sOldToken = user_Api::instance()->getToken();
                    $aResult['token'] = user_Api::instance()->setToken();

                    if (empty($sToken)) {
                        $aResult['message'] = 'Token值不存在,请刷新页面.';
                        throw new Exception(json::instance()->enCode($aResult));
                    }
                    if ($sOldToken === $sToken) {
                        # token刷新
                        // $aResult['token'] = user_Api::instance()->setToken();
                    } else {
                        $aResult['message'] = 'Token值验证失败,请刷新页面.';
                        throw new Exception(json::instance()->enCode($aResult));
                    }
                    // 根据模板名称，动态控制模板对应表字段
                    switch ($sTableName) {
                        // 获荣誉称号情况
                        case 'ac':
                            $aProject['hjdw'] = safe::instance()->getSafeValue(http::POST('hjdw', NULL));
                            $aProject['hjmc'] = safe::instance()->getSafeValue(http::POST('hjmc', NULL));
                            $aProject['hjsj'] = safe::instance()->getSafeValue(http::POST('hjsj', NULL));
                            break;
                        // 任现有职务以来工作量及年度考核情况
                        case 'af':
                            $aProject['nd'] = safe::instance()->getSafeValue(http::POST('nd', NULL));
                            $aProject['rjxk'] = safe::instance()->getSafeValue(http::POST('rjxk', NULL));
                            $aProject['sjskss'] = safe::instance()->getSafeValue(http::POST('sjskss', NULL));
                            $aProject['ndshjg'] = safe::instance()->getSafeValue(http::POST('ndshjg', NULL));
                            break;
                        // 任现有职务以来承担县级以上教育行政部门组织的示范课等情况
                        case 'ag':
                            $aProject['zzbm'] = safe::instance()->getSafeValue(http::POST('zzbm', NULL));
                            $aProject['date'] = safe::instance()->getSafeValue(http::POST('date', NULL));
                            $aProject['place'] = safe::instance()->getSafeValue(http::POST('place', NULL));
                            $aProject['content'] = safe::instance()->getSafeValue(http::POST('content', NULL));
                            break;
                        // 任现有职务以来承担教育行政部门组织培训的情况
                        case 'ah':
                            $aProject['zzbm'] = safe::instance()->getSafeValue(http::POST('zzbm', NULL));
                            $aProject['startdate'] = safe::instance()->getSafeValue(http::POST('startdate', NULL));
                            $aProject['enddate'] = safe::instance()->getSafeValue(http::POST('enddate', NULL));
                            $aProject['place'] = safe::instance()->getSafeValue(http::POST('place', NULL));
                            $aProject['content'] = safe::instance()->getSafeValue(http::POST('content', NULL));
                            break;
                        // 参与其它形式的教育教学活动情况
                        case 'ai':
                            $aProject['zzbm'] = safe::instance()->getSafeValue(http::POST('zzbm', NULL));
                            $aProject['date'] = safe::instance()->getSafeValue(http::POST('date', NULL));
                            $aProject['place'] = safe::instance()->getSafeValue(http::POST('place', NULL));
                            $aProject['content'] = safe::instance()->getSafeValue(http::POST('content', NULL));
                            break;
                        // 指导学生情况
                        case 'aj':
                            $aProject['zzbm'] = safe::instance()->getSafeValue(http::POST('zzbm', NULL));
                            $aProject['content'] = safe::instance()->getSafeValue(http::POST('content', NULL));
                            break;
                        // 教研情况
                        case 'ak':
                            $aProject['kwmc'] = safe::instance()->getSafeValue(http::POST('kwmc', NULL));
                            $aProject['lwmc'] = safe::instance()->getSafeValue(http::POST('lwmc', NULL));
                            break;
                        // 任现有职务以来，指导本地中青年教师情况
                        case 'al':
                            $aProject['xmdw'] = safe::instance()->getSafeValue(http::POST('xmdw', NULL));
                            $aProject['cj'] = safe::instance()->getSafeValue(http::POST('cj', NULL));
                            $aProject['startdate'] = safe::instance()->getSafeValue(http::POST('startdate', NULL));
                            $aProject['enddate'] = safe::instance()->getSafeValue(http::POST('enddate', NULL));
                            $aProject['zdxg'] = safe::instance()->getSafeValue(http::POST('zdxg', NULL));
                            $aProject['zmr'] = safe::instance()->getSafeValue(http::POST('zmr', NULL));
                            break;
                        // 其他补充类材料
                        case 'am':
                            $aProject['name'] = safe::instance()->getSafeValue(http::POST('name', NULL));
                            break;
                        // 说课视频材料
                        case 'an':
                            $aProject['name'] = safe::instance()->getSafeValue(http::POST('name', NULL));
                            break;
                        // 工作简历
                        case 'aq':
                            $aProject['gzdw'] = safe::instance()->getSafeValue(http::POST('gzdw', NULL));
                            $aProject['rzsj'] = safe::instance()->getSafeValue(http::POST('rzsj', NULL));
                            $aProject['lzsj'] = safe::instance()->getSafeValue(http::POST('lzsj', NULL));
                            $aProject['zwzc'] = safe::instance()->getSafeValue(http::POST('zwzc', NULL));
                            $aProject['zmr'] = safe::instance()->getSafeValue(http::POST('zmr', NULL));
                            break;
                        // 受省内外教育行政部门邀请讲学等情况
                        case 'ar':
                            $aProject['jtnr'] = safe::instance()->getSafeValue(http::POST('jtnr', NULL));
                            $aProject['dd'] = safe::instance()->getSafeValue(http::POST('dd', NULL));
                            $aProject['snwyqmc'] = safe::instance()->getSafeValue(http::POST('snwyqmc', NULL));
                            break;
                        // 任现有职务以来发表论文情况
                        case 'as':
                            $aProject['tm'] = safe::instance()->getSafeValue(http::POST('tm', NULL));
                            $aProject['qc'] = safe::instance()->getSafeValue(http::POST('qc', NULL));
                            $aProject['sh'] = safe::instance()->getSafeValue(http::POST('sh', NULL));
                            break;
                        // 任现有职务以来教研课题（项目）获奖情况
                        case 'at':
                            $aProject['mc'] = safe::instance()->getSafeValue(http::POST('mc', NULL));
                            $aProject['dw'] = safe::instance()->getSafeValue(http::POST('dw', NULL));
                            $aProject['pm'] = safe::instance()->getSafeValue(http::POST('pm', NULL));
                            $aProject['dc'] = safe::instance()->getSafeValue(http::POST('dc', NULL));
                            break;
                        // 实录课视频材料
                        case 'av':
                            $aProject['name'] = safe::instance()->getSafeValue(http::POST('name', NULL));
                            break;
                        // 特殊申报材料
                        case 'az':
                            $aProject['first_name'] = safe::instance()->getSafeValue(http::POST('first_name', NULL));
                            $aProject['start_date'] = safe::instance()->getSafeValue(http::POST('start_date', NULL));
                            $aProject['end_date'] = safe::instance()->getSafeValue(http::POST('end_date', NULL));
                            $aProject['remark'] = safe::instance()->getSafeValue(http::POST('remark', NULL));
                            $aProject['detail'] = safe::instance()->getSafeValue(http::POST('detail', NULL));
                            $aProject['material_id'] = safe::instance()->getSafeValue(http::POST('material_id', NULL));
                            break;
                        default:
                            # code...
                            break;
                    }
                    if (empty($iID)) {
                        $iRID = safe::instance()->getSafeValue(http::POST('review_id', NULL));
                        $aProject['review_declare_id'] = $this->business->getReviewDeclareIDByID($iRID, $this->iUserID);
                        $aProject['review_id'] = $iRID;
                        $aProject['user_id'] = $this->iUserID;
                        $aProject['create_id'] = $this->iUserID;
                        $aProject['create_date'] = time();
                        $iID = $this->business->addReviewDeclareTable($sTableName, $aProject);
                        if (!isset($iID)) {
                            $aResult['message'] = '添加失败.';
                            throw new Exception(json::instance()->enCode($aResult));
                        }
                        manager_Api::instance()->addLog($this->iUserID, 'review', 'ajaxUserDeclareMultiplePage', 2, '添加:id=' . $iID);
                    } else {
                        $aProject['modify_id'] = $this->iUserID;
                        $aProject['modify_date'] = time();
                        $iTemp = $this->business->editReviewDeclareTableByID($sTableName, $iID, $aProject);
                        if (!isset($iTemp)) {
                            $aResult['message'] = '修改失败.';
                            throw new Exception(json::instance()->enCode($aResult));
                        }
                        manager_Api::instance()->addLog($this->iUserID, 'review', 'ajaxUserDeclareMultiplePage', 3, '修改:id=' . $iID);
                    }
                    // 获取附件信息
                    $oFile = http::POST('jFile', NULL);
                    // 附件入库
                    $sModular = 'declare'.strtoupper($sTableName);
                    file_Api::instance()->uploadBigFileAttachment($sModular, $iID, $oFile);
                }
                $aResult['success'] = true;
                $aResult['message'] = '操作成功';
                echo json::instance()->enCode($aResult);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            exit;
        }


        /**
         * [评审申报材料列表操作]
         * @author  [wj]
         * @date    [2017-7-12 15:11:49]
         * @version [1.0.0]
         * @param   string $sTableName   表名称
         * @return  json   是否成功
         */
        public function ajaxUserDeclareMultipleList() {
            $iUID = safe::instance()->getSafeValue(http::REQUEST('user_id', NULL));
            if (!empty($iUID)) {
                $this->iUserID = $iUID;
            } else {
                $this->iUserID = user_Api::instance()->getCurrentUserID();
            }
            $sTableName = safe::instance()->getSafeValue(http::REQUEST('tpl', NULL));
            // 参数初始化
            $aSearch = $aResult = array();
            // 页码
            $iPageNow = intval(http::REQUEST('page', 1));
            // *easyUI 每页多少条记录
            $iRows = intval(http::REQUEST('rows', review_Object::instance()->iMgrPageSize));
            // 外键
            $iID = safe::instance()->getSafeValue(http::GET('id', NULL));
            // 求教师信息外键
            $aSearch['review_declare_id'] = $this->business->getReviewDeclareIDByID($iID, $this->iUserID);
            // 特殊类型需增加申报材料id查询
            if ($sTableName == 'az') {
                $aSearch['material_id'] = safe::instance()->getSafeValue(http::GET('mid', NULL));
            }
            $oResult = $this->business->getReviewDeclareTableListPage($sTableName, $iPageNow, $iRows, $aSearch);
            foreach ($oResult['aList'] as $key => $value) {
                $oResult['aList'][$key]['tpl'] = $sTableName;
            }
            $aResult['rows'] = $oResult['aList'];
            $aResult['total'] = $oResult['iTotal'];
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * [评审申报材料查看操作]
         * @author  [wj]
         * @date    [2017-7-11 09:13:24]
         * @version [1.0.0]
         * @param   int    $iID    数据id
         * @param   string $sTableName   表名称
         * @return  json   是否成功
         */
        public function ajaxUserDeclareMultipleSee() {
            $aProject = $aResult = $aError = array();
            $iID = safe::instance()->getSafeValue(http::GET('id', NULL));
            $sTableName = safe::instance()->getSafeValue(http::GET('tpl', NULL));
            $aDeclare = $this->business->getReviewDeclareTableByID($sTableName, $iID);
            $aResult['aDeclare'] = $aDeclare;
            $sModular = 'declare'.strtoupper($sTableName);
            $aFileList = file_Api::instance()->getUploadFileAttachment($sModular, $aDeclare['id']);
            $aResult['aFileList'] = $aFileList;
            header("Content-type: application/json");
            echo json::instance()->enCode($aResult);
            exit;
        }


        /**
         * [评审申报材料删除操作]
         * @author  [wj]
         * @date    [2017-7-11 09:13:24]
         * @version [1.0.0]
         * @param   int    $iID    数据id
         * @param   string $sTableName   表名称
         * @return  json   是否成功
         */
        public function ajaxRemoveUserDeclareMultiple() {
            $this->iUserID = user_Api::instance()->getCurrentUserID();
            $aResult = array();
            $aResult['status'] = false;
            $bCompetence = false;
            $bCompetence = true;
            if ($bCompetence) {
                $iID = intval(http::instance()->POST('id', NULL));
                $sTableName = safe::instance()->getSafeValue(http::GET('tpl', NULL));
                if (!empty($iID) && is_numeric($iID) && !empty($sTableName)) {
                    $aResult['result'] = $this->business->deleteReviewDeclareTableByID($sTableName, $iID);
                    $aResult['success'] = true;
                    $aResult['message'] = '删除信息:ID=' . $iID . '成功';
                    manager_Api::instance()->addLog($this->iUserID, 'review', 'ajaxRemoveUserDeclareMultiple', 1, '移除信息:id=' . $iID);
                } else {
                    $aResult['success'] = false;
                    $aResult['message'] = '删除信息:ID=' . $iID . '失败';
                    manager_Api::instance()->addLog($this->iUserID, 'review', 'ajaxRemoveUserDeclareMultiple', 1, '移除信息:id=' . $iID);
                }
            } else {
                $aResult['bCompetence'] = false;
                $aResult['message'] = "您无删除权限!";
            }
            header("Content-type: application/json");
            echo iconv('utf-8', 'utf-8', json::instance()->enCode($aResult));
            exit;
        }
}
?>
