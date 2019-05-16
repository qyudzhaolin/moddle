<?php
defined('MOODLE_INTERNAL') || die();
 global $CFG;

class mod_news_testcase extends advanced_testcase {
    public function test_plugin_settings() {
        global $DB;
        $news_config_plugins= $DB->get_recordset_sql("select * from mdl_config_plugins WHERE PLUGIN='news' ");

        foreach($news_config_plugins as $news_config_plugin)
        {
            if($news_config_plugin->name=="checkproductionmode")
            {
                $checkproductionmode=true;
            }
            if($news_config_plugin->name=="cost")
            {
                $cost=true;
            }
            if($news_config_plugin->name=="currency")
            {
                $currency=true;
            }
            if($news_config_plugin->name=="emailcontent")
            {
                $emailcontent=true;
            }
            if($news_config_plugin->name=="loginid")
            {
                $loginid=true;
            }
            if($news_config_plugin->name=="mailadmins")
            {
                $mailadmins=true;
            }
            if($news_config_plugin->name=="mailstudents")
            {
                $mailstudents=true;
            }
            if($news_config_plugin->name=="mailteachers")
            {
                $mailteachers=true;
            }
            if($news_config_plugin->name=="merchantmd5hash")
            {
                $merchantmd5hash=true;
            }
            if($news_config_plugin->name=="roleid")
            {
                $roleid=true;
            }
            if($news_config_plugin->name=="transactionkey")
            {
                $transactionkey=true;
            }
        }
        $is_setting= $checkproductionmode && $cost &&$currency &&$emailcontent && $loginid && $mailadmins && $mailstudents && $mailteachers && $merchantmd5hash && $roleid && $transactionkey;
        $this->assertEquals($is_setting, true);
    }
}


?>