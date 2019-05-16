<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Internal library of functions for module news
 *
 * All the news specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
/**
 * @package    news
 * @copyright  SunNet Solutions 2019
 * @license
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/mod/news/model.php');

//支付处理
class news{
    private $pay_url = "https://stageccp.dev.cdc.nicusa.com/CommonCheckout/CCPWebService/ServiceWeb.wsdl";   //支付的webservice地址
    private $reutrn_url = "https://stageccp.dev.cdc.nicusa.com/CommonCheckout/CCPWebService/ServiceWeb.wsdl";   //支付返回地址
    private $MERCHANTID = '744TXAGYCLI';                        //账号ID
    private $MERCHANTKEY = 'gfp9W@4vA2WL3&p3u*li';              //账号密码
    private $SERVICECODE = '14744008';                          //服务code

    public function __construct($_pay_url,$_reutrn_url,$_MERCHANTID,$_MERCHANTKEY,$_SERVICECODE)
    {
        $this->pay_url= $_pay_url;
        $this->reutrn_url = $_reutrn_url;
        $this->MERCHANTID = $_MERCHANTID;
        $this->MERCHANTKEY = $_MERCHANTKEY;
        $this->SERVICECODE = $_SERVICECODE;
    }
    //获取支付Token信息
    public function getPayToken($_cost,$_USER)
    {
        $request = new \mod_news\model\newsInfo();
        $field   = new \mod_news\model\FIELD2();

        $arr_filed        = new \mod_news\model\ArrayOfFIELD();
        $arr_filed->FIELD = $field;

        $field->FIELDNAME         = "CONV_FEE";
        $field->FIELDVALUE        = "0";
        $request->ORDERATTRIBUTES = $arr_filed;

        $request->STATECD       = "TX";
        $request->NAME          = $_userfullname;            //用户名
        $request->COMPANYNAME   = "cliengage";
        $request->STATE         = "TX";
        $request->CID           = "";
        $request->PAYTYPE       = "CC";
        $request->UNIQUETRANSID = uniqid();

        $request->DESCRIPTION = "";
        $request->LOCALREFID  = "-158937971";
        $request->MERCHANTID  =  $this->MERCHANTID;
        $request->MERCHANTKEY =  $this->MERCHANTKEY;
        $request->SERVICECODE =  $this->SERVICECODE;

        $request->COUNTRY = $_USER->country;
        $request->CITY    = $_USER->city;
        $request->AMOUNT  = $_cost;
        $request->EMAIL   = $_USER->email;
        $request->EMAIL1  = "";
        $request->EMAIL2  = "";
        $request->EMAIL3  = "";
        $request->PHONE   = $_USER->phone1;
        $request->FAX     = "";
        $request->ADDRESS1 = $USER->address;

        $request->HREFSUCCESS   = $_return;
        $request->HREFFAILURE   = $_return;
        $request->HREFDUPLICATE = $_return;
        $request->HREFCANCEL    = $_return;

        $li = new \mod_news\model\LINEITEM();
        $li->SKU = $_coursefullname;
        $li->DESCRIPTION = $_courseshortname.':'.$_instancename;
        $li->ITEM_ID = 1;
        $li->QUANTITY = 1;
        $li->UNIT_PRICE = $_cost;
        $li->ATTRIBUTES = $arr_filed;

        $arr_lineitem =new \mod_news\model\ArrayOfLINEITEM();
        $arr_lineitem->LINEITEM = array($li);
        $request->LINEITEMS = $arr_lineitem;
        $p = new \mod_news\model\Preparenews();
        $p->request = $request;

        $soap= new SoapClient($this->pay_url);
        $result = $soap->__Call('Preparenews',array('parameters'=>$p));
        return $result;
    }
}