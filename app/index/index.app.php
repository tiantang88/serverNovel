<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
class indexApp {
	public $methods	= array('iCMS','andVersion');
    public function __construct() {}
    public function do_iCMS($a = null) {
        if(iView::$gateway!="html"){
            $domain = $this->domain();
            if($domain) return;
        }

        return $this->index($a);
    }
    public function API_iCMS(){

        return $this->do_iCMS();
    }
    private function index($a = null){
        $page  = (int)($_GET['page']??1);
        $pageSize = 10;
        $vars = [
//            'loop'=>10,
//            'page'=>1,
            'offset'=>($page-1)*$pageSize,
            'tags'=>2,
            'sub'=>'all',
            'row'=>$pageSize,
            'app'=>'article',
            'method'=>'list',
            ];

            $newList = array();
           $a =  articleFunc::article_list($vars);
           if (is_array($a) && !empty($a)){

            foreach ($a as $keys=>$values) {

               $newList[] = [
                'id' =>$values['id'],
                'description' =>$values['description'],
               ]; 
            }

            return iUI::json_code(0,'',$newList);

           } else {

            return iUI::json_code(1,'',$newList);

           }
          

        $view = iView::render($index_tpl,'index');



        if($view) return array($view,$iurl);
    }
    public function domain(){
        $domain = iCMS::$config['category']['domain'];
        if($domain){
            $host = iSecurity::escapeStr($_GET['host']);
            empty($host) && $host = iPHP_REQUEST_HOST;
            $cid = $domain[$host];
            if(empty($cid) && iPHP_REQUEST_SCHEME=='http'){
                $host = str_replace('http://', '', $host);//兼容无协义域名
                $cid = $domain[$host];
            }
            if($cid){
                categoryApp::category($cid);
                return true;
            }
        }
        return false;
    }


    /**
     * android 版本更新  最新版本
     *
     * 返回
     * 1、force 是否强制
     * 2、url 下载地址
     * 3、note 更新日志
     * 4、apk大小
     */
    public function do_andVersion($version, $os)
    {

        $params['os']   =   $os;
        $params['version']  =   $version;


        $currentVersionModel = appversion::getCurrentVersion($params['os'], $params['version']);
        $lastVersionModel = AppVersion::getLastVersion($params['os']);


        if (!$currentVersionModel && $lastVersionModel) {
            return $this->respond([
                'force' => 'NO',
                'download_uri' => $lastVersionModel->download_uri,//$lastVersionModel->download_uri ? $this->_parseDownloadUri($params['version']):'',
                'note'  => $lastVersionModel->note,
                'new_version' => 'NO'
            ]);
        }

        if (!$lastVersionModel) {
            return $this->failed(ApiStatus::CODE_20005, '不存在任何版本');
        }


        if ($currentVersionModel->version < $lastVersionModel->version) {
            $data['download_uri'] =  $lastVersionModel->download_uri;// ? $this->_parseDownloadUri($params['version']):'';//$lastVersionModel->download_uri;
            $data['force'] = $currentVersionModel->isForce();
            $data['note'] = $lastVersionModel->note;
            $data['new_version'] = 'YES';
        } else {
            $data['download_uri'] = '';
            $data['note'] = '';
            $data['force'] = 'NO';
            $data['new_version'] = 'NO';
        }

        return $this->respond($data);
    }


    public function API_andVersion(){

        $version = iSecurity::escapeStr($_POST['version']);
        $os = iSecurity::escapeStr($_POST['os']);
        if (empty($version) || empty($os)) {

            return iUI::json_code(-1,'参数有误');
        }

        return $this->do_andVersion($version, $os);
    }
}
