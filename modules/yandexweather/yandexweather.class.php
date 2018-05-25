<?php
/**
* https://yandex.ru/pogoda/
* author Sannikov Dmitriy sannikovdi@yandex.ru
* support page 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 09:04:00 [Apr 04, 2016])
*/
//
//
class yandexweather extends module {
/**
*
* Module class constructor
*
* @access private
*/
function yandexweather() {
  $this->name="yandexweather";
  $this->title="Погода Яндекс";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
 function edit_classes(&$out, $id) {
  require(DIR_MODULES.$this->name.'/classes_edit.inc.php');
 }
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  global $today;
  global $forecast;
  global $type;	
  global $skin;		
global $sayweather;		
global $sayforecast;		
	
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
if (isset($today)) {
   $this->today=$today;
  }	
	
if (isset($type)) {
   $this->type=$type;
  }		
	
if (isset($skin)) {
   $this->skin=$skin;
  }		
	
	
if (isset($forecast)) {
   $this->forecast=$forecast;
  }		
	
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
// global $type;	
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
	
//$out['TODAY']=$this->today;	
//$out['FORECAST']=$this->forecast;		
$out['TYPE']=$this->type;			

	
//if (IsSet($this->skin)) {$out['SKIN']=$this->skin;}	
//else {$out['SKIN']=1;}
	
$out['SKIN']=1;
	
//$out['TYPE']=$type;				
//$out['TYPE']='FORECAST';
	
	
  $date = date("Y-m-d");
  $date = strtotime($date);
  $out['D1']=date('d/m', strtotime("+1 day", $date));
  $out['D2']=date('d/m', strtotime("+2 day", $date));
  $out['D3']=date('d/m', strtotime("+3 day", $date));
  $out['D4']=date('d/m', strtotime("+4 day", $date));
  $out['D5']=date('d/m', strtotime("+5 day", $date));
  $out['D6']=date('d/m', strtotime("+6 day", $date));
  $out['D7']=date('d/m', strtotime("+7 day", $date));	
	
	
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
//        if ((time() - gg('cycle_livegpstracksRun')) < $this->config['TLG_TIMEOUT']*2 ) {
        if ((time() - gg('cycle_yandexweatherRun')) < 360*2 ) {
			$out['CYCLERUN'] = 1;
		} else {
			$out['CYCLERUN'] = 0;
		}
 $out['DUUID'] = $this->config['DUUID'];
 $out['DEVICEID']=$this->config['DEVICEID'];
	
 $out['EVERY']=$this->config['EVERY'];
 
 if (!$out['UUID']) {
	 $out['UUID'] = md5(microtime() . rand(0, 9999));
	 $this->config['UUID'] = $out['UUID'];
	 $this->saveConfig();
 }
 
 if ($this->view_mode=='update_settings') {
	global $duuid;
	$this->config['DUUID']=$duuid;	 
	global $deviceid;
	$this->config['DEVICEID']=$deviceid;	 
   
   $this->saveConfig();
   $this->redirect("?");
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 
// if ($this->tab=='' || $this->tab=='outdata') {
//   $this->outdata_search($out);
// }  
 if ($this->tab=='' || $this->tab=='indata' || $this->tab=='widgets') {
$today = $this->today;		 
    $this->indata_search($out); 
 }
	
 if ($this->tab=='indataforecast') {
    $forecast = $this->forecast;		 
    $this->indata_search($out); 
 }
	
 if ($this->view_mode=='config_edit') {
   $this->config_edit($out, $this->id);
 }
 if ($this->view_mode=='config_check') {
echo "echeck";
   $this->config_check($this->id);
 }
 if ($this->view_mode=='config_uncheck') {
   $this->config_uncheck($this->id);
 }
if ($this->view_mode=='config_mycity') {
   $this->config_mycity($this->id);
 }
if ($this->view_mode=='indata_del') {
   $this->config_del($this->id);
 }
	
 if ($this->view_mode=='get') {
setGlobal('cycle_yandexweatherControl','start'); 
		$this->getdatefnc();
 }

        if ($this->view_mode=='sayweather')
        {
            $this->sayweather();
        }

        if ($this->view_mode=='sayforecast')
        {
            $this->sayforecast();
        }
	
	
//$today = $this->today;	
//$forecast = $this->forecast;		
//if ($map == "on") {$out["MULTI_ROUTES"]["map"]= gg('yt_settings.height');} else {$out["MULTI_ROUTES"]["map"]= "0";}		
	
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {

 $this->admin($out);
}
 
 function indata_search(&$out) {	 
  require(DIR_MODULES.$this->name.'/indata.inc.php');
  require(DIR_MODULES.$this->name.'/cfgdata.inc.php');
 }
 function processCycle() {
   $this->getConfig();
   $every=$this->config['EVERY'];
   $tdev = time()-$this->config['LATEST_UPDATE'];
   $has = $tdev>$every*60;
   if ($tdev < 0) {
		$has = true;
   }
   
   if ($has) {  
$this->getdatefnc();   
		 
	$this->config['LATEST_UPDATE']=time();
	$this->saveConfig();
   } 
  }
/**
* InData edit/add
*
* @access public
*/
 
 function config_edit(&$out, $id) {	
  require(DIR_MODULES.$this->name.'/config_edit.inc.php');
 } 
/**
* InData delete record
*
* @access public
*/
 function config_del($id) {
  // some action for related tables
  SQLExec("DELETE FROM yaweather_cities WHERE ID='".$id."'");
 }
/**
* InData delete record
*
* @access public
*/
 function config_check($id) {
  $rec=SQLSelectOne("SELECT * FROM yaweather_cities WHERE ID=".$id);
//echo "<br>". implode( $id);
   $rec['check']=1;
SQLUpdate('yaweather_cities',$rec); 
} 
/**
* InData delete record
*
* @access public
*/
 
 function config_uncheck($id) {
  $rec=SQLSelectOne("SELECT * FROM yaweather_cities WHERE ID=".$id);
   $rec['check']=0;
SQLUpdate('yaweather_cities',$rec); 
}
////////////////////////////////////////
////////////////////////////////////////
////////////////////////////////////////	
function sayweather() {
$text='Сегодня хорошая погода';
$return_full="";
$status="";
$condition='ясно';
	
if (gg('yw_mycity.condition')=='overcast') {$condition='ясно';}
if (gg('yw_mycity.condition')=='cloudy-and-light-rain') {$condition='облачно и легкий дождь';}
if (gg('yw_mycity.condition')=='cloudy-and-rain') {$condition='облачно с  дождем';}
if (gg('yw_mycity.condition')=='cloudy') {$condition='облачно';}
if (gg('yw_mycity.condition')=='overcast-and-light-rain') {$condition='легкий дождь';}
if (gg('yw_mycity.condition')=='overcast-and-light-snow') {$condition='небольшой снег';}
if (gg('yw_mycity.condition')=='partly-cloudy-and-light-rain') {$condition='переменная облачность и легкий дождь';}
if (gg('yw_mycity.condition')=='partly-cloudy-and-light-snow') {$condition='переменная облачность и небольшой снег';}
if (gg('yw_mycity.condition')=='partly-cloudy-and-rain') {$condition='переменная облачность с дождем';}
if (gg('yw_mycity.condition')=='partly-cloudy-and-snow') {$condition='переменная облачность со снегом';}
if (gg('yw_mycity.condition')=='partly-cloudy') {$condition='переменная облачность';}
	

	
$status.="Сейчас ".$condition.".";
$return_full.=$status." ";
    
$status="";
$w=round(gg("yw_mycity.temp"));
$tempw=$w;
if($tempw >= 11 and $tempw <= 14) {
  $tempcels="градусов";
} else {
  while ($tempw > 9) {
    $tempw=$tempw-10;
  }
    
  if($tempw == 0 or $tempw >= 5 and $tempw <= 9) { $tempcels= градусов ; }
  if($tempw == 1) { $tempcels= градус ; }
  if($tempw >= 2 and $tempw <= 4) { $tempcels= градуса ; }
}

$tNew = abs((float)gg('yw_mycity.temp'));
$status.='По данным метеослужб температура воздуха '.gg('yw_mycity.temp')." ".$tempcels." цельсия. ";
//Датчики на балконе показывают " . chti(round(gg("zaoknom")), 'градус', 'градуса', 'градусов')  . " цельсия." ;
$return_full.=$status." ";

$tempw="";
$tempcels="";
    
$status="";  
$h=round(gg("yw_mycity.humidity"));
$tempw=$h;
if($tempw >= 11 and $tempw <= 14){
  $tempcels="процентов";
} else {
  while ($tempw > 9){
    $tempw=$tempw-10;
  }
  if($tempw == 0 or $tempw >= 5 and $tempw <= 9) { $tempcels= процентов ; }
  if($tempw == 1) { $tempcels= процент ; }
  if($tempw >= 2 and $tempw <= 4) { $tempcels= процента ; }
}
$status.=" Относительная влажность ".gg("yw_mycity.humidity")." ".$tempcels. ".";
$return_full.=$status." ";

$tempw="";
$tempcels="";
    
$status="";
$pressure=round(gg("yw_mycity.pressure_mm"));
if ($pressure<728) {
  $status.=' Атмосферное давление пониженное';
} elseif ($pressure>768) {
  $status.=' Атмосферное давление повышенное.';
} else {
  $status.=' Атмосферное давление нормальное.';
}
$return_full.=$status." ";
    
$status="";
//ветер
$WindSpeed=(float)gg("yw_mycity.wind_speed");
if ($WindSpeed<1) {
  $status.='Ветра нет.';
} elseif ($WindSpeed<4) {
  $status.='Ветер слабый.';
} elseif ($WindSpeed<6) {
  $status.='Ветер сильный.';
} elseif ($WindSpeed<9) {
  $status.='Ветер очень сильный.';
} else {
  $status.='Ветер очень! Очень сильный.';
}
$return_full.=$status." ".round(gg("yw_mycity.wind_speed"))." метра в секунду. ";
say($return_full,2);

}
///////////////////////////////////////////
///////////////////////////////////////////
///////////////////////////////////////////
///////////////////////////////////////////	
function sayforecast() {
$text='Завтра ожидается хорошая погода';	
//$text=gettextforecast_long();
$condition='ясно';
	
if (gg('yw_mycity.condition')=='overcast') {$condition='ясно';}
if (gg('yw_mycity.condition')=='cloudy-and-light-rain') {$condition='облачно и легкий дождь';}
if (gg('yw_mycity.condition')=='cloudy-and-rain') {$condition='облачно с  дождем';}
if (gg('yw_mycity.condition')=='cloudy') {$condition='облачно';}
if (gg('yw_mycity.condition')=='overcast-and-light-rain') {$condition='легкий дождь';}
if (gg('yw_mycity.condition')=='overcast-and-light-snow') {$condition='небольшой снег';}
if (gg('yw_mycity.condition')=='partly-cloudy-and-light-rain') {$condition='переменная облачность и легкий дождь';}
if (gg('yw_mycity.condition')=='partly-cloudy-and-light-snow') {$condition='переменная облачность и небольшой снег';}
if (gg('yw_mycity.condition')=='partly-cloudy-and-rain') {$condition='переменная облачность с дождем';}
if (gg('yw_mycity.condition')=='partly-cloudy-and-snow') {$condition='переменная облачность со снегом';}
if (gg('yw_mycity.condition')=='partly-cloudy') {$condition='переменная облачность';}
	
$status .= "Сейчас на улице по данным метеослужб " .$condition . ", ";

$w = round(gg("yw_mycity.temp"));
//$status .= 'температура ' . chti($w, 'градус', 'градуса', 'градусов')  . " цельсия, ";
$status .= 'температура '.$w." градусов цельсия, ";

//if (gg('all_in_one.zaoknom_actual')=='1' && (gg('all_in_one.zaoknom2_actual')=="1"))
//{$realFellTemp = round(min(gg("all_in_one.zaoknomtemp"),gg("all_in_one.zaoknom2temp")));}
//else if 
//(gg('all_in_one.zaoknom_actual')=='1' && (gg('all_in_one.zaoknom2_actual')=="0"))
//{$realFellTemp = round(gg("all_in_one.zaoknomtemp"));} 
//else if 
//(gg('all_in_one.zaoknom_actual')=='0' && (gg('all_in_one.zaoknom2_actual')=="1"))
//{$realFellTemp = round(gg("all_in_one.zaoknom2temp"));} 
//else if 
//(gg('all_in_one.zaoknom_actual')=='0' && (gg('all_in_one.zaoknom2_actual')=="0"))
//{$realFellTemp = round(gg("ow_fact.realFell"));} 
//else {$realFellTemp = round(gg("ow_fact.realFell"));     }

//if ($w != $realFellTemp) {
//    $status .= "на датчиках на балконе  " . chti($realFellTemp, 'градус', 'градуса', 'градусов')  . " цельсия, ";
//}
//$w = gg("ow_fact.realFell");
$w = gg("yw_mycity.temp");
if ($w < -40) {
    $status .= 'мы морозов не боимся! ';
} elseif ($w < -30) { 
     $status .= 'одевайтесь теплее, очень холодно, ';
} elseif ($w < -20) {
    $status .= 'самое время есть мороженое, ';
} elseif ($w < -10) {
    $status .= 'холодновато, ';
} elseif ($w <- 3)  {
    $status .= 'не особо холодно, ';
} elseif ($w < 3) {
    $status .= 'значит, возможно, гололёд, ';
} elseif ($w < 10) {
    $status .= 'прохладно, ';
} elseif ($w < 22)  {
    $status .= 'тепло, ';
} elseif ($w < 30) {
    $status .= 'жарко, ';
} elseif ($w > 30) {
    $status .= 'ташкент, ';
} 




// Сравнение со вчерашним днем
$tNew = round((float) getGlobal('yw_mycity.temp'));
//$tOld = round((float) getHistoryAvg("tsrearyard.temp", strtotime("-1 day")) ('ow_fact.tempYesterday'));
$tOld = round((float) getHistoryAvg("yw_mycity.temp", strtotime("-1 day")));
$tDelta = abs($tNew - $tOld);

if ($tNew > $tOld) {
     //$status .= "теплее, чем вчера на " . chti($tDelta, 'градус', 'градуса', 'градусов') . ". ";
$status .= "теплее, чем вчера на " . $tDelta. ' градуса. ';     
} elseif ($tNew < $tOld) {
//     $status .= "холоднее, чем вчера на " . chti($tDelta, 'градус', 'градуса', 'градусов') . ". ";
     $status .= "холоднее, чем вчера на " . $tDelta . ' градуса. ';
} elseif ($tNew == $tOld) {
     $status .= "так же как и вчера. ";
}

$h = round(gg("yw_mycity.humidity"));

//$status .= "Относительная влажность " . chti($h, 'процент', 'процента', 'процентов') . ". ";
$status .= "Относительная влажность " . $h.' процентов.';	

$pressure = (float) gg("yw_mycity.pressure_mm");
if ($pressure < 738) {
    $status .= 'Атмосферное давление пониженное';
} elseif ($pressure > 768) {
    $status .='Атмосферное давление повышенное';
} else {
    $status .= 'Атмосферное давление в пределах нормы';
}
 //$status .= " (" . chti(round($pressure), 'миллиметр', 'миллиметра', 'миллиметров') . " ртутного столба). ";
$status .= " ". round($pressure). " (миллиметров ртутного столба). "; 

// ветер
$WindSpeed = (float) gg("yw_mycity.wind_speed");
if ($WindSpeed < 1) {
    $status .= "Ветра нет";
} elseif ($WindSpeed < 2) {
    $status .= "Легкий ветер, ";
} elseif ($WindSpeed < 5) {
    $status .= "Слабый ветер";
} elseif ($WindSpeed < 8) {
    $status .= "Умеренный ветер";
} elseif ($WindSpeed < 10) {
    $status .= "Свежий ветер";
} elseif ($WindSpeed < 14) {
    $status .= "Сильный ветер";
} elseif ($WindSpeed < 17) {
    $status .= "Очень сильный ветер";
} elseif ($WindSpeed < 21) {
    $status .= "Ветер очень-очень сильный";
} elseif ($WindSpeed < 28) {
    $status .= "Шторм";
} else {
    $status .= "Ураган";
}
if ($WindSpeed >= 1) {
//    $status .= " (" . chti(round($WindSpeed), 'метр', 'метра', 'метров') . " в секунду), ";
$status .= " ( " . round($WindSpeed) ." метра в секунду), ";
//    $windDirections = array('севера', 'северо-востока', 'востока', 'юго-востока', 'юга', 'юго-запада', 'запада', 'северо-запада', 'севера');
    
if (gg('yw_mycity.wind_dir')=='n') {$degree = 'севера';}
if (gg('yw_mycity.wind_dir')=='ne') {$degree = 'северо-востока';}     
if (gg('yw_mycity.wind_dir')=='e') {$degree = 'востока';}     
if (gg('yw_mycity.wind_dir')=='se') {$degree = 'юго-востока';}     
if (gg('yw_mycity.wind_dir')=='s') {$degree = 'юга';}     
if (gg('yw_mycity.wind_dir')=='sw') {$degree = 'юго-запада';}     
if (gg('yw_mycity.wind_dir')=='w') {$degree = 'запада';}     
if (gg('yw_mycity.wind_dir')=='nw') {$degree = 'северо-запада';}     



//     $WindDir = $windDirections[round($degree / 45)];
    $status .= " дующий с " . $degree;
}
$status .= ". ";
// Прогноз погоды на сегодня
if (timeBetween("01:00", "10:00")) {
    $status .= "Сегодня утром ожидается ";
    $w = round(gg("yw_mycity.forecast_0_morning_temp_avg"));
$condition='ясно';
if (gg('yw_mycity.forecast_0_morningcondition')=='overcast') {$condition='ясно';}
if (gg('yw_mycity.forecast_0_morningcondition')=='cloudy-and-light-rain') {$condition='облачно и легкий дождь';}
if (gg('yw_mycity.forecast_0_morningcondition')=='cloudy-and-rain') {$condition='облачно с  дождем';}
if (gg('yw_mycity.forecast_0_morningcondition')=='cloudy') {$condition='облачно';}
if (gg('yw_mycity.forecast_0_morningcondition')=='overcast-and-light-rain') {$condition='легкий дождь';}
if (gg('yw_mycity.forecast_0_morningcondition')=='overcast-and-light-snow') {$condition='небольшой снег';}
if (gg('yw_mycity.forecast_0_morningcondition')=='partly-cloudy-and-light-rain') {$condition='переменная облачность и легкий дождь';}
if (gg('yw_mycity.forecast_0_morningcondition')=='partly-cloudy-and-light-snow') {$condition='переменная облачность и небольшой снег';}
if (gg('yw_mycity.forecast_0_morningcondition')=='partly-cloudy-and-rain') {$condition='переменная облачность с дождем';}
if (gg('yw_mycity.forecast_0_morningcondition')=='partly-cloudy-and-snow') {$condition='переменная облачность со снегом';}
if (gg('yw_mycity.forecast_0_morningcondition')=='partly-cloudy') {$condition='переменная облачность';}        
} elseif (timeBetween("10:00", "14:00")) {
     $status .= "Сегодня днем ожидается ";
    $w = round(gg("yw_mycity.forecast_0_day_temp_avg"));
$condition='ясно';
if (gg('yw_mycity.forecast_0_daycondition')=='overcast') {$condition='ясно';}
if (gg('yw_mycity.forecast_0_daycondition')=='cloudy-and-light-rain') {$condition='облачно и легкий дождь';}
if (gg('yw_mycity.forecast_0_daycondition')=='cloudy-and-rain') {$condition='облачно с  дождем';}
if (gg('yw_mycity.forecast_0_daycondition')=='cloudy') {$condition='облачно';}
if (gg('yw_mycity.forecast_0_daycondition')=='overcast-and-light-rain') {$condition='легкий дождь';}
if (gg('yw_mycity.forecast_0_daycondition')=='overcast-and-light-snow') {$condition='небольшой снег';}
if (gg('yw_mycity.forecast_0_daycondition')=='partly-cloudy-and-light-rain') {$condition='переменная облачность и легкий дождь';}
if (gg('yw_mycity.forecast_0_daycondition')=='partly-cloudy-and-light-snow') {$condition='переменная облачность и небольшой снег';}
if (gg('yw_mycity.forecast_0_daycondition')=='partly-cloudy-and-rain') {$condition='переменная облачность с дождем';}
if (gg('yw_mycity.forecast_0_daycondition')=='partly-cloudy-and-snow') {$condition='переменная облачность со снегом';}
if (gg('yw_mycity.forecast_0_daycondition')=='partly-cloudy') {$condition='переменная облачность';}
    
} elseif (timeBetween("14:00", "20:00")) {
     $status .= "Сегодня вечером ожидается ";
    $w = round(gg("yw_mycity.forecast_0_evening_temp_avg"));
$condition='ясно';
if (gg('yw_mycity.forecast_0_eveningcondition')=='overcast') {$condition='ясно';}
if (gg('yw_mycity.forecast_0_eveningcondition')=='cloudy-and-light-rain') {$condition='облачно и легкий дождь';}
if (gg('yw_mycity.forecast_0_eveningcondition')=='cloudy-and-rain') {$condition='облачно с  дождем';}
if (gg('yw_mycity.forecast_0_eveningcondition')=='cloudy') {$condition='облачно';}
if (gg('yw_mycity.forecast_0_eveningcondition')=='overcast-and-light-rain') {$condition='легкий дождь';}
if (gg('yw_mycity.forecast_0_eveningcondition')=='overcast-and-light-snow') {$condition='небольшой снег';}
if (gg('yw_mycity.forecast_0_eveningcondition')=='partly-cloudy-and-light-rain') {$condition='переменная облачность и легкий дождь';}
if (gg('yw_mycity.forecast_0_eveningcondition')=='partly-cloudy-and-light-snow') {$condition='переменная облачность и небольшой снег';}
if (gg('yw_mycity.forecast_0_eveningcondition')=='partly-cloudy-and-rain') {$condition='переменная облачность с дождем';}
if (gg('yw_mycity.forecast_0_eveningcondition')=='partly-cloudy-and-snow') {$condition='переменная облачность со снегом';}
if (gg('yw_mycity.forecast_0_eveningcondition')=='partly-cloudy') {$condition='переменная облачность';}
    
} else {
     $status .= "Сегодня ночью ожидается ";
    $w = round(gg("yw_mycity.forecast_0_night_temp_avg")); 
$condition='ясно';
if (gg('yw_mycity.forecast_0_nightcondition')=='overcast') {$condition='ясно';}
if (gg('yw_mycity.forecast_0_nightcondition')=='cloudy-and-light-rain') {$condition='облачно и легкий дождь';}
if (gg('yw_mycity.forecast_0_nightcondition')=='cloudy-and-rain') {$condition='облачно с  дождем';}
if (gg('yw_mycity.forecast_0_nightcondition')=='cloudy') {$condition='облачно';}
if (gg('yw_mycity.forecast_0_nightcondition')=='overcast-and-light-rain') {$condition='легкий дождь';}
if (gg('yw_mycity.forecast_0_nightcondition')=='overcast-and-light-snow') {$condition='небольшой снег';}
if (gg('yw_mycity.forecast_0_nightcondition')=='partly-cloudy-and-light-rain') {$condition='переменная облачность и легкий дождь';}
if (gg('yw_mycity.forecast_0_nightcondition')=='partly-cloudy-and-light-snow') {$condition='переменная облачность и небольшой снег';}
if (gg('yw_mycity.forecast_0_nightcondition')=='partly-cloudy-and-rain') {$condition='переменная облачность с дождем';}
if (gg('yw_mycity.forecast_0_nightcondition')=='partly-cloudy-and-snow') {$condition='переменная облачность со снегом';}
if (gg('yw_mycity.forecast_0_nightcondition')=='partly-cloudy') {$condition='переменная облачность';}    
}



//$status .= chti($w, 'градус', 'градуса', 'градусов') . " цельсия, " . gg("ow_day0.weather_type") . ". ";
$status .= $w ." градусов цельсия, " . $condition . ". ";

// Погода на завтра
$w = round(gg("yw_mycity.forecast_1_day_temp_avg"));
//$status .= 'Завтра ожидается ' . chti($w, 'градус', 'градуса', 'градусов') . " цельсия, ";
$status .= 'Завтра ожидается ' . $w. "градусов цельсия, ";
$condition='ясно';
if (gg('yw_mycity.forecast_1_daycondition')=='overcast') {$condition='ясно';}
if (gg('yw_mycity.forecast_1_daycondition')=='cloudy-and-light-rain') {$condition='облачно и легкий дождь';}
if (gg('yw_mycity.forecast_1_daycondition')=='cloudy-and-rain') {$condition='облачно с дождем';}
if (gg('yw_mycity.forecast_1_daycondition')=='cloudy') {$condition='облачно';}
if (gg('yw_mycity.forecast_1_daycondition')=='overcast-and-light-rain') {$condition='легкий дождь';}
if (gg('yw_mycity.forecast_1_daycondition')=='overcast-and-light-snow') {$condition='небольшой снег';}
if (gg('yw_mycity.forecast_1_daycondition')=='partly-cloudy-and-light-rain') {$condition='переменная облачность и легкий дождь';}
if (gg('yw_mycity.forecast_1_daycondition')=='partly-cloudy-and-light-snow') {$condition='переменная облачность и небольшой снег';}
if (gg('yw_mycity.forecast_1_daycondition')=='partly-cloudy-and-rain') {$condition='переменная облачность с дождем';}
if (gg('yw_mycity.forecast_1_daycondition')=='partly-cloudy-and-snow') {$condition='переменная облачность со снегом';}
if (gg('yw_mycity.forecast_1_daycondition')=='partly-cloudy') {$condition='переменная облачность';}    

$status .= $condition . ".";	
	
say($status,2);
}

	
	
 function config_mycity($id) {
$rec=SQLSelectOne("update yaweather_cities set mycity=0");
SQLExec($rec);
	 
$rec=SQLSelectOne("update yaweather_cities set mycity=1 WHERE ID=".$id );
SQLExec($rec);
	 
} 	
	
 
 
///////////////////////////////////
function  getdatefnc(){
$this->getConfig();
$timestamp = time();
$token = md5('eternalsun'.$timestamp);
 
$uuid = "0b122ce93c77f68831839ca1d7cbf44a";
$deviceid = "3fb4aa04ac896f1b51dd48d643d9e76e";

	
	$properties=SQLSelect("SELECT * FROM `yaweather_cities` where `check`=1   ");
	
	
foreach ($properties as $did)
{

   
 
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: yandex-weather-android/4.2.1\n" .
               "X-Yandex-Weather-Client: YandexWeatherAndroid/4.2.1\n" .
               "X-Yandex-Weather-Device: os=null;os_version=21;manufacturer=chromium;model=App Runtime for Chrome Dev;device_id=$deviceid;uuid=$uuid;\n" .
               "X-Yandex-Weather-Token: $token\n" .
               "X-Yandex-Weather-Timestamp: $timestamp\n" .
               "X-Yandex-Weather-UUID: $uuid\n" .
               "X-Yandex-Weather-Device-ID: $deviceid\n" .
               "Accept-Encoding: gzip, deflate\n" .
               "Host: api.weather.yandex.ru\n" .
               "Connection: Keep-Alive"
  )
);
 
$context = stream_context_create($opts);
	
$cityid=$did[ID];
$latlon=$did[latlon];	
	
 //ID города узнаем тут: https://pogoda.yandex.ru/static/cities.xml
//region="11162" id="28440
//$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid=54&lang=ru', false, $context);
//$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid=53&lang=ru', false, $context);
$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid='.$cityid.'&lang=ru', false, $context);	
if (isset($cityid)) {$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?geoid='.$cityid.'&lang=ru', false, $context);}
if (isset($latlon)) {$file = file_get_contents('https://api.weather.yandex.ru/v1/forecast?'.$latlon.'&lang=ru', false, $context);}	
//$file = file_get_contents('https://api.weather.yandex.ru/v1/locations?lang=ru', false, $context);
 
header('Content-type: text/json');
//echo gzdecode($file);
$otvet=gzdecode($file);
$data=json_decode($otvet,true);
//$objn=$data[0]['id'];
$objn=$data[info][slug];
$src=$data[info];
//echo $objn;
addClassObject('YandexWeather',$objn);
sg( $objn.'.json',$otvet);
$src=$data[info];
sg( $objn.'.now',gg('sysdate').' '.gg('timenow')); 
	
foreach ($src as $key=> $value ) { 
if (is_array($value)) {
foreach ($value as $key2=> $value2 ) {sg( $objn.'.'.$key.'_'.$key2,$value2); }
}	
else	
{sg( $objn.'.'.$key,$value); }     

$src=$data[geo_object];
foreach ($src as $key=> $value ) {
if (is_array($value)) {
foreach ($value as $key2=> $value2 ) {sg( $objn.'.'.$key.'_'.$key2,$value2); }
}	
else	
{sg( $objn.'.'.$key,$value); }     
}	
	
	
$src=$data[fact];
	foreach ($src as $key=> $value ) { sg( $objn.'.'.$key,$value); }
	
	}     
	$fobjn= $objn;
	$src=$data[forecasts][0][parts];
		foreach ($data[forecasts] as $day=> $value ) { 
			foreach ($data[forecasts][$day][parts] as $key=> $value ) {    
				
				
				
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_temp_avg',$data[forecasts][$day][parts][$key][temp_avg]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_speed',$data[forecasts][$day][parts][$key][wind_speed]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_gust',$data[forecasts][$day][parts][$key][wind_gust]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_wind_dir',$data[forecasts][$day][parts][$key][wind_dir]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_pressure_mm',$data[forecasts][$day][parts][$key][pressure_mm]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_pressure_pa',$data[forecasts][$day][parts][$key][pressure_pa]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'_humidity',$data[forecasts][$day][parts][$key][humidity]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'condition',$data[forecasts][$day][parts][$key][condition]);
			sg( $fobjn.'.'."forecast_".$day."_".$key.'daytime',$data[forecasts][$day][parts][$key][daytime]); 
 			}
		}
	
	
//mycity	
	
$objmycity='yw_mycity';
addClassObject('YandexWeather',$objmycity);	
	
$mycity1=SQLSelectOne("SELECT ID FROM `yaweather_cities` where `mycity`=1 ");
$mycity=$mycity1[ID];	

sg($objmycity.'.cityID', $mycity);
	
if ($mycity==$cityid){
$objprops=get_props($fobjn);
foreach ($objprops as $value){ sg($objmycity.'.'.$value,gg($fobjn.".".$value));}	
}
}	
	
}
  
  
  
 
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS yaweather_cities');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
setGlobal('cycle_yandexweatherAutoRestart','1');	 	 
$classname='YandexWeather';
addClass($classname); 
	 

$ChangeCondition="	
if ($this->object_title=='yw_mycity')
{
$lastcondition=gg('yw_mycity.lastcondition');
$conditioneng=gg('yw_mycity.condition');
 if ($lastcondition<>$conditioneng){
if ($conditioneng=='overcast') {$condition='ясно';}
if ($conditioneng=='cloudy-and-light-rain') {$condition='облачно и легкий дождь';}
if ($conditioneng=='cloudy-and-rain') {$condition='облачно с  дождем';}
if ($conditioneng=='cloudy') {$condition='облачно';}
if ($conditioneng=='overcast-and-light-rain') {$condition='легкий дождь';}
if ($conditioneng=='overcast-and-light-snow') {$condition='небольшой снег';}
if ($conditioneng=='partly-cloudy-and-light-rain') {$condition='переменная облачность и легкий дождь';}
if ($conditioneng=='partly-cloudy-and-light-snow') {$condition='переменная облачность и небольшой снег';}
if ($conditioneng=='partly-cloudy-and-rain') {$condition='переменная облачность с дождем';}
if ($conditioneng=='partly-cloudy-and-snow') {$condition='переменная облачность со снегом';}
if ($conditioneng=='partly-cloudy') {$condition='переменная облачность';}
sg('yw_mycity.lastcondition',$conditioneng) ;
say(' На улице стало '.$condition,2);
}
}
";	
	
	 
addClassMethod($classname,'OnChange','SQLUpdate("objects", array("ID"=>$this->id, "DESCRIPTION"=>gg("sysdate")." ".gg("timenow"))); ');
addClassMethod($classname,'ChangeCondition',$ChangeCondition);



$prop_id=addClassProperty($classname, 'temp', 30);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Фактическая температура'; //   <-----------
SQLUpdate('properties',$property); }

$prop_id=addClassProperty($classname, 'wind_speed', 30);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Скорость ветра'; //   <-----------
SQLUpdate('properties',$property); } 
	
$prop_id=addClassProperty($classname, 'condition', 30);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['ONCHANGE']="ChangeCondition"; // 	       <-----------	       
$property['DESCRIPTION']='Состояние погоды'; //   <-----------

SQLUpdate('properties',$property); } 
	 
$prop_id=addClassProperty($classname, 'pressure_pa', 30);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']=' Нормальное давление для заданных координат, кПА'; //   <-----------
SQLUpdate('properties',$property); } 
$prop_id=addClassProperty($classname, 'pressure_mm', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Атмосферное давление, ммртст.'; //   <-----------
SQLUpdate('properties',$property); } 
$prop_id=addClassProperty($classname, 'now', 2);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['ONCHANGE']='OnChange'; //   <-----------
$property['DESCRIPTION']='Когда обновлена инфомация'; //   <-----------
SQLUpdate('properties',$property); } 
$prop_id=addClassProperty($classname, 'humidity', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Текущая влажность'; //   <-----------
SQLUpdate('properties',$property); } 
$prop_id=addClassProperty($classname, 'wind_dir', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Направление ветра'; //   <-----------
SQLUpdate('properties',$property); } 
$prop_id=addClassProperty($classname, 'phenom', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']=''; //   <-----------
SQLUpdate('properties',$property); } 
$prop_id=addClassProperty($classname, 'soil_temp', 10);
if ($prop_id) {$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='Ощущение погоды'; //   <-----------
SQLUpdate('properties',$property); } 
$prop_id=addClassProperty($classname, 'uv_index', 10);
if ($prop_id) {
$property=SQLSelectOne("SELECT * FROM properties WHERE ID=".$prop_id);
$property['DESCRIPTION']='УФ-индекс'; //   <-----------
SQLUpdate('properties',$property);} 

addClassObject('YandexWeather',$objmycity);	 	 
	 

	 
	 
  $data = <<<EOD
 yaweather_cities: country varchar(100) 
 yaweather_cities: cityname varchar(30) 
 yaweather_cities: part varchar(30) 
 yaweather_cities: ID int(30) unsigned NOT NULL 
 yaweather_cities: check int(30) 
 yaweather_cities: head int(30)
 yaweather_cities: type int(30) 
 yaweather_cities: region int(30) 
 yaweather_cities: mycity int(30) 
 yaweather_cities: latlon varchar(50) 
 
EOD;
  parent::dbInstall($data);
        $cmds = SQLSelectOne("SELECT * FROM yaweather_cities;");
        if(count($cmds) == 0) {
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Екатеринбург';
            $rec['part'] = 'Свердловская область';
            $rec['ID'] = 54;
            $rec['check'] = '1';
            $rec['head'] = '1';
            SQLInsert('yaweather_cities', $rec);
        $cmds = SQLSelectOne("SELECT * FROM yaweather_cities;"); 
     
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Москва';
            $rec['part'] = 'Московская область';
            $rec['ID'] = 213;
            $rec['check'] = '1';
            $rec['head'] = 0;
            $rec['type'] = '1';
            $rec['mycity'] = '1';				
		
            SQLInsert('yaweather_cities', $rec);
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Санкт-Петербург';
            $rec['part'] = 'Ленинградская область';
            $rec['ID'] = 2;
            $rec['check'] = '0';
            
            SQLInsert('yaweather_cities', $rec);
		
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Курган';
            $rec['part'] = 'Курганская область';
            $rec['ID'] = 53;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);
		
            $rec['country'] = 'Россия';
            $rec['cityname'] = 'Новосибирск';
            $rec['part'] = 'Новосибирская область';
            $rec['ID'] = 65;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);		
		   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Красноярск';
            $rec['part'] = 'Красноярский край';
            $rec['ID'] = 62;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
		
	   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Краснодарский край';
            $rec['part'] = 'Краснодар';
            $rec['ID'] = 35;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
		
$rec['country'] = 'Россия';
            $rec['cityname'] = 'Краснодарский край';
            $rec['part'] = 'Сочи';
            $rec['ID'] = 239;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);			
		
	   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Анапа';
            $rec['part'] = 'Краснодарский край';
            $rec['ID'] = 1107;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
		
		
			   $rec['country'] = 'Россия';
            $rec['cityname'] = 'Челябинск';
            $rec['part'] = '';
            $rec['ID'] = 56;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Пермь';
            $rec['part'] = '';
            $rec['ID'] = 50;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Уфа';
            $rec['part'] = '';
            $rec['ID'] = 172;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);	
		
	$rec['country'] = 'Россия';
            $rec['cityname'] = 'Казань';
            $rec['part'] = '';
            $rec['ID'] = 43;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Самара';
            $rec['part'] = '';
            $rec['ID'] = 51;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Волгоград';
            $rec['part'] = '';
            $rec['ID'] = 38;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Саратов';
            $rec['part'] = '';
            $rec['ID'] = 194;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Воронеж';
            $rec['part'] = '';
            $rec['ID'] = 193;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Тверь';
            $rec['part'] = '';
            $rec['ID'] = 14;
            $rec['check'] = '0';
            SQLInsert('yaweather_cities', $rec);	
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Вологда';
            $rec['part'] = '';
            $rec['ID'] = 21;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);	
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Ярославль';
            $rec['part'] = '';
            $rec['ID'] = 16;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);
		
	    $rec['country'] = 'Россия';
            $rec['cityname'] = 'Киров';
            $rec['part'] = '';
            $rec['ID'] = 46;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);	
	
	    $rec['country'] = 'Украина';
            $rec['cityname'] = 'Полтава';
            $rec['part'] = '';
            $rec['ID'] = 964;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);		
			    $rec['country'] = 'Белорусия';
            $rec['cityname'] = 'Минск';
            $rec['part'] = '';
            $rec['ID'] = 157;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);
		
    $rec['country'] = 'Украина';
            $rec['cityname'] = 'Запорожье';
            $rec['part'] = '';
            $rec['ID'] = 960;
            $rec['check'] = '0';
            $rec['latlon'] = '';								
            SQLInsert('yaweather_cities', $rec);		

 $rec['country'] = 'Россия';
            $rec['cityname'] = 'Спутник';
            $rec['part'] = 'Ростовская область';
            $rec['ID'] = 0;
            $rec['check'] = '0';
            $rec['latlon'] = 'lat=47.240585&lon=38.870989';		
		
            SQLInsert('yaweather_cities', $rec);		
		
        $rec['country'] = 'Россия';
            $rec['cityname'] = 'Ростов-на-Дону';
            $rec['part'] = 'Ростовская область';
            $rec['ID'] = 39;
            $rec['check'] = '0';
            $rec['latlon'] = '';				
            
            SQLInsert('yaweather_cities', $rec);		

$rec['country'] = 'Россия';
            $rec['cityname'] = 'Таганрог';
            $rec['part'] = 'Ростовская область';
            $rec['ID'] = 971;
            $rec['check'] = '0';
            $rec['latlon'] = '';						
            
            SQLInsert('yaweather_cities', $rec);				
		
		
 }}
// --------------------------------------------------------------------
//////
function getaddrfromcoord($x,$y)
{
$url='http://maps.googleapis.com/maps/api/geocode/xml?latlng='.$x.',' .$y.'&sensor=false&language=ru'; 
  $fields = array(
   	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
	'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.3',
	'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',	'Connection: keep-alive',	'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.76 Safari/537.36'     );
foreach($fields as $key=>$value)
{ $fields_string .= $key.'='.urlencode($value).'&'; }
rtrim($fields_string, '&');
   $ch = curl_init();   
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_POST, count($fields));   
   curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
   $result = curl_exec($ch);
 curl_close($ch);
$xml = simplexml_load_string($result);
$otvet=$xml->result->formatted_address; 
$spl=explode(',',$otvet) ;
return $spl[0] ;
//return $url;
} 
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgQXByIDA0LCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/

function get_props($obj)
{
//$sql='SELECT title FROM `properties`  where object_id = (SELECT id FROM `objects`  where title="'.$obj.'")';
$sql='SELECT substring(PROPERTY_NAME, POSITION("." in PROPERTY_NAME)+1) title FROM `pvalues` where PROPERTY_NAME like "'.$obj.'%"';
$rec = SQLSelect($sql); 
foreach ($rec as $prop)
{
 //print_r($prop)[title];
$ar2[] = $prop[title];
}
return $ar2;
}
