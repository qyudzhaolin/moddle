<?php
namespace mod_news\model;
class Preparenews
{
    public $request;
}

class newsInfo
{
    public $STATECD;
    public $NAME;
    public $COMPANYNAME;
    public $STATE;
    public $CID ;
    public $PAYTYPE;
    public $UNIQUETRANSID;
    
    public $DESCRIPTION ;
    public $LOCALREFID;
    public $MERCHANTID;
    public $MERCHANTKEY;
    public $SERVICECODE;
    public $AMOUNT;

    public $COUNTRY;
    public $CITY;
    public $EMAIL;
    public $EMAIL1;
    public $EMAIL2;
    public $EMAIL3;
    public $PHONE;
    public $FAX;
    public $ADDRESS1;
    
    public $HREFSUCCESS;
    public $HREFFAILURE;
    public $HREFDUPLICATE;
    public $HREFCANCEL;

    public $ORDERATTRIBUTES;
    public $LINEITEMS;
}

class ArrayOfFIELD
{
    public $FIELD;
}

class FIELD2
{
    public $FIELDNAME;
    public $FIELDVALUE;
}

class LINEITEM
{
    public $ITEM_ID;
    public $SKU;
    public $DESCRIPTION;
    public $UNIT_PRICE;
    public $QUANTITY;
    public $ATTRIBUTES;
}

class ArrayOfLINEITEM
{
    public $LINEITEM;
}

class Querynews
{
    public $token;
}
?>