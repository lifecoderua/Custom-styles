<?php
function wssr_hvac_form(&$form_state){
    wssr_set_admin_form_title(__FUNCTION__);
    return wssr_process_form('_'.__FUNCTION__, &$form_state);
}

function _wssr_hvac_form(&$form_state){
    global $user;
    file_prefix('hvac_');
    inherited('inherit_sr_hvac');
    ws_get_tz_array();

    $params = wssr_get_field_params(wssr_load_field_params('wssr_hvac_form'), 'wssr_hvac_form');

    if (($form_state['storage']['page'] == 'review') || ($form_state['storage']['page'] == 'confirm'))
    {
        $form = _wssr_hvac_form_summary($form_state);
        $form['is_summary'] = array('#type' => 'hidden', '#value' => 'true', '#disabled'=>true);
        return $form;
    }

    $originDefaults = wssr_get_origin_defaults($form_state);
    $originTree=ws_get_filtered_user_building_structure(null, 'hvac');
    if (is_ws_error())
    	return ws_get_ws_info_error_form(null, $form_state, 'hvac',get_last_ws_error_description());
    
	if (empty($originTree) && $user->uid!=1 )
		return noaccess(null, $form_state, 'wssr_hvac_form');

	if (empty($originDefaults['suiteId'])) {
		$originSimpleArr=wssr_full_tree_simple($originTree, get_user_space_first());
	} else {
	$originSimpleArr=wssr_full_tree_simple($originTree, $originDefaults['suiteId']);
	}
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

	$form['#attributes']=array('class'=>'sr_form sr_form_hvac sr_form_creation', 'onsubmit'=>'return validate_hvac(this);');

	$propertyId = $originSimpleArr['default']['property_id'];

    $form['origin_property']=array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['property']);
	$form['origin_property_id']=array('#type'=> 'hidden', '#default_value'=>$propertyId);
    $form['origin_address']=array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['address']);
    $form['origin_suite']=array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['suiteId']);
	$form['originTreeStructure']=array('#type'=> 'value', '#value'=>$originSimpleArr);
    //@karp: extra check - we can remove it
    $requestList=getCurrentUserRequestListTree($propertyId,'hvac');
    if (is_ws_error())
    	return ws_get_ws_info_error_form(null, $form_state, 'hvac',get_last_ws_error_description());
    
	if (empty($requestList) && $user->uid!=1) {
		return noaccess(null, $form_state, 'wssr_hvac_form');
	}
    //@karp: extra check - we can remove it (end of section to remove)
	drupal_add_js("
    	int__time_picker_minute_interval=".variable_get('int__time_picker_minute_interval','30')."
		",'inline');


	$divisionsData = get_ws_user_divisions_list_options();
    if (is_ws_error())
    	return ws_get_ws_info_error_form(null, $form_state, 'hvac',get_last_ws_error_description());
    
    $tmpDivisions = $divisionsData;
    $tmpDivisions = array_shift($tmpDivisions['options']);

	$form['start_date']=array('#type'=>'textfield','#title'=>$params['start_date']['#title'],'#size'=>'','#required'=>true, '#attributes'=>array('class'=>'date-pick'));
	$form['start_time']=array('#type'=>'textfield','#title'=>$params['start_time']['#title'],'#size'=>'','#required'=>true, '#attributes'=>array('class'=>'time-pick','onclick'=>"ShowTimePicker(this,'id__visitor_time_picker_layer-start','edit-start-time')"));

	$form['h_separator_1']=array('#type'=>'markup','#value'=>'<div class="css__h_separator"></div>');

	$form['end_date']=array('#type'=>'textfield','#title'=>$params['end_date']['#title'],'#size'=>'','#required'=>true, '#attributes'=>array('class'=>'date-pick'));
	$form['end_time']=array('#type'=>'textfield','#title'=>$params['end_time']['#title'],'#size'=>'','#required'=>true, '#attributes'=>array('class'=>'time-pick','onclick'=>"ShowTimePicker(this,'id__visitor_time_picker_layer-end','edit-end-time')"));
    
    $form['brief_description']=array('#type'=>'textarea','#title'=>$params['brief_description']['#title'],'#required'=>true,'#maxlength'=>2000);
    $form['division']=array('#type'=>'select','#title'=>$params['division']['#title'],'#options'=>$divisionsData['options'], '#access' => $divisionsData['options']?true:false);

    $form['cost_center']=array('#type'=>'textfield','#title'=>$params['cost_center']['#title'],'#size'=>'', '#default_value'=>$divisionsData['default_cost_center'],'#maxlength'=>100);
    $form['cost_center_array'] = array('#type' => 'hidden', '#value' => $divisionsData['cost_center_array'], '#access' => false);

    if(!$params['cost_center']['#access'])
        $form['cost_center']['#default_value'] = '';

	$form['location_details']=array('#type'=>'textarea','#title'=>$params['location_details']['#title'],'#maxlength'=>255);
	$form['ua_ask_for']=array('#type'=>'textarea','#title'=>$params['ua_ask_for']['#title'],'#maxlength'=>255);


	$form['review']=array('#type'=>'submit','#value'=>'Submit');

    wssr_form_restore_defaults($form,$form_state);

    array_walk($form, 'wssr_attach_form_attributes', $params);

    return $form;
}

function _wssr_hvac_form_summary($form_state)
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

    $wrapper = wssr_summary_wrapper($form_state, 'wssr_hvac_form');
    //$vals = wssr_replace_select_data($form_state['storage']['values']);
	$vals = $form_state['storage']['values'];
//	print_r($form_state);
    $form['#attributes'] = array('class' => 'sr_form '.$wrapper['class']);

    $form['form_container_prefix']=array('#type'=>'markup','#value'=>'<div class="css__form_container">');

    $form['before'] = $wrapper['before'];

    $params = wssr_get_field_params(wssr_load_field_params('wssr_hvac_form'), 'wssr_hvac_form');
//@karp: to read from list
	$requestName = 'HVAC - overtime';

    if($params['sr_category']['#access'])
    	$array__create_information[]=array('title'=>$params['sr_category']['#title'],'value'=>$requestName);

    if($params['requested_by']['#access'])
    	$array__create_information[]=array('title'=>$params['requested_by']['#title'],'value'=>$_SESSION['user_display_name']);

	$array__create_information[]=array('title'=>$params['visiting_from']['#title'],'value'=>$vals['start_date'].' at '.$vals['start_time']);
	$array__create_information[]=array('title'=>$params['visiting_to']['#title'],'value'=>$vals['end_date'].' at '.$vals['end_time']);
    
	if($params['tenant']['#access'])
    	$array__location_description[]=array('title'=>$params['tenant']['#title'],'value'=>$_SESSION['wsUserInfo']['entity']['displayName']);
    
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
	
/*	$form['main_info']=array('#type'=>'markup','#value'=>'<tr>
		<td class="css__left_column"><div class="css_div__left_column">'.WSSR_RenderReviewTableBlock("Create Information",$array__create_information).'</div></td>
		<td class="css__right_column"><div class="css_div__right_column">'.WSSR_RenderReviewTableBlock("Location Description",$array__location_description).'</div></td></tr>');
*/
//	$form['main_info']=array('#type'=>'markup','#value'=>'<tr>
//		<td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableBlock("Create Information",$array__create_information).'</td></tr>');
	$form['main_info']=array('#type'=>'markup','#value'=>'<tr>
		<td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableGridSummary("Create Information",$array__create_information,array('150px')).'</td></tr>');
    
/*	if ($vals['brief_description'])
    {
    	$form['h_separator-blank-0']=array('#type'=>'markup','#value'=>'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>');
    	$form['form_content_brief_description']=array('#type'=>'markup','#value'=>
    	 	'<tr><td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableDescription($params['brief_description']['#title'],nl2br($vals['brief_description'])).'</td></tr>');
    }
*/
	if ($vals['location_details'] || $vals['ua_ask_for'])
    {
    	$form['h_separator-blank-1']=array('#type'=>'markup','#value'=>'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>');
    	$array__temp=array();
    	if ($vals['location_details'])
    		$array__temp[]=array('title'=>$params['location_details']['#title'],'value'=>nl2br($vals['location_details']));
    	if ($vals['ua_ask_for'])
    		$array__temp[]=array('title'=>$params['ua_ask_for']['#title'],'value'=>nl2br($vals['ua_ask_for']));
    	$form['form_content_additional_information']=array('#type'=>'markup','#value'=>
    	 	'<tr><td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableBlock("Additional Information",$array__temp).'</td></tr>');
    }

    $form['after'] = $wrapper['after'];

	$form['form_container_postfix']=array('#type'=>'markup','#value'=>'</div>');

    $page = $form_state['storage']['page'];
    if(isset($form_state['storage']['reply']))
        $reply = $form_state['storage']['reply'];
	if($page == 'confirm' && $reply['status'] == 'new')
		$form['#action']=base_path().'wsservicerequest/main';

    return $form;
}

function wssr_hvac_form_validate($form, &$form_state){
    $start_stamp = ws_get_property_time($form_state['values']['start_date'].' '.$form_state['values']['start_time'], $form_state['values']['origin_property_id']);

    if ($form_state['clicked_button']['#id'] == 'edit-review') {
        if ($start_stamp<time())
        	form_set_error('start_time', "Start Date and Time can't be in the past.");

        if($form_state['values']['start_date'] && $form_state['values']['end_date'] && $form_state['values']['start_time'] && $form_state['values']['end_time']){
            $startTimestamp = strtotime($form_state['values']['start_date'].' '.$form_state['values']['start_time']);
            $endTimestamp = strtotime($form_state['values']['end_date'].' '.$form_state['values']['end_time']);
            if($startTimestamp >= $endTimestamp)
            {
                if($form_state['values']['start_date'] === $form_state['values']['end_date'])
                    form_set_error('end_time', 'Start Time is after End Time!');
                else
                    form_set_error('end_date', 'Start Date is after End Date!');
            }
        }
    }
}

function wssr_hvac_form_submit($form, &$form_state){

    if ($form_state['clicked_button']['#id'] == 'edit-review') {
        $form_state['storage']['page'] = 'review';
        // Push data to storage
        $form_state['storage']['values'] = $form_state['values'];
    }
    else if ($form_state['clicked_button']['#id'] == 'edit-back') {
        $form_state['rebuild'] = true;
        unset($form_state['storage']['page']);
        $form_state['storage']['restore_defaults'] = true;
    }
    else if ($form_state['clicked_button']['#id'] == 'edit-confirm') {
        $form_state['storage']['page'] = 'confirm';
        //some data processing before send it to main app needed.
        $data = $form_state['storage']['values'];
		$propertyId=$data['origin_property_id'];
        //$originTree = explode("::", $data['origin_tree']);
		$tmpTimeAr = explode(" ",$data['start_time']);
		$tmpTimeAr[0]=$tmpTimeAr[0].':00';
		$sTime=implode(" ",$tmpTimeAr);
        $startDate = sprintf('%s %s', $data['start_date'], $sTime);
		$tmpTimeAr = explode(" ",$data['end_time']);
		$tmpTimeAr[0]=$tmpTimeAr[0].':00';
		$eTime=implode(" ",$tmpTimeAr);
        $endDate = sprintf('%s %s', $data['end_date'], $eTime); //strtoupper
		$originSuite=$data['origin_suite'];

        if($data['division'] == -1)
            $data['division'] = NULL;
            
        $args = array(
            "propertyId"=> $propertyId,
            //"spaceId"=> trim($originTree[3]),
			"spaceId"=>$originSuite,
            "typeId"=> "9010",
            //"entityId"=> $data['company'],
            "division"=> $data['division'],
			"costCenterId"=> $data['cost_center'],
			//"description"=> $data['brief_description'],
			//"createdFor"=>get_ws_user_id(),
            //"profileId"=> $profileId,
            //"description"=> $data['description'],
            "startDate"=> $startDate,
            "endDate"=> $endDate,
			//"locationDetails"=> $data['location_details'],
			//"askFor"=> $data['ua_ask_for'],

        );
		$rawData = '<description>'.xmlspecialchars($data['brief_description']).'</description>';
		if ($data['ua_ask_for'] && $data['ua_ask_for']!='')
			$rawData .= '<askFor>'.xmlspecialchars($data['ua_ask_for']).'</askFor>';
		if ($data['location_details'] && $data['location_details']!='')
			$rawData .= '<locationDetails>'.xmlspecialchars($data['location_details']).'</locationDetails>';
        $form_state['storage']['reply'] = wssr_submit_request('createServiceRequest', $args, $rawData);
    }
    // Finally
    else {
        // We must unset 'storage' to prevent form from rebuild. http://drupal.org/node/144132
        unset ($form_state['storage']);
    }
}

function wssr_hvac_form_map()
{
	return array
		(
        'page_title'=>array('name'=>'Tenant Services','type'=>'core', 'admin_description'=>"Create overtime HVAC page title"),
        'origin'=>array('name'=>'Change location','type'=>'core', 'admin_description'=>"'Location tree' popup link text"),
        'origin_tree'=>array('name'=>'Location','type'=>'core', 'admin_description'=>"'Location tree' popup title"),
		'visiting_from'=>array('name'=>'From Date/Time','type'=>'core', 'admin_description'=>"'Requested Start Date/Time' field title on the Review/Confirm page"),
		'visiting_to'=>array('name'=>'To Date/Time','type'=>'core', 'admin_description'=>"'Requested End Date/Time' field title on the Review/Confirm page"),
        'brief_description'=>array('name'=>'Brief Description','type'=>'core', 'admin_description'=>"'SR description'/'Brief description' field title"),
		'start_date'=>array('name'=>'Start Date','type'=>'core', 'admin_description'=>"'Requested Start Date' field title"),
		'start_time'=>array('name'=>'Start Time','type'=>'core', 'admin_description'=>"'Requested Start Time' field title"),
		'end_date'=>array('name'=>'End Date','type'=>'core', 'admin_description'=>"'Requested End Date' field title"),
		'end_time'=>array('name'=>'End time','type'=>'core', 'admin_description'=>"'Requested End Time' field title"),
        'no_access'=>array('name'=>'No access', 'type'=>'core', 'admin_description'=>"Message if user has no permission to access Create overtime HVAC page"),
		'new_request_button_title'=>array('name'=>'Request more overtime HVAC', 'type'=>'core', 'admin_description'=>"Label on the 'Create new overtime HVAC' button ('Confirmation' page)"),
		'modify_request_button_title'=>array('name'=>'Modify', 'type'=>'core', 'admin_description'=>"Label on the 'Modify overtime HVAC' button ('Confirmation' page, if failed)"),

		'sr_category'=>array('name'=>'SR Category','type'=>'custom', 'admin_description'=>"'SR Category' field title (Review/Confirm page)"),
		'requested_by'=>array('name'=>'Requested by','type'=>'custom', 'admin_description'=>"'Requested by user' field title (Review/Confirm page)"),
		'tenant'=>array('name'=>'Tenant','type'=>'custom', 'admin_description'=>"'Tenant Company name' field on the Review/Confirm page"),
        'cost_center'=>array('name'=>'Cost Center','type'=>'custom', 'admin_description'=>"'Cost Center' field title"),
        'division'=>array('name'=>'Division','type'=>'custom', 'admin_description'=>"'Division' field title"),
        'location_details'=>array('name'=>'Location Details','type'=>'custom', 'admin_description'=>"'Location Details' field title"),
		'ua_ask_for'=>array('name'=>'Upon arrival ask for','type'=>'custom', 'admin_description'=>"'Upon arrival ask for' field title")
		);
}

function theme_wssr_hvac_form($form){
    body_class('sr_hvac');

    if (isset($form['is_summary']) || isset($form['is_noaccess']))
    	return drupal_render($form);

    $output = '<div class="css__form_container">';

	wssr_adjust_scripts_temp();

    wssr_init_cost_center_js($form['cost_center_array']);

    drupal_add_js("
        $(document).ready(function()
	    {
            $('#edit-start-date').bind(
                'dpClosed',
                function(e, selectedDates)
                {
                    var d = selectedDates[0];
                    if (d) {
                        d = new Date(d);
                        $('#edit-end-date').dpSetStartDate(d.addDays(0).asString());
                    }
                }
            );
            $('#edit-end-date').bind(
                'dpClosed',
                function(e, selectedDates)
                {
                    var d = selectedDates[0];
                    if (d) {
                        d = new Date(d);
                        $('#edit-start-date').dpSetEndDate(d.addDays(0).asString());
                    }
                }
            );
        });
        ",'inline'
    );
    drupal_add_js("
    	$(document).ready(function()
		{
			DeployTimePicker('id__visitor_time_picker_layer-start','edit-start-time');
			DeployTimePicker('id__visitor_time_picker_layer-end','edit-end-time');
		});
		document.onmousedown=function(object_dom__event)
		{
			if (typeof(event)=='object')
				object_dom__element=event.srcElement;
			else if (typeof(object_dom__event)=='object')
				object_dom__element=object_dom__event.target;
			HideTimePicker(1,object_dom__element,'css__time_picker_layer');
		}
		",'inline'
    );
	//@karp - set condition!

	$simpleArr=$form['originTreeStructure']['#value'];
//@karp: $bShowOriginChangeLink=true; - for future use.
    $bShowOriginChangeLink=true;
	$output .= wssr_adjust_change_origin($simpleArr['default']['property'], $simpleArr['default']['address'],$bShowOriginChangeLink, 'wssr_hvac_form');

    wssr_add_js_property_tree($simpleArr);

    $output .= '<div class="css__form_wrapper">';

    $review=drupal_render($form['review']);

    $output .= drupal_render($form);
    $output .= '</div><div class="css__bottom_border">&nbsp;</div><div class="submit"><div class="css__padding_1">&nbsp;</div><div class="css__padding_2">'.$review.'</div></div>';
    $output .= '</div>';
    return $output;
}