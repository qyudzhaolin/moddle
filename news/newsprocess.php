<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$x_response_code = optional_param('x_response_code', '', PARAM_TEXT);
$x_response_reason_text = optional_param('x_response_reason_text', '', PARAM_TEXT);
$x_invoice_num = optional_param('x_invoice_num', '', PARAM_TEXT);
$x_MD5_Hash = optional_param('x_MD5_Hash', '', PARAM_TEXT);
$x_trans_id = optional_param('x_trans_id', '', PARAM_TEXT);
echo '  $x_response_code:'.$x_response_code;
echo '  $x_response_reason_text'.$x_response_reason_text;
echo '  $x_invoice_num:'.$x_invoice_num;
echo '  $x_MD5_Hash:'.$x_MD5_Hash;
echo '  $x_trans_id:'.$x_trans_id;

?>
<script type="text/javascript">
    function requisitaFuncao() {
        window.location = '<?php echo $CFG->wwwroot?>/mod/news/newsresult.php?x_response_code=<?php echo $x_response_code?>
    &x_response_reason_text=<?php echo $x_response_reason_text?>&x_invoice_num=<?php echo $x_invoice_num?>
    &x_MD5_Hash=<?php echo $x_MD5_Hash?>&x_trans_id=<?php echo $x_trans_id?>';
    }
    requisitaFuncao();
</script>