<?php

	define('TIMEFRAME_INTERVAL',30);


function wssr_shared_form(&$form_state){
    wssr_set_admin_form_title(__FUNCTION__);
    return wssr_process_form('_'.__FUNCTION__, &$form_state);
}

function _wssr_shared_form(&$form_state){
    global $user;
    file_prefix('shared_');
    inherited('inherit_sr_shared');
    ws_get_tz_array();

    $params = wssr_get_field_params(wssr_load_field_params('wssr_shared_form'), 'wssr_shared_form');

    if (($form_state['storage']['page'] == 'review') || ($form_state['storage']['page'] == 'confirm'))
    {
        $form = _wssr_shared_form_summary($form_state);
        $form['is_summary'] = array('#type' => 'hidden', '#value' => 'true', '#disabled'=>true);
        return $form;
    }

    $originDefaults = wssr_get_origin_defaults($form_state);
    $originTree=ws_get_filtered_user_building_structure();
    if (is_ws_error())
    	return ws_get_ws_info_error_form(null, $form_state, 'shared',get_last_ws_error_description());
    
	if (empty($originTree) && $user->uid!=1 )
		return noaccess(null, $form_state, 'wssr_shared_form');

    //$originSimpleArr=wssr_full_tree_simple($originTree, get_user_space_first());
    //$originSimpleArr=wssr_full_tree_simple($originTree, $originDefaults['suiteId']);
    $treeDefault = $originDefaults['suiteId'] ? $originDefaults['suiteId'] : null;

    $originSimpleArr = wssr_full_tree_simple($originTree, $treeDefault);
    $sharedCatList = wssr_get_shared_categories_list();
    $shResources = array();
    foreach($originSimpleArr['simpleTree'] as $propId => $tmp){
        $shResources[$propId] = wssr_get_shared_resources_list($sharedCatList, $propId);
    }
    $originSimpleArr['simpleTree'] = ws_shared_resources_tree($originSimpleArr['simpleTree'], $shResources);
    $default = ws_get_tree_defaults($originSimpleArr['simpleTree'], $treeDefault);

    $defaultCat = $shResources[$default['property_id']][$default['suiteId']]['categoryId'];
    $category = $sharedCatList['categories'][$defaultCat]['value'];
    $default['address'] .= ' ('.$category.')';
    $originSimpleArr['default'] = $default;
    
    //ToDo: should be in two arrays! idShared-idCat, idCat - name!
    //  maybe, store Shared Resource data into the tree element?
    foreach($shResources as $key=>$val){
        foreach($val as $id=>$data){
            //$resCat[$id] = $sharedCatList['categories'][$data['categoryId']]['value'];
            $resData[$id] = array(
                'category' => $data['categoryId'],
                'reservationBlock' => $data['reservationBlock'],
            );
        }
    }
    //$originSimpleArr['resCat'] = $resCat;
    $originSimpleArr['resData'] = $resData;
    $originSimpleArr['catData'] = $sharedCatList['categories'];
    //print_r($originSimpleArr); die;
	//@karp - set access denied if space was not found
	//if ($originSimpleArr[] && $user->uid!=1 ) {
	//	return noaccess(null, $form_state);
	//}

	//@karp - set real defaults from $originSimpleArr
	//we can use it later for rendering !!!
	//it's better to use form values
	//@karp: ??? do we need it?
    //$form['origin'] = array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['property']);
	//^^

	$form['#attributes']=array('class'=>'sr_form sr_form_shared sr_form_creation');//, 'onsubmit'=>'return (validate_shared(this))');

	$propertyId = $originSimpleArr['default']['property_id'];

    $form['origin_property']=array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['property']);
	$form['origin_property_id']=array('#type'=> 'hidden', '#default_value'=>$propertyId);
    $form['origin_address']=array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['address']);
    $form['origin_suite']=array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['suiteId']);
	$form['originTreeStructure']=array('#type'=> 'value', '#value'=>$originSimpleArr);

    $requestList=getCurrentUserRequestListTree($propertyId,'shared');
    if (is_ws_error())
    	return ws_get_ws_info_error_form(null, $form_state, 'shared',get_last_ws_error_description());
    
	if (empty($requestList) && $user->uid!=1) {
		return noaccess(null, $form_state, 'wssr_shared_form');
	}

/*	drupal_add_js("
    	int__time_picker_minute_interval=".variable_get('int__time_picker_minute_interval','30')."
		",'inline');
*/

	$divisionsData = get_ws_user_divisions_list_options();
    if (is_ws_error())
    	return ws_get_ws_info_error_form(null, $form_state, 'shared',get_last_ws_error_description());
    
    $tmpDivisions = $divisionsData;
    $tmpDivisions = array_shift($tmpDivisions['options']);


    $form['division']=array('#type'=>'select','#title'=>$params['division']['#title'],'#options'=>$divisionsData['options'], '#access' => $divisionsData['options']?true:false);
    $form['cost_center']=array('#type'=>'textfield','#title'=>$params['cost_center']['#title'],'#size'=>'', '#default_value'=>$divisionsData['default_cost_center'],'#maxlength'=>100);
    $form['cost_center_array'] = array('#type' => 'hidden', '#value' => $divisionsData['cost_center_array'], '#access' => false);
    if(!$params['cost_center']['#access'])
        $form['cost_center']['#default_value'] = '';

    $form['h_separator_1']=array('#type'=>'markup','#value'=>'<div class="css__h_separator"></div>');

/*    $form['select_shared_resource']=array('#type'=>'select','#title'=>$params['select_shared_resource']['#title'],
    	'#options'=>array(''=>'Please select shared resource',strtotime("today")=>'Shared Resource #1 (today)','1315774800'=>'Shared Resource #2','1314824400'=>'Shared Resource #3','1312923600'=>"Shared Resource #3 (past)"),
    	'#attributes'=>array('onchange'=>'InitTimeframeControl(this.value)'),
    	'#default_value'=>($form_state['post']['select_shared_resource']?$form_state['post']['select_shared_resource']:($form_state['storage']['values']['select_shared_resource']?$form_state['storage']['values']['select_shared_resource']:'')));

	$form['h_separator_2']=array('#type'=>'markup','#value'=>'<div class="css__h_separator"></div>');
*/
    /* Default title = Reason for reservation */
    $form['brief_description']=array('#type'=>'textarea','#title'=>$params['brief_description']['#title'],'#required'=>true,'#maxlength'=>2000);

//    $form['h_separator_3']=array('#type'=>'markup','#value'=>'<div class="css__h_separator"></div>');

    //$form['start_date']=array('#type'=>'textfield','#title'=>$params['start_date']['#title'],'#size'=>'','#required'=>true, '#attributes'=>array('class'=>'date-pick'));
    //ToDo: could be changed on sliding for restore on Back from review page.
//    $form['start_date']=array('#type' => 'hidden', '#default_value' => strtotime("today"));
	$form['review']=array('#type'=>'submit','#value'=>'Submit');

    $form["pager"] = array("#value"=>"<div class='clear'></div><div id='timeframes-pager'><p class='left' onclick='slide_timeframes(\"backward\")'>&larr; prev </p><p class='right' onclick='slide_timeframes(\"forward\")'> next &rarr;</p></div>");
    
    $form['timeframes'] = array('#type' => 'hidden');
	if ($form_state['post']['timeframes'])
    	$array__selected_timeframe_list=ws_parse_timeframes($form_state['post']['timeframes']);
    else if ($form_state['values']['timeframes'])
    	$array__selected_timeframe_list=ws_parse_timeframes($form_state['values']['timeframes']);
    else if ($form_state['storage']['values']['timeframes'])
    	$array__selected_timeframe_list=ws_parse_timeframes($form_state['storage']['values']['timeframes']);
    
    if ($form_state['storage']['values']['timeframe_reservation_conflict_list'])
    {
    	$array__timeframe_reservation_conflict_list=ws_parse_timeframes($form_state['storage']['values']['timeframe_reservation_conflict_list']);
//    	$form_state['storage']['values']['timeframe_reservation_conflict_list']='';
//    	print "storage";
    }
/*   	else if ($form_state['post']['timeframe_reservation_conflict_list'])
   	{
   		$array__timeframe_reservation_conflict_list=ws_parse_timeframes($form_state['post']['timeframe_reservation_conflict_list']);
   		print "post";
   	}
*/
   	else if ($form_state['values']['timeframe_reservation_conflict_list'])
   		$array__timeframe_reservation_conflict_list=ws_parse_timeframes($form_state['values']['timeframe_reservation_conflict_list']);

    if ($array__timeframe_reservation_conflict_list)
    {
    	$form['timeframe_reservation_conflict_list']=array('#type' => 'hidden', '#default_value' => EncodeSelectedTimeframes($array__timeframe_reservation_conflict_list));
    	$array__selected_timeframe_list=FilterSelectedTimeframes($array__selected_timeframe_list,$array__timeframe_reservation_conflict_list);
    	
    	$form_state['storage']['values']['timeframes']=EncodeSelectedTimeframes($array__selected_timeframe_list);
    }
    
    if (count($array__timeframe_reservation_conflict_list)>0)
    {
		$array__timeframe_reservation_conflict_list=GenerateTimeframesResultList($array__timeframe_reservation_conflict_list,false);
		if ($form_state['values']['timeframe_reservation_conflict_error_flag']===true)
			$string__error_class_modificator='css__form_field_error';
		
		$form['reservation_conflict']=array('#type'=>'markup','#value'=>'<div class="clear"></div><div id="id_div__schedule_reservation_conflict" class="css_div__schedule_summary_preview css_div__schedule_summary_conflict '.$string__error_class_modificator.'"
    		><div id="id_div__schedule_reservation_conflict_container">'.WSSR_RenderReviewTableGrid("Reservation conflicts",array("Date","Reservation Conflict Time"),$array__timeframe_reservation_conflict_list)
				.'</div><div style="align:right"><a href="javascript:ClearReservationConflicts()"><span>Clear conflicts</span></a></div></div>');
    }

    $string__schedule_summary_visiblity_modificator='style="display:none"';
    if (count($array__selected_timeframe_list)>0)
    {
    	$string__schedule_summary_visiblity_modificator='';
		$array__selected_timeframe_list=GenerateTimeframesResultList($array__selected_timeframe_list);
    }
    $form['schedule_summary']=array('#type'=>'markup','#value'=>'<div class="clear"></div><div id="id_div__schedule_summary_preview" class="css_div__schedule_summary_preview" '.$string__schedule_summary_visiblity_modificator.' 
    	><div id="id_div__schedule_summary_preview_container">'.WSSR_RenderReviewTableGrid("Selected timeframes",array("Date","Reserved Time","Action"),$array__selected_timeframe_list).'</div></div>');
   	
    $form['timeframe_popup_visibility_control']=array('#type'=>'markup','#value'=>'<div class="clear"></div><div id="id_div__timeframe_popup_visibility_control" class="css_div__timeframe_popup_visibility_control '
    	.'"><a href="javascript:void(0)" onclick="InitScheduleCalendar();"><span>Set '.$params['reservations']['#title'].'</span></a></div>');

    wssr_form_restore_defaults($form,$form_state);

    array_walk($form, 'wssr_attach_form_attributes', $params);

    return $form;
}

	function FilterSelectedTimeframes($array__selected_timeframe_list,$array__timeframe_reservation_conflict_list)
	{
		$array__result=array();
		foreach ($array__selected_timeframe_list as $var__key=>$var__value)
    	{
    		$array__result[$var__key]=array_diff($array__selected_timeframe_list[$var__key],$array__timeframe_reservation_conflict_list[$var__key]);
    		if (count($array__result[$var__key])===0)
    			unset($array__result[$var__key]);
    		else
    			sort($array__result[$var__key]);
    	}
    	return $array__result;
	}

	function ParseTimeframesConflicts($array__raw_conflict_list)
	{
		$array__timeframe_reservation_conflict_list=array();
    	foreach ($array__raw_conflict_list as $var__value)
    	{
    		$int__current_full_date=strtotime($var__value[SXML_ATTR]['value']); //'value'=>'30-08-2011 10:30 AM'
    		$int__start_date=intval(date('w',$int__current_full_date))===0?strtotime("midnight",$int__current_full_date):strtotime('last sunday',$int__current_full_date);
    		$array__timeframe_reservation_conflict_list[date('m/d/Y',$int__start_date)][]=floor(($int__current_full_date-$int__start_date)/1800);
    	}
    	
    	foreach ($array__timeframe_reservation_conflict_list as $var__key=>$var__value)
    		sort($array__timeframe_reservation_conflict_list[$var__key]);

    	return $array__timeframe_reservation_conflict_list;
	}

	function EncodeSelectedTimeframes($array__timeframe)
	{
		$array__result=array();
		foreach ($array__timeframe as $var__key=>$var__value)
			if(count($array__timeframe[$var__key]) != 0)
				$array__result[]=($var__key.':'.implode(',',$array__timeframe[$var__key]));
			
		return implode(';',$array__result);
	}

	function GroupSelectedTimeframeList($selectedTimeframes)
	{		
        $groups = array();
        foreach ($selectedTimeframes as $date=>$var__value)
		{
            $dateFrames = $selectedTimeframes[$date];
            $first = $dateFrames[0];
            for($x = 1; $x<count($dateFrames); $x++)
			{
                if( (intval($dateFrames[$x]) - intval($dateFrames[$x-1]) != 1) || (intval($dateFrames[$x])%48 == 0) )
				{
                    $groups[date('m/d/Y',$dateFrames[$x-1] * 1800 + strtotime($date))][]=array('start'=>intval($first), 'end'=>intval($dateFrames[$x-1]));
                    $first = $dateFrames[$x];
				}
			}
            $groups[date('m/d/Y',$dateFrames[$x-1] * 1800 + strtotime($date))][]=array('start'=> intval($first), 'end'=> intval($dateFrames[$x-1]));
		}
		
		
		$array__temp=array_keys($groups);
		sort($array__temp,SORT_STRING);
		$array__result=array();
		foreach ($array__temp as $var__value)
		{
			$array__result[$var__value]=$groups[$var__value];
		}

		return $array__result;
	}
	
	function GenerateTimeframesResultList($array__selected_timeframe_list,$bool__edit_link_visibility_status=true)
	{
		$array__selected_timeframe_list=GroupSelectedTimeframeList($array__selected_timeframe_list);
//		$string_html__output='<table class="css_table__timeframe_list">';

		$int__index=0;
		$array__result=array();
		foreach ($array__selected_timeframe_list as $var__key=>$var__value)
		{
//			$string_html__output.=
//				'<tr class="'.($int__index%2==0?'even':'odd').'"><td width="100px" valign="top">'.$var__key.'</td><td>';
			
			$array__temp=array();
			foreach ($array__selected_timeframe_list[$var__key] as $var__key_=>$var__value_)
			{
				$int__start_timeframe_timestamp=$var__value_['start'] * 1800 + strtotime($var__key);
				$int__end_timeframe_timestamp=($var__value_['end']+1) * 1800 + strtotime($var__key);				

				$array__temp[]='<span>'.date('h:ia',$int__start_timeframe_timestamp).' - '.date('h:ia',$int__end_timeframe_timestamp).'</span>';
			}
			
			$array__result[$int__index][]=$var__key;
			
			$array__result[$int__index][]=implode(', ',$array__temp);
			
			if ($bool__edit_link_visibility_status)
			{
				$string__week_start_date=intval(date('w',strtotime($var__key)))===0?$var__key:date('m/d/Y',strtotime('last sunday',strtotime($var__key)));
				$string_html__edit_link='<a href="javascript:void(0)" onclick="InitScheduleCalendar(\''.$string__week_start_date.'\')">Edit</a>';
				$array__result[$int__index][]=$string_html__edit_link;
			}
			$int__index++;
			
//			$string_html__output.=implode(', ',$array__temp).'</td>'.($bool__edit_link_visibility_status?$string_html__edit_link:'').'</tr>';
		}
//		$string_html__output.='</td></tr>';
//		$string_html__output.='</table>';
		
		return $array__result; 
	}

function _wssr_shared_form_summary($form_state)
{
	$array__create_information=array();
	$array__location_description=array();
	
	if ($form_state['storage']['page']=='review')
    	drupal_set_title(variable_get('review_page_title','Review Your Request'));
    else if ($form_state['storage']['page']=='confirm')
    {
    	if ($form_state['storage']['reply']['status']=='new')
    	{
    		$array__create_information[]=array('title'=>'Request ID','value'=>$form_state['storage']['reply']['id']);
    		drupal_set_title(variable_get('confirmation_page_success','Request Created'));
    	}
    	else
    	{
    		if (get_last_ws_error_description())
        		$string__error_description=get_last_ws_error_description();
        	else
        		$string__error_description=$form_state['storage']['reply']['description'];
        	
        	if (!$string__error_description)
        		$string__error_description="Can't Create Service Request";
        	
    		$array__create_information[]=array('title'=>'Error description','value'=>$string__error_description,'attribute'=>array('class'=>'css_tr__error'));
    		drupal_set_title(variable_get('confirmation_page_error','Creation Failed'));
    	}
    }

    $wrapper = wssr_summary_wrapper($form_state, 'wssr_shared_form');
    //$vals = wssr_replace_select_data($form_state['storage']['values']);
	$vals = $form_state['storage']['values'];

    $form['#attributes'] = array('class' => 'sr_form '.$wrapper['class']);

    $form['form_container_prefix']=array('#type'=>'markup','#value'=>'<div class="css__form_container">');

    $form['before'] = $wrapper['before'];

    $params = wssr_get_field_params(wssr_load_field_params('wssr_shared_form'), 'wssr_shared_form');
//@karp: to read from list
	$requestName = 'Shared Resources';

    if($params['sr_category']['#access'])
    	$array__create_information[]=array('title'=>$params['sr_category']['#title'],'value'=>$requestName);
    
	if($params['requested_by']['#access'])
    	$array__create_information[]=array('title'=>$params['requested_by']['#title'],'value'=>$_SESSION['user_display_name']);

	if ($vals['division'] && $vals['division']!=-1)
	{
		$divisionsData = get_ws_user_divisions_list_options();
		//if ($divisionsData===false)
		//	return false;
		$divisionName = $divisionsData['options'][$vals['division']];
		$array__create_information[]=array('title'=>$params['division']['#title'],'value'=>$divisionName);
	}

	if ($vals['cost_center'])
		$array__create_information[]=array('title'=>$params['cost_center']['#title'],'value'=>$vals['cost_center']);
	if ($vals['brief_description'])
    	$array__create_information[]=array('title'=>$params['brief_description']['#title'],'value'=>nl2br($vals['brief_description']));
	
	$array__create_information=array_merge($array__create_information,$array__location_description);
//	$bool__other_field_presence_flag=false;
//	if (count($array__create_information)>0)
//	{
//		$bool__other_field_presence_flag=true;
/*		$form['main_info']=array('#type'=>'markup','#value'=>'<tr>
			<td class="css__left_column"><div class="css_div__left_column">'.WSSR_RenderReviewTableBlock("Create Information",$array__create_information).'</div></td>
			<td class="css__right_column"><div class="css_div__right_column">'.WSSR_RenderReviewTableBlock("Location Description",$array__location_description).'</div></td></tr>');
*/
//		$form['main_info']=array('#type'=>'markup','#value'=>'<tr>
//			<td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableBlock("Create Information",$array__create_information).'</td></tr>');
		$form['main_info']=array('#type'=>'markup','#value'=>'<tr>
			<td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableGridSummary("Create Information",$array__create_information,array('150px')).'</td></tr>');
//	}

/*	if ($vals['brief_description'])
    {
    	if ($bool__other_field_presence_flag)
    		$form['h_separator-blank-0']=array('#type'=>'markup','#value'=>'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>');
    	else
    		$bool__other_field_presence_flag=true;
    	
    	$form['form_content_brief_description']=array('#type'=>'markup','#value'=>
    	 	'<tr><td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableDescription($params['brief_description']['#title'],nl2br($vals['brief_description'])).'</td></tr>');
    }
*/
	
	$array__selected_timeframe_list=ws_parse_timeframes($vals['timeframes']);
    if ($form_state['storage']['reply']['timeframe_reservation_conflict_list'])
    {
/*    	if ($bool__other_field_presence_flag)
    		$form['h_separator-blank-1']=array('#type'=>'markup','#value'=>'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>');
    	else
    		$bool__other_field_presence_flag=true;*/
    	
    	$array__timeframe_reservation_conflict_list=ParseTimeframesConflicts($form_state['storage']['reply']['timeframe_reservation_conflict_list']);
    	
    	$array__selected_timeframe_list=FilterSelectedTimeframes($array__selected_timeframe_list,$array__timeframe_reservation_conflict_list);
   
		$array__timeframe_reservation_conflict_list=GenerateTimeframesResultList($array__timeframe_reservation_conflict_list,false);
		
		$form['h_separator-blank-0']=array('#type'=>'markup','#value'=>'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>');
		$form['reservation_conflict']=array('#type'=>'markup','#value'=>'<tr><td class="css__left_column" colspan="2"><div id="id_div__schedule_reservation_conflict" class="css_div__schedule_summary_preview css_div__schedule_summary_conflict"
    		><div id="id_div__schedule_reservation_conflict_container">'.WSSR_RenderReviewTableGrid("Reservation conflicts",array("Date","Reserved Time"),$array__timeframe_reservation_conflict_list).'</div></div></td></tr>');
    }
    
	$array__selected_timeframe_list=GenerateTimeframesResultList($array__selected_timeframe_list,false);
//	if ($bool__other_field_presence_flag)
		
	$form['h_separator-blank-1']=array('#type'=>'markup','#value'=>'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>');
	$form['schedule_summary']=array('#type'=>'markup','#value'=>'<tr><td class="css__left_column" colspan="2"><div id="id_div__schedule_summary_preview" class="css_div__schedule_summary_preview" 
	   	><div id="id_div__schedule_summary_preview_container">'.WSSR_RenderReviewTableGrid("Selected timeframes",array("Date","Reserved Time"),$array__selected_timeframe_list).'</div></div></td></tr>');    

    $form['after'] = $wrapper['after'];

	$form['form_container_postfix']=array('#type'=>'markup','#value'=>'</div>');

    $page = $form_state['storage']['page'];
    if(isset($form_state['storage']['reply']))
        $reply = $form_state['storage']['reply'];
	if($page == 'confirm' && $reply['status'] == 'new') {
		$form['#action']=base_path().'wsservicerequest/main';
	}

    return $form;
}

function wssr_shared_form_validate($form, &$form_state){
//    $start_stamp = ws_get_property_time($form_state['values']['start_date'].' '.$form_state['values']['start_time'], $form_state['values']['origin_property_id']);
//print_r($form_state);
	if ($form_state['values']['timeframe_reservation_conflict_list'])
	{
		$form_state['values']['timeframe_reservation_conflict_error_flag']=true;
		form_set_error('timeframe_reservation_conflict_list', "Please clear time reservation conflict list.");
		$form_state['storage']['values']['timeframe_reservation_conflict_list']='';
	}
/*	if (!$form_state['values']['timeframes'] && $form_state['clicked_button']['#id']==='edit-review')
	{
		$form_state['values']['timeframe_empty_list_error_flag']=true;
		form_set_error('timeframe_empty_list_error_flag', "Please select reservation time.");
	}
*/	
/*    if ($form_state['clicked_button']['#id'] == 'edit-review') {
        if ($start_stamp<time())
       	form_set_error('start_time', "Start Date and Time can't be in the past.");
    }*/
}

function wssr_shared_form_submit($form, &$form_state){

    if ($form_state['clicked_button']['#id'] == 'edit-review') {
        $form_state['storage']['page'] = 'review';
        // Push data to storage
        $form_state['storage']['values'] = $form_state['values'];
    }
    else if ($form_state['clicked_button']['#id'] == 'edit-back') {
        $form_state['rebuild'] = true;
        unset($form_state['storage']['page']);
        $form_state['storage']['restore_defaults'] = true;
        
        if ($form_state['storage']['reply']['timeframe_reservation_conflict_list'])
        {
        	$form_state['storage']['values']['timeframe_reservation_conflict_list']=EncodeSelectedTimeframes(ParseTimeframesConflicts($form_state['storage']['reply']['timeframe_reservation_conflict_list']));
	    	unset($form_state['storage']['reply']['timeframe_reservation_conflict_list']);
        }
    }
    else if ($form_state['clicked_button']['#id'] == 'edit-confirm') {
        $form_state['storage']['page'] = 'confirm';
        //some data processing before send it to main app needed.
        $data = $form_state['storage']['values'];
		$propertyId=$data['origin_property_id'];
        //$originTree = explode("::", $data['origin_tree']);
		$timeframesstr=$data['timeframes'];
        $weeksArray=explode(';',$timeframesstr);
        $shiftInSeconds=30*60;
        $reservations=array();
        foreach($weeksArray as $weekStr) {
            list($weekStartDay,$weekTimeframes) = explode(':',$weekStr.':');
            //08/28/2011 -> 2011-08-28
            $startDateArray=explode('/', $weekStartDay);
            $startTS=gmmktime(0, 0, 0, intval($startDateArray[0]), intval($startDateArray[1]), intval($startDateArray[2]));
            $utcTZ = new DateTimeZone("UTC");
            //$startDate = new DateTime($reqTSStr,$utcTZ);
            $timeframesArray=explode(',',$weekTimeframes);
            foreach($timeframesArray as $shift) {
                $ts=$startTS+$shift*$shiftInSeconds;
                $dt = new DateTime('@'.$ts,$utcTZ);
                $dtDay=$dt->format("Y-m-d");
                /*
                if (empty($reservations[$dtDay])) {
                    $reservations[$dtDay]=$dt->format("H:i");
                } else
                    $reservations[$dtDay].=','.$dt->format("H:i");
                 */
                /*
                if (empty($reservations[$dtDay])) {
                    $reservations[$dtDay]=array();
                }
                 */
                $reservations[$dtDay][]=$dt->format("H:i");
            }
        }
        /*
		$tmpTimeAr = explode(" ",$data['start_time']);
		$tmpTimeAr[0]=$tmpTimeAr[0].':00';
		$sTime=implode(" ",$tmpTimeAr);
        $startDate = sprintf('%s %s', $data['start_date'], $sTime);
		$tmpTimeAr = explode(" ",$data['end_time']);
		$tmpTimeAr[0]=$tmpTimeAr[0].':00';
		$eTime=implode(" ",$tmpTimeAr);
        $endDate = sprintf('%s %s', $data['end_date'], $eTime); //strtoupper
        */

		//$originSuite=$data['origin_suite'];
        $resourceId=$data['origin_suite'];

        if($data['division'] == -1)
            $data['division'] = NULL;

        //$timeframes = ws_parse_timeframes($data['timeframes']);

        $args = array(
            //"resourceId"=>"282681", //@karp: temporary hardcoded
            "resourceId"=>$resourceId, //@karp: temporary hardcoded
            //"propertyId"=> $propertyId,
            //"spaceId"=> trim($originTree[3]),
			//"spaceId"=>$originSuite,
            //"typeId"=> "9010",
            //"entityId"=> $data['company'],
            "division"=> $data['division'],
			"costCenterId"=> $data['cost_center'],
			//"description"=> $data['brief_description'],
			//"createdFor"=>get_ws_user_id(),
            //"profileId"=> $profileId,
            //"description"=> $data['description'],
            //"startDate"=> $startDate,
            //"endDate"=> $endDate,
			//"locationDetails"=> $data['location_details'],
			//"askFor"=> $data['ua_ask_for'],
        );

		$rawData = '<description>'.xmlspecialchars($data['brief_description']).'</description>';
/*
        foreach($timeframes as $timeframe){
            $dates[date("Y-m-d", $timeframe)][] = date("H:i", $timeframe);
        }
  */
        $frameTags = '';
        foreach($reservations as $rday=>$rtime){
            if (!empty($rtime))
                $frameTags .= '<timeFrameWithDate dateValue="'.$rday.'" timeValue="'.implode(',', $rtime).'"/>';
        }

		//$rawData.='<timeframes>'.xmlspecialchars(implode(';',$timeframes)).'</timeframes>';
        $rawData.=$frameTags;
		
		//if ($data['ua_ask_for'] && $data['ua_ask_for']!='')
		//	$rawData .= '<askFor>'.xmlspecialchars($data['ua_ask_for']).'</askFor>';
		//if ($data['location_details'] && $data['location_details']!='')
		//	$rawData .= '<locationDetails>'.xmlspecialchars($data['location_details']).'</locationDetails>';
        $form_state['storage']['reply'] = wssr_submit_request('createSharedResourceRequest', $args, $rawData);
    }
    // Finally
    else {
        // We must unset 'storage' to prevent form from rebuild. http://drupal.org/node/144132
        unset ($form_state['storage']);
    }
}

function wssr_shared_form_map()
{
	return array
		(
        'page_title'=>array('name'=>'Tenant Services','type'=>'core', 'admin_description'=>"Create overtime shared page title"),
        'origin'=>array('name'=>'Change shared request','type'=>'core', 'admin_description'=>"'Location tree' popup link text"),
        'origin_tree'=>array('name'=>'Shared Requests','type'=>'core', 'admin_description'=>"'Location tree' popup title"),
        'brief_description'=>array('name'=>'Brief Description','type'=>'core', 'admin_description'=>"'SR description'/'Brief description' field title"),
        'no_access'=>array('name'=>'No access', 'type'=>'core', 'admin_description'=>"Message if user has no permission to access Create overtime shared page"),
		'new_request_button_title'=>array('name'=>'Create shared resource request', 'type'=>'core', 'admin_description'=>"Label on the 'Create shared resource request' button ('Confirmation' page)"),
		'modify_request_button_title'=>array('name'=>'Modify', 'type'=>'core', 'admin_description'=>"Label on the 'Modify overtime shared' button ('Confirmation' page, if failed)"),

        'reservations'=>array('name'=>'Reservation', 'type'=>'core', 'admin_description'=>"Used for 'Time reservation' labels"),
        'selected_timeframes'=>array('name'=>'Selected timeframes', 'type'=>'core', 'admin_description'=>"'Selected timeframes' label"),
        'legend'=>array('name'=>'Legend', 'type'=>'core', 'admin_description'=>"'Legend' label for popup window"),

        'shared_resource'=>array('name'=>'Shared Resource','type'=>'core', 'admin_description'=>"'Shared Resource' fields part"),

		'sr_category'=>array('name'=>'SR Category','type'=>'custom', 'admin_description'=>"'SR Category' field title (Review/Confirm page)"),
		'requested_by'=>array('name'=>'Requested by','type'=>'custom', 'admin_description'=>"'Requested by user' field title (Review/Confirm page)"),
//		'tenant'=>array('name'=>'Tenant','type'=>'custom', 'admin_description'=>"'Tenant Company name' field on the Review/Confirm page"),
        'cost_center'=>array('name'=>'Cost Center','type'=>'custom', 'admin_description'=>"'Cost Center' field title"),
        'division'=>array('name'=>'Division','type'=>'custom', 'admin_description'=>"'Division' field title"),
		);
}

function theme_wssr_shared_form($form){
    body_class('sr_shared');
    $params = wssr_get_field_params(wssr_load_field_params('wssr_shared_form'), 'wssr_shared_form');

    drupal_add_js('
        var reservationLabel = "'.$params['reservations']['#title'].'";
    ', 'inline');

    if (isset($form['is_summary']) || isset($form['is_noaccess']))
    	return drupal_render($form);

    $output = '<div class="css__form_container">';

	wssr_adjust_scripts_temp();

    wssr_init_cost_center_js($form['cost_center_array']);

	$simpleArr=$form['originTreeStructure']['#value'];
    
    /*$sharedCatList = wssr_get_shared_categories_list();
    $shResources = array();
    foreach($simpleArr['simpleTree'] as $propId => $tmp){
        $shResources[$propId] = wssr_get_shared_resources_list($sharedCatList, $propId);
    }
    $simpleArr['simpleTree'] = ws_shared_resources_tree($simpleArr['simpleTree'], $shResources);
    */

    //print_r($shResources); die;
//@karp: $bShowOriginChangeLink=true; - for future use.
    $bShowOriginChangeLink=true;
	$output .= wssr_adjust_change_origin($simpleArr['default']['property'], $simpleArr['default']['address'],$bShowOriginChangeLink, 'wssr_shared_form', 'Select '.$params['shared_resource']['#title']);

    drupal_add_js('
        shared_init_origin_apply();
    ', 'inline');
    wssr_add_js_property_tree($simpleArr);


    $output .= '<div class="css__form_wrapper">';

    $review=drupal_render($form['review']);
    
    $string_html__temp=drupal_render($form['pager']);

    $output .= drupal_render($form);
    
            $output .= '<div id="id_div__schedule_calendar-template" class="css_div__schedule_calendar-overlay_layer" style="display:none;"
            	><div class="css_div__schedule_calendar">
            		<div class="css_div__group" onmousedown="return false;" onselectstart="return false;">
            			<div style="margin-bottom:10px;text-align:right;"
            				><a href="javascript:void(0)" onclick="CancelScheduleCalendarChanges()">Close</a></div>
            			'.$string_html__temp.'
            			<div class="clear"></div>
            			<div class="css_div__time_intervals_column_overlay"></div>
            			<div class="css_div__timeframe"
	            			><table class="css_table__timeframe" cellpadding="0" cellspacing="0">
	            				<tr>
	            					<td class="css_td__first_column"></td>
	            					<td></td>
	            				</tr>
	            				<tr>
	            					<td>'.GenerateTimeIntervalsColumn().'</td>
	            					<td>'.ws_timeframe_selector($form,"#edit-timeframes").'</td>
	            				</tr>
	            			</table></div>
            			<div style="text-align:right;margin-top:10px"><a href="javascript:void(0)" onclick="ClearAllTimeframes();">Clear All</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="ApplyScheduleCalendarChanges();">Apply</a></div>
            			<div class="css_div__legend">
	            			<div class="css_div__title">'.$params['legend']['#title'].':</div>
	            			<div class="css_div__icon css_div__icon-reserved"><div></div>Reserved</div>
	            			<div class="css_div__icon css_div__icon-available"><div></div>No Reservation Required</div>
	            			<div class="css_div__icon css_div__icon-close"><div></div>Unavailable</div>
	            			<div class="css_div__icon css_div__icon-selected"><div></div>Current Selection</div>
	            			<div class="css_div__icon css_div__icon-past_date"><div></div>Past Date</div>
	            		</div>
            		</div>
            	</div></div>';
            
	

    $output .= '</div><div class="css__bottom_border">&nbsp;</div><div class="submit"><div class="css__padding_1">&nbsp;</div><div class="css__padding_2">'.$review.'</div></div>';
    $output .= '</div>';
    return $output;
}

function GenerateTimeIntervalsColumn($int__interval=30)
{
	$grid=array();
	for($time=0; $time<48; $time++)
	{
        $timeLabel = gmdate('h:i', ($time)*$int__interval*60) .' - '. gmdate('h:ia', ($time+1)*$int__interval*60);
        
        $grid[] = '<tr class="'.(($time+1)%2==0?'even':'odd').'"><td>'.$timeLabel.'</td></tr>';
    }
    return '<table class="css_table__time_intervals_column" cellpadding="0" cellspacing="0">'.implode('',$grid).'</table>';
}

/*
 * $data - square table with id's and required info (open/reserved/normal etc.)
 * $recipient - recipient css ID
 */
function ws_timeframe_selector($form, $recipient){
    add_jquery_multi();

    $grid = array();
    $heading = array();

    for($day=0; $day<7; $day++)
    {
        $heading[] = array('data'=>'&nbsp;');
    }

    for($time=0; $time<48; $time++)
    {
        $row = array();
        for($day=0; $day<7; $day++)
        {
			$int__time_offset=($day*48)+($time);
            $row[] = array(
                'data'=> '&nbsp;',
                'id'=>'timeframe-'.$int__time_offset,
                'unselectable' => 'on',
            	'class'=>'css_td__timeframe_item '
            );
        }
        $grid[] = $row;
    }

    $control = theme('table', $heading, $grid, array('id'=>'timeframe-select'));

        drupal_add_js('
        var recipient = "'.$recipient.'";
        shared_init();
    ', 'inline');
    return $control;

}

function ws_parse_timeframes($timeframes)
{
    $array__temp = explode(';', $timeframes);
    $timeframes=array();
    foreach ($array__temp as $var__value)
    {
    	$array__temp_=explode(':',$var__value);
    	$timeframes[$array__temp_[0]]=explode(',',$array__temp_[1]);
    	sort($timeframes[$array__temp_[0]]);
    }
    
    

    return $timeframes;
}
function wssr_shared_calendar($resourceId, $dateYMD) {
    //$dateYMD -> 'yyyy-mm-dd'
    //$resourceId=282681;
    $array__temp=wssr_get_restricted($resourceId, $dateYMD);
    echo json_encode($array__temp);
    //$restricted = $array__temp['timeframes'];

    /*
    for($day=0; $day<7; $day++)
    {
        $dayLabel = date('D m/d', $startDay + ($day * 24 * 60 * 60));
        $heading[] = "<th id='timeframe-day-".($startDay + ($day * 24 * 60 * 60))."'>$dayLabel</th>";
    }
    $heading = implode('',$heading);
    */


    //echo json_encode(array(/*'weekStartDate'=>,*/ 'restricted'=>$restricted,'timeframe_details'=>$array__temp['timeframe_details']));
    die;
}

//Lex: maybe send m-d-Y from JS and replace '-' to '/'? Looks cleaner.
function wssr_get_restricted($resourceId, $dateMDY){
    //$requestedDate=date_create_from_format("m-d-Y", $dateMDY, new DateTimeZone("UTC"));
    //list($req_month, $req_day, $req_year) = sscanf($dateMDY, '%02d-%02d-%04d');
    $reqDateArray=explode('-', $dateMDY);
    $reqTS=gmmktime(0, 0, 0, intval($reqDateArray[0]), intval($reqDateArray[1]), intval($reqDateArray[2]));
    $reqTSStr='@'.$reqTS;
    $utcTZ = new DateTimeZone("UTC");
    $requestedDate = new DateTime($reqTSStr,$utcTZ);
    $xml = wssr_data_request("shrschedule", array(
                                       'resourceId' => $resourceId,
                                       'startDate' => date_format($requestedDate, 'm/d/Y'),
                                     ));
    //@karp: check for errors
    $weekCalendar=$xml['Content'][0][SXML_CHILDLIST]['WeekCalendar'][0];
    $firstDayStr=$weekCalendar[SXML_ATTR]['startDate']; //YYYY-MM-DD
    if (empty($firstDayStr))
            return null;
    //$firstDay=date_create_from_format("Y-m-d", $firstDayStr, new DateTimeZone("UTC"));
    //list($first_month, $first_day, $first_year) = sscanf($dateMDY, '%02d-%02d-%04d');
    $firstDay=date_create($firstDayStr, new DateTimeZone("UTC"));
    $firstDayTS=intval($firstDay->format("U"));

    //$highFrames = array();
    //$shortFrames = array();
    $dayList = $xml['Content'][0][SXML_CHILDLIST]['WeekCalendar'][0][SXML_CHILDLIST]['DayList'][0][SXML_CHILDLIST]['Day'];
    $restrictions=array( 'reservation'=>array(), 'available'=>array(), 'close'=>array(), reserved=>array() );
    $secondsInDay=60*60*24;
    //@karp: restrictions are unsorted (do not use numeric indexes on client's javascript site
    foreach($dayList as $day){
        $dateVal = $day[SXML_CHILDLIST]['Date'][0][SXML_VALUE];
        $date=date_create($dateVal, new DateTimeZone("UTC"));
        $dateTS=intval($date->format("U"));
        $int_day_offset=floor( ($dateTS-$firstDayTS)/$secondsInDay )*48;
        //$date->diff() php >=5.3
        //$offsetInterval=$date->diff($firstDay);
        //$intOffsetDays=$offsetInterval->day
        //@karp: use priority!
        //only one icon is 
        //1. "reservation" - not necessary!!!!
        //2. "available"
        //3. "close"
        //4. "reserved"
    
        //processRestrictions(&$restrictions, $int_day_offset, getAvailabilityByType('available'));
        //processRestrictions(&$restrictions, $int_day_offset, getAvailabilityByType('close'));
            //<- another operation
        //processRestrictions(&$restrictions, $int_day_offset, getAvailabilityByType("reserved"));
        //@karp: copy to process restrictions func
        foreach($day[SXML_CHILDLIST]['Availability'] as $availability){
            $type = $availability[SXML_ATTR]['constraint'];
            $tfFrom= $int_day_offset+intval($availability[SXML_ATTR]['fromhour'])*2 + (($availability[SXML_ATTR]['fromminutes']=='30') ? 1:0);
            $tfTo= $int_day_offset+intval($availability[SXML_ATTR]['tohour'])*2 + (($availability[SXML_ATTR]['tominutes']=='30') ? 1:0)-1;
            $restrictions[$type][]=array('startTime'=>$tfFrom, 'endTime'=>$tfTo);
        }
//            $from = sprintf("%02d",$availability[SXML_ATTR]['fromhour']).':'.sprintf("%02d",$availability[SXML_ATTR]['fromminutes']);
//            $to = sprintf("%02d",$availability[SXML_ATTR]['tohour']).':'.sprintf("%02d",$availability[SXML_ATTR]['tominutes']);
            //$highFrames[$type][$date] = array(
            //    'from' => strtotime($date.' '.$from.' GMT'),
            //    //'to' => strtotime($date.' '.$to.' GMT'),
            //);
//            if( $highFrames[$type][$date]['from'] && !$highFrames[$type][$date]['to'] ){
            //    $highFrames[$type][$date]['to'] = strtotime($date.' 23:59'); //@karp: for what???
            //}
            //for ($itTF=$tfFrom;$itTF<$tfTo;$itTF++) {
            //    $restrictionsForDay[$itTF]=$type;
//            }
        //@karp: end of copy to process restrictions func
//        }
        //$restrictions[$date.format("m/d/Y")]=$restrictionsForDay;
    }
    //$array__reserved_timeframe_details=array();
    //
    $xmlSchedule=$xml['Content'][0][SXML_CHILDLIST]['WeekCalendar'][0][SXML_CHILDLIST]['Reservation'][0]
                                                                [SXML_CHILDLIST]['Schedule'];
    $reservationInfo=array();
    $offsetInSeconds=30*60;
	foreach($xmlSchedule as $schedule) {
        $srId=$schedule[SXML_ATTR]['srid'];
        if (empty($reservationInfo[$srId])) {
            $reservationInfo[$srId]=array();
            $reservationInfo[$srId]['srid']=$srId;
            $reservationInfo[$srId]['entityInfo']=array(
                'description'=>$schedule[SXML_CHILDLIST]['EntitySummary'][0][SXML_CHILDLIST]['Description'][0][SXML_VALUE],
                'id'=>$schedule[SXML_CHILDLIST]['EntitySummary'][0][SXML_ATTR]['id'],
            );
                }
        $fromDateStr=$schedule[SXML_CHILDLIST]['FromDate'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE];
        $toDateStr=$schedule[SXML_CHILDLIST]['ToDate'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE];
        //$date=date_create_from_format("Y-m-d H:i:s", $fromDateStr, new DateTimeZone("UTC"));
        $dateFrom=date_create($fromDateStr, new DateTimeZone("UTC"));
        $dateTo=date_create($fromDateStr, new DateTimeZone("UTC"));
        $fromDateTS=intval($dateFrom->format("U"));
        $toDateTS=intval($dateTo->format("U"));
        $tfFrom=($fromDateTS-$firstDayTS)/$offsetInSeconds;
        $tfTo=($toDateTS-$firstDayTS)/$offsetInSeconds;
        $restrictions['reserved'][]=array('startTime'=>$tfFrom, 'endTime'=>$tfTo, 'srid'=>$srId);
            }
    $propertyIdStr = $xml['Content'][0][SXML_CHILDLIST]['PropertyId'][0][SXML_VALUE];
    $propertyId=null;
    if (!empty($propertyIdStr)) {
        $propertyId=intval($propertyIdStr);
    }
    $tz=($propertyId==null) ? 'UTC' : get_property_tz($propertyId);
    $curDate=new DateTime(null, new DateTimeZone($tz));
    $currentWithUTC=date_create($curDate->format("Y-m-d H:i:s"), new DateTimeZone("UTC"));
    $curTS=intval($currentWithUTC->format("U"));
    $maxShift=48*7;
    $currentTimeframe=ceil(($curTS-$firstDayTS)/$offsetInSeconds);
    if ($currentTimeframe<=0)
        $currentTimeframe=-1;
    else if ($currentTimeframe>$maxShift)
        $currentTimeframe=$maxShift;

    return array('timeframes'=>$restrictions,'startDay'=>$firstDay->format("m/d/Y"),
                 'currentTimeframe'=>$currentTimeframe,
                 'reservationInfo'=>$reservationInfo);
}

function ws_shared_tree(){
	
}