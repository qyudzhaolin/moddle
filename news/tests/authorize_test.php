<?php
defined('MOODLE_INTERNAL') || die();

class mod_news_testcase extends advanced_testcase {

    public function test_plugin_authorizedata() {
        global $DB,$CFG,$USER;

        $news_config_plugins= $DB->get_recordset_sql("select * from mdl_config_plugins WHERE PLUGIN='news' ");
        foreach($news_config_plugins as $news_config_plugin)
        {
            if($news_config_plugin->name=="loginid")
            {
                $loginid=$news_config_plugin->value;
            }
            if($news_config_plugin->name=="transactionkey")
            {
                $transactionkey=$news_config_plugin->value;
            }
            if($news_config_plugin->name=="currency")
            {
                $currency=$news_config_plugin->value;
            }
        }

        $amount = 1;
        $url = "https://test.authorize.net/gateway/transact.dll";
        $testmode = true;
        $invoice = date('YmdHis');
        $sequence = rand(1, 1000);
        $timestamp = time();

        if ( phpversion() >= '5.1.2' ) {
            $fingerprint = hash_hmac("md5", $loginid . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^", $transactionkey);
        } else {
            $fingerprint = bin2hex(mhash(MHASH_MD5
                           , $loginid . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^"
                           , $transactionkey));
        }

        $course = $this->getDataGenerator()->create_course([
             'fullname'=>'news_coursecompletion_test',
             'enablecompletion'=>1,
             'completion'=>1
             ]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_news');
        $news= $generator->create_instance(array('course'=>$course->id,'completionnews'=>1));
        $cm         = get_coursemodule_from_id('news', $news->cmid, 0, false, MUST_EXIST);
        $userid=1;
        $cust_id=$news->course.'-'.$userid.'-'.$cm->id;

        $data = ['x_login'=>$loginid,'x_amount'=>$amount,'x_cust_id'=>'','x_invoice_num'=>$invoice,
            'x_fp_sequence'=>$sequence,'x_fp_timestamp'=>$timestamp,'x_fp_hash'=>$fingerprint
            ,'x_test_request'=>$testmode,'x_relay_response'=>true];
        $headers = array('Content-Type: application/x-www-form-urlencoded');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);
        }
        curl_close($curl);

        $array=explode('id=', $result);
        if(count($array)>0)
        {
            $actual=true;
        }
        $expect=true;
        $this->assertEquals($actual,$expect);

        //$old_newscount=$DB->count_records('mod_news_result');

        //$news_result=new stdClass();
        //$news_result->item_name='test';
        //$news_result->courseid=3;
        //$news_result->userid=3;
        //$news_result->instanceid='test intro';
        //$news_result->amount='1';
        //$news_result->tax=time();
        //$news_result->duty=time();
        //$news_result->news_status=2;
        //$news_result->response_code=1;
        //$news_result->response_reason_code='USD';

        ////
        //$news_result->response_reason_text=1;
        //$news_result->auth_code=1;
        //$news_result->trans_id=1;
        //$news_result->method=1;
        //$news_result->account_number=1;
        //$news_result->card_type=1;
        //$news_result->invoice_num=1;
        //$news_result->test_request=1;
        ////
        //$news_result->first_name=1;
        //$news_result->last_name=1;
        //$news_result->company=1;
        //$news_result->phone=1;
        //$news_result->fax=1;
        //$news_result->email=1;
        //$news_result->address=1;
        //$news_result->city=1;
        //$news_result->state=1;
        //$news_result->zip=1;
        //$news_result->country=1;
        //$news_result->auth_json=1;
        //$news_result->timeupdated=time();

        //$DB->insert_record('mod_news_result',$news_result);

        //$new_newscount=$DB->count_records('mod_news_result');

        //$this->assertNotEquals($new_newsresultcount,$old_newsresultcount);
    }
}


?>