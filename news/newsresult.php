<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$str='Child Growth and Development [P1]; Responsive Interactions and Guidance [P2]; Learning Environments, Planning Framework, Curriculum, and Standards [P3]; Supporting Skill Development [P4] -  Language and Communication; Family and Community Relationships [P7]';
$ids = [];
$result = preg_match_all('/\[\s*([^\s\]]+)\s*\]/', $str, $matches);
if ($result === false) {
    throw new \coding_exception('preg_match_all failed');
} else if ($result > 0) {
    $ids = array_unique($matches[1]);
    $ids = array_map(function ($val) {
        return (string)$val;
    }, $ids);
}
echo implode(",", $ids);;


//$x_response_code = optional_param('x_response_code', '', PARAM_TEXT);
//$x_response_reason_text = optional_param('x_response_reason_text', '', PARAM_TEXT);
//$x_invoice_num = optional_param('x_invoice_num', '', PARAM_TEXT);
//$x_MD5_Hash = optional_param('x_MD5_Hash', '', PARAM_TEXT);
//$x_trans_id = optional_param('x_trans_id', '', PARAM_TEXT);

//$news_invoice  = $DB->get_record('news_invoice', array('invoice_num' => $x_invoice_num), '*', MUST_EXIST);
//$id = $news_invoice->course_moduleid;
//if ($id) {
//    $cm         = get_coursemodule_from_id('news', $id, 0, false, MUST_EXIST);
//    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
//    $news  = $DB->get_record('news', array('id' => $cm->instance), '*', MUST_EXIST);
//}

//require_login($course, true, $cm);

//if($x_response_code=="1")
//{
//    global  $USER;
//    // Update completion state
//    $completion=new completion_info($course);
//    $completion->update_state($cm,COMPLETION_COMPLETE);

//    //֧���ɹ�����Ҫ�޸�invoice״̬,֮����ת���γ�Viewҳ��
//    $news_invoice  = $DB->get_record('news_invoice', array('invoice_num' => $x_invoice_num), '*', MUST_EXIST);
//    $news_invoice->iscompletion=1;
//    $news_invoice_id = $DB->update_record('news_invoice', $news_invoice);
//    header('Location: '.$CFG->wwwroot.'/course/view.php?id='.$course->id);
//}
