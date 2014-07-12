<?php
/* $Id$ */

$php_str_sql_options_array = array(
       "str_sql_date_format" => "Y-m-d",
       "str_sql_datetime_format" => "Y-m-d H:i:s",
       "str_sql_quote_func" => "mysql_real_escape_string");
/**
 * This method validates the sql insert string must always be used in conjunction with an insert to the db
 * @return string
 */

function kl_str_sql()
{
 GLOBAL $php_str_sql_options_array;
 $f = $php_str_sql_options_array['str_sql_quote_func'];
 
 $narg = func_num_args();
 $args = func_get_args();
 
 if ($narg<1) {
  trigger_error("At least one parameter required", E_USER_WARNING);
  return "";
 }
 
 $offset = 0;
 $flen = strlen($args[0]);
 $res = "";
 $narg = 1;
 
 while ($offset < $flen) {
  if (false !== ($pos = strpos($args[0],"!", $offset))) {
 
   $res .= substr($args[0], $offset, $pos-$offset);
 
   switch ($args[0][$pos+1]) {
 
   case 's':
    if (is_null($args[$narg])) {
     $res .= 'NULL';
    } else {
     $res .= "'".$f($args[$narg])."'";
    }
    $narg++;
    break;
 

   case 'b':
    if (is_null($args[$narg])) {
     $res .= 'NULL';
    } else {
     $res .= "'".$f($args[$narg])."'";
    }
    $narg++;
    break;
 
   case 'i':
    if (is_null($args[$narg])) {
     $res .= 'NULL';
    } else {
     $res .= (int)($args[$narg]);
    }
    $narg++;
    break;
 
   case 'f':
    if (is_null($args[$narg])) {
     $res .= 'NULL';
    } else {
     $res .= (double)($args[$narg]);
    }
    $narg++;
    break;
 
   case 'd':
    if (!($args[$narg])) {
     $res .= 'NULL';
    } else {
     $res .= "'".date($php_str_sql_options_array['str_sql_date_format'], $args[$narg])."'";
    }
    $narg++;
    break;
 
   case 't':
    if (!($args[$narg])) {
     $res .= 'NULL';
    } else {
     $res .= "'".date($php_str_sql_options_array['str_sql_datetime_format'], $args[$narg])."'";
    }
    $narg++;
    break;
  
 
   default:
    $res .= "!".$args[0][$pos+1];
   }
   $offset = $pos + 2;
  } else {
   $res .= substr($args[0], $offset);
   $offset = $flen;
  }
 }
 
 return $res;
 
}
 
/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
