<?php
/**
 *
 * Author: wutiantang
 * Email :wutiantang@huishoubao.com.cn
 * Date: 2018/9/21 0021
 * Time: 下午 6:36
 */
class appversion {

    /**
     * 查询用户版本信息
     * Author: heaven
     * @param $os
     * @param $version
     * @return null
     */
    public static function getCurrentVersion($os, $version){
        $sql = "SELECT `version`,`download_uri` FROM `app_version` where `os`='{$os}' and `version`='{$version}'";
        return iDB::value($sql);
    }



    /**
     * 查询用户最新版本信息
     * Author: heaven
     * @param $os
     * @return null
     */
    public static function getLastVersion($os){
        $sql = "SELECT max (`version`),`version`,`download_uri` FROM `app_version` where `os`='{$os}'";
        return iDB::value($sql);
    }




}


