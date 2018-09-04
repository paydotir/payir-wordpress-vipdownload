<?php

function all_currencies(){
	$currencies = array(
		'IRR' => array('Iranian Rial', '&#65020;'),
		'IRT' => array('Iranian Toman', 'تومان'),
		'USD' => array('United States Dollar', '$'),
		'AUD' => array('Australian Dollar', 'AUD$'),
		'BRL' => array('Brazilian Real', 'R$'),
		'GBP' => array('British Pound', '&pound;'),
		'CAD' => array('Canadian Dollar', 'CAD$'),
		'CNY' => array('Chinese Yuan', '&#20803;'),
		'DKK' => array('Danish Krone', 'kr.'),
		'EUR' => array('European Euro', '&#8364;'),
		'HKD' => array('Hong Kong Dollar', 'HK$'),
		'HUF' => array('Hungarian Forint', 'Ft'),
		'INR' => array('Indian Rupee', 'INR'),
		'IDR' => array('Indonesian Rupiah', 'Rp'),
		'JPY' => array('Japanese Yen', '&yen;'),
		'MXN' => array('Mexican Peso', 'MEX$'),
		'NZD' => array('New Zealand Dollar', 'NZ$'),
		'NOK' => array('Norwegian Kroner', 'kr'),
		'PLN' => array('Polish Zloty', 'zl.'),
		'RUB' => array('Russian Ruble', 'RUB'),
		'SAR' => array('Saudi Riyal', 'SR'),
		'SGD' => array('Singapore Dollar', 'SGD$'),
		'ZAR' => array('South African Rand', 'R'),
		'SEK' => array('Swedish Krona', 'kr'),
		'CHF' => array('Swiss Franc', 'CHF'),
		'THB' => array('Thai Bhat', '&#3647;'),
		'TRY' => array('Turkish Lira', 'TRY'),
		'TWD' => array('Taiwan Dollar', 'TWD')
	);
	return $currencies;
}

function get_symbol_of_currency($c)
{
	$currencies = all_currencies();
	return $currencies[$c][1];
}

function get_exp_day($Time_Sec,$Bool=false)
{
	date_default_timezone_set("Asia/Tehran");
	$x=ceil((strtotime($Time_Sec)-strtotime(date("Y-m-d")))/60/60/24);
	if($x<0)
	{
		if($Bool==true)
			return true;
		return 'منقضی شده';
	}
	else if($x==0)
	{
		if($Bool==true)
			return true;
		return 'امروز تمام میشود';
	}
	else
	{
		if($Bool==true)
			return false;
		return $x.' روز';
	}
	
}

function get_ExtantDownload($Extant, $Exp_Per_Day)
{
	$per_day=intval($Exp_Per_Day);
	$Extant=intval($Extant);
	if($per_day==-1)
	{
		return 'نامحدود';
	}
	else
	{
		if($Extant==0)
		{
			return 'پایان';
		}
		else
		{
			return $Extant;
		}
	}
}

function CreateFormBuyVIP1($str,$ShowDescript){
	global $wpdb;
	$SiteURL=get_option("siteurl");
	$table_name = $wpdb->prefix . "vip_accounts";
	$accounts = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id desc" ,ARRAY_A);
	
	if (count($accounts) == 0)
	{
		$str .= '<b>اشتراک های VIP هنوز از سوی مدیریت تعریف نشده اند</b>';	
	}
	else
	{
		$tmp1='';
		$tmp2='<div style="text-align:right;">';
		$tmp1 .= '<form method="get" action="'.$SiteURL.'/">';
		$tmp1 .= 'اشتراک خود را انتخاب کنید <br><select name="buy_account_vip">';

		
		foreach($accounts as $item)
		{
			
			$tmp2.='<b>'.$item['name'].'<b> : '.$item['descript'].'<br>';
			
			$tmp1 .= '<option value="'.$item['id'].'">'.$item['name'].' '.intval($item['cost']).' '.get_symbol_of_currency($item['currency']).'  | دانلود روزانه : ';
			
			if(intval($item['per_day'])==-1)
				$tmp1.='نامحدود';
			else
				$tmp1.=$item['per_day'];
			
			$tmp1 .='</option>';
		}
		$tmp2 .='</div><hr>';
		$tmp1 .= '</select><br/><div style="text-align:center;">';
		$tmp1 .= '<input type="submit" class="mrbtn_green" value="خرید اشتراک ویژه"/></div>';
		$tmp1 .= '</form>';
		
		if($ShowDescript==true)
		{
			$str .=$tmp2;
		}
		$str.=$tmp1;
	}
	
	return $str;
}

// شورت کد برای استفاده در صفحه خرید اشتراک
function form_buy_vip_func( $atts ) {
	$args = shortcode_atts( array(
        'descript' => 'true',
    ), $atts );
	$ShowDescript=true;
	if($args['descript']=='false')
	{
		$ShowDescript=false;
	}
		
	date_default_timezone_set("Asia/Tehran");
	$SiteURL=get_option('siteurl');
	$Login_URL=wp_login_url();
	$_Date=date("Y-m-d");
	$html = '<link rel="stylesheet" media="all" type="text/css" href="'.plugins_url('style.css',__FILE__).'">';
	
	if(is_user_logged_in()){
		
		global $wpdb;
		$current_user=wp_get_current_user();
		$user_id=$current_user->ID;
		$Exp_Vip=get_user_meta($user_id,'exp_vip',true);
		
		if($Exp_Vip)
		{
			$Last_Vip_Name=get_user_meta($user_id,'last_vip_name',true);
			$Last_Buy_Vip=get_user_meta($user_id,'last_buy_vip',true);
			$Exp_Per_Day=get_user_meta($user_id,'exp_per_day',true);
			$Extant_Daily=get_user_meta($user_id,'extant_daily',true);
			
			$diff = strtotime($Exp_Vip) - strtotime($_Date);
			$diffday=$diff / (60*60*24);
			
						
			if($diffday>=0)
			{
				// اشتراک کاربر منقضی نشده
				$html .='<div class="mrbox">';
				
				$html .= '<b>نام اشتراک : '.$Last_Vip_Name.'</b><br>';
				$html .= '<b>مدت اعتبار : '.get_exp_day($Exp_Vip,false).'</b><br>';
				$_ShowExp_Per_Day='نامحدود';
				if(intval($Exp_Per_Day)>=0)
				{
					$_ShowExp_Per_Day=$Exp_Per_Day;
				}			
				$html .= '<b>تعداد دانلود روز : '.$_ShowExp_Per_Day.'</b><br>';
				
				$html .= '<b>الباقی دانلود روزانه : '.get_ExtantDownload($Extant_Daily, $Exp_Per_Day).'</b><br>';
				$html .= '</div>';
			}
			else
			{
				// منقضی شده
				$html .='<div class="mrbox">';
				$html .= '<b>اشتراک ویژه شما به پایان رسیده</b><br>
				شما می توانید اشتراک جدید بخرید<br>';
				$html=CreateFormBuyVIP1($html,$ShowDescript);
				$html .='</div>';
			}
		}
		else
		{	
			// Create Form Buy Vip
			$html .='<div class="mrbox">';
			$html=CreateFormBuyVIP1($html,$ShowDescript);
			$html .='</div>';
		}
	} 
	else 
	{
		$html .= '<div class="mrbox"><div style="text-align:center;">';
		$html .= '<a class="mrbtn_blue" href="'.$Login_URL.'">ابتدا باید وارد حساب کاربری خود شوید</a>';
		$html .= '</div></div>';
	}
	
	return $html;	
	
}
add_shortcode('form_buy_vip','form_buy_vip_func');

// شورت کد بخش ایجاد محتوای vip
function vip_data_func( $atts,$content = '') {
	$SiteURL=get_option('siteurl');
	$Login_URL=wp_login_url();
	$html = '<link rel="stylesheet" media="all" type="text/css" href="'.plugins_url('style.css',__FILE__).'">';

	if(is_user_logged_in())
	{
		global $wpdb;
		
		////////Edit
		$current_user=wp_get_current_user();
		$user_id=$current_user->ID;
		$Exp_Vip=get_user_meta($user_id,'exp_vip',true);
		
		if($Exp_Vip)
		{
			$x=get_exp_day($Exp_Vip,true);
			if($x)
			{
				// Message Expaire Vip
				$html .='<div class="mrbox">';
				$html .= '<b>اشتراک ویژه شما به پایان رسیده <br> شما اجازه مشاهده این بخش را ندارید</b><br>
				شما می توانید اشتراک جدید بخرید<br>';
				$html=CreateFormBuyVIP1($html,false);
				$html .='</div>';
			}
			else
			{
				// Show Content
				return $content;
			}
		}
		else
		{
			$html .='<div class="mrbox">';
			$html .='این بخش مخصوص اعضای vip سایت می باشد <br>';
			$html .='شما می توانید اشتراک VIP بخرید<br>';
			$html=CreateFormBuyVIP1($html,false);
			$html .='</div>';
		}
		
	} else {
		$html .='<div class="mrbox">';
		$html .='این بخش مخصوص اعضای vip سایت می باشد <br>';
		$html .='<a class="mrbtn_blue" href="'.$Login_URL.'">ابتدا باید وارد حساب کاربری خود شوید</a>';	
		$html .='<br> پس از ثبت نام و ورود به سایت اشتراک VIP بخرید ';
		$html .='</div>';
	}
	return $html;
}
add_shortcode('vip_data','vip_data_func');

// َشورت کد محصول فروشی و دانلود vip
function vip_linkdownload_func( $atts) {
	global $wpdb;
	$onlinebuy=true;
	$html = '<link rel="stylesheet"  media="all" type="text/css" href="'.plugins_url('style.css',__FILE__).'">';
	
	$SiteURL=get_option('siteurl');
	$Login_URL=wp_login_url();
	
	$args = shortcode_atts( array(
        'idproduct' => '0',
    ), $atts );	
	
	$current_user=wp_get_current_user();
	$user_id=$current_user->ID;
	
	$table_name = $wpdb->prefix . "pfd_products";
	$product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d",$args['idproduct']) , ARRAY_A, 0);
				
	if(is_user_logged_in()){		
		$Exp_Vip=get_user_meta($user_id,'exp_vip',true);
		$Exp_Per_Day=intval(get_user_meta($user_id,'exp_per_day',true));
		$Extant_Daily=intval(get_user_meta($user_id,'extant_daily',true));
			
		
		if($Exp_Vip)
		{
			$x=get_exp_day($Exp_Vip,true);
			if($x)
			{
				// Message Expaire Vip
				$html .='<div class="mrbox">';
				$html .= '<b>اشتراک ویژه شما به پایان رسیده <br> شما اجازه مشاهده این بخش را ندارید</b><br>
				شما می توانید اشتراک جدید بخرید<br>';
				$html=CreateFormBuyVIP1($html,false);
			}
			else
			{
				// Show LinkDownload Vip
				// 1.Link download product for vip user
				
				if(get_ExtantDownload($Extant_Daily, $Exp_Per_Day)=="پایان")
				{					
					$html .='<div class="mrbox">';
					$html .= '<b>تعداد دانلود روزانه شما به پایان رسیده</b><br>
					شما می توانید فردا مراجعه کنید<br>';
					// دکمه خرید تعداد دانلود روزانه (فشفشه ای ) بزودی
				}
				else
				{
				
					$html .='<div class="mrbox">';		
					$html .='نام محصول : '.$product['name'].'<hr>';
					$html .='قیمت : '.$product['cost'].' '.get_symbol_of_currency($product['currency']).'<hr>';
					$html .='<a class="mrbtn_purple" href="'.$SiteURL.'?vipdownload='.$product['id'].'"> دانلود کنید </a>';
					$onlinebuy=false;
					
				}
			}
		}
		else
		{
			$html .='<div class="mrbox">';
			$html .='این محصول ویژه اعضای VIP سایت می باشد<br>';
			$html .='شما می توانید اشتراک VIP بخرید<br>';
			$html=CreateFormBuyVIP1($html,false);			
		}
		
	} else {
		$html .='<div class="mrbox">';
		$html .='این محصول ویژه اعضای VIP سایت می باشد<br>';
		$html .='<a class="mrbtn_blue" href="'.$Login_URL.'">ابتدا باید وارد حساب کاربری خود شوید</a><br>';	
		$html .=' پس از ثبت نام و ورود به سایت اشتراک VIP بخرید <br>';
	}
	
	// 2.show button pay product
	if($onlinebuy)
	{
		$html .='<hr><b> همین الان محصول را با پرداخت آنلاین بخرید </b><br>';
		$html .='نام محصول : '.$product['name'].'<br>';
		$html .='قیمت : '.$product['cost'].' '.get_symbol_of_currency($product['currency']).'<br>';		
		$html .='<form name="frm_payline" action="'.$SiteURL.'" method="get">
			<input type="hidden" name="checkout" value="'.$product['id'].'">
			<input type="submit" name="submit" value="پرداخت آنلاین و دانلود" class="mrbtn_red" ></form>';
	}
	$html .='</div>';			
	return $html;
}

add_shortcode('vip_linkdownload','vip_linkdownload_func');
