<?php

function wssr_visitor_form(&$form_state){
    //wssr_set_sr_title(__FUNCTION__, 'Invite guest');
    wssr_set_admin_form_title(__FUNCTION__);
    return wssr_process_form('_'.__FUNCTION__, &$form_state);
}

function _wssr_visitor_form(&$form_state){
    wssr_patch_visitor_values($form_state);
    global $user;
    file_prefix('visitor_');
    inherited('inherit_sr_visitor');
    ws_get_tz_array();

	$params=wssr_get_field_params(wssr_load_field_params('wssr_visitor_form'), 'wssr_visitor_form');

	if (($form_state['storage']['page'] == 'review') || ($form_state['storage']['page'] == 'confirm'))
    {
    	$form = _wssr_visitor_form_summary($form_state);
        $form['is_summary'] = array('#type' => 'hidden', '#value' => 'true', '#disabled'=>true);
        return $form;
    }
	
	$originTree=ws_get_filtered_user_building_structure('no_common_spaces', 'visitor');

    if (is_ws_error())
    	return ws_get_ws_info_error_form(null, $form_state, 'visitor',get_last_ws_error_description());
	if (empty($originTree) && $user->uid!=1 )
		return noaccess(null, $form_state, 'wssr_visitor_form');
    
	$form['#attributes']=array('class'=>'sr_form sr_form_visitor sr_form_creation', 'onsubmit'=>'return validate_visitor(this);');
	
	$originDefaults = wssr_get_origin_defaults($form_state);

	if (empty($originDefaults['suiteId'])) {
		$originSimpleArr=wssr_full_tree_simple($originTree, get_user_space_first());
	} else {
	    $originSimpleArr=wssr_full_tree_simple($originTree, $originDefaults['suiteId']);
	}
	
	//@karp - set real defaults from $originSimpleArr
	//we can use it later for rendering !!!
	//it's better to use form values
	//@karp: ??? do we need it?
    //$form['origin'] = array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['property']);
	//^^

	//$propertyId = $originSimpleArr['default']['property_id'];
	
    $form['origin_property']=array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['property']);
	$form['origin_property_id']=array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['property_id']);
    $form['origin_address']=array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['address']);
    $form['origin_suite']=array('#type'=> 'hidden', '#default_value'=>$originSimpleArr['default']['suiteId']);
	$form['originTreeStructure']=array('#type'=> 'value', '#value'=>$originSimpleArr);

	$requestList=getCurrentUserRequestListTree($originSimpleArr['default']['property_id'],'visitor');
    if (is_ws_error())
    	return ws_get_ws_info_error_form(null, $form_state, 'visitor',get_last_ws_error_description());
	if (empty($requestList) && $user->uid!=1 )
		return noaccess(null, $form_state, 'wssr_visitor_form');
	
	$visitors = $form_state['post']['visitor'];
    if (!$visitors){
        if($form_state['clicked_button']['#id'] == 'edit-review'){
            foreach($form_state['values']['visitor'] as $key=>$value)
                $visitors[]=array('first_name'=>$value['first_name'], 'last_name'=>$value['last_name'], 'email'=>$value['email'], 'company'=>$value['company']);
        } else {
    	foreach($form_state['storage']['values']['visitor'] as $key=>$value)
			$visitors[]=array('first_name'=>$value['first_name'], 'last_name'=>$value['last_name'], 'email'=>$value['email'], 'company'=>$value['company']);
    }
    }
    if (!$visitors)
    	$visitors=array(0=>array('first_name'=>'','last_name'=>'','company'=>'','email'=>''));
	
	$divisionsData = get_ws_user_divisions_list_options();
    if (is_ws_error())
    	return ws_get_ws_info_error_form(null, $form_state, 'visitor',get_last_ws_error_description());
    $tmpDivisions = $divisionsData;
    $tmpDivisions = array_shift($tmpDivisions['options']);

    $form['start_date']=array('#type'=>'textfield','#title'=>$params['start_date']['#title'],'#size'=>'','#required'=>true, '#attributes'=>array('class'=>'date-pick'));
	$form['start_time']=array('#type'=>'textfield','#title'=>$params['start_time']['#title'],'#size'=>'','#required'=>true, '#attributes'=>array('class'=>'time-pick','onclick'=>"ShowTimePicker(this,'id__visitor_time_picker_layer','edit-start-time')"));
	$form['time_divider'] = array('#value' => '<div class="css__time_separator">&nbsp;</div>');

   	$form['visitor_wrapper_start'] = array('#type'=>'markup', '#value'=>'<div id="visitor_data">');		

   	$string__visitor_company='';
   	$string__visitor_company='';
   	if ($params['visitor_company']['#access']!==0)
   		$string__visitor_company='<div class="css__field_wrapper"><div class="form-item"><label>'.$params['visitor_company']['#title'].':</label></div></div>';
   	if ($params['visitor_email']['#access']!==0)
   		$string__visitor_email='<div class="css__field_wrapper"><div class="form-item"><label>'.$params['visitor_email']['#title'].':</label></div></div>';
   	
	$form['visitor_field_titles'] = array('#type'=>'markup', '#value'=>'
   		<table cellspacing="0" cellpadding="0" class="css__title_group">
   			<tr>
   				<td>
   					<div class="css__group_container">
   						<div class="css__field_wrapper"><div class="form-item"><label>'.$params['visitor_first_name']['#title'].':<span title="This field is required." class="form-required">*</span></label></div></div>
						<div class="css__field_wrapper"><div class="form-item"><label>'.$params['visitor_last_name']['#title'].':<span title="This field is required." class="form-required">*</span></label></div></div>
						'.$string__visitor_company.'
						'.$string__visitor_email.'
					</div>
				</td><td style="width:40px;">&nbsp;</td>
			</tr></table>');
   	
   	$form['visitor']=array('#tree'=>true);

   	drupal_add_js("
   		var string__visitor_title={};
	    string__visitor_title['first_name']='".htmlentities($params['visitor_first_name']['#title'],ENT_QUOTES)."';
	    string__visitor_title['last_name']='".htmlentities($params['visitor_last_name']['#title'],ENT_QUOTES)."';
	    string__visitor_title['company']='".htmlentities($params['visitor_company']['#title'],ENT_QUOTES)."';
	    string__visitor_title['email']='".htmlentities($params['visitor_email']['#title'],ENT_QUOTES)."';
	    	
	    var string__visitor_access={};
	    string__visitor_access['company']='".$params['visitor_company']['#access']."';
	    string__visitor_access['email']='".$params['visitor_email']['#access']."';
	    	
	    int__time_picker_minute_interval=".variable_get('int__time_picker_minute_interval','30')."
	    	
		",'inline');

   	
	$int__visitor_index=0;
	foreach($visitors as $visitor)
	{    		
		$form['visitor'][$int__visitor_index]['group_separator_start']=array('#type'=>'markup', '#value'=>'<table id="id__visitor_'.$int__visitor_index.'" class="css__group" cellspacing="0" cellpadding="0"><tr><td>'
			.'<div class="css__group_container">');
		/* !!!! RESTORED BY DRUPAL ON DELETION IF FIELDS USED INSTEAD OF MARKUP !!!!!! */
		$form['visitor'][$int__visitor_index]['first_name']=array('#type'=>'textfield','#size'=>'','#required'=>true,'#value'=>$visitor['first_name'], '#prefix'=>'<div class="css__field_wrapper">','#suffix'=>'</div>', '#tree'=>true,'#maxlength'=>35);
		$form['visitor'][$int__visitor_index]['last_name']=array('#type'=>'textfield','#size'=>'','#required'=>true,'#value'=>$visitor['last_name'],'#prefix'=>'<div class="css__field_wrapper">','#suffix'=>'</div>', '#tree'=>true,'#maxlength'=>35);
		
		$form['visitor'][$int__visitor_index]['company']=array('#type'=>'textfield','#access'=>$params['visitor_company']['#access'],'#size'=>'','#value'=>$visitor['company'],'#prefix'=>'<div class="css__field_wrapper">','#suffix'=>'</div>', '#tree'=>true,'#maxlength'=>255);
		$form['visitor'][$int__visitor_index]['email']=array('#type'=>'textfield','#access'=>$params['visitor_email']['#access'],'#size'=>'','#value'=>$visitor['email'],'#prefix'=>'<div class="css__field_wrapper last">','#suffix'=>'</div>', '#tree'=>true,'#maxlength'=>100);

		/*
		$form['visitor'][$int__visitor_index]['first_name']=array('#type'=>'markup','#value'=>'<div class="css__field_wrapper"><div class="form-item"><label class="css__block">'.$array__visitor_title['first_name'].': <span title="This field is required." class="form-required">*</span></label><input type="text" maxlength="128" name="visitor['.$int__visitor_index.'][first_name]" value="'.$visitor['first_name'].'" class="form-text required"/></div></div>');
		$form['visitor'][$int__visitor_index]['last_name']=array('#type'=>'markup','#value'=>'<div class="css__field_wrapper"><div class="form-item"><label class="css__block">'+string__visitor_title.last_name+': <span title="This field is required." class="form-required">*</span></label><input type="text" maxlength="128" name="visitor['+int__added_visitor_amount+'][last_name]" value="" class="form-text required"/></div></div>');
		$form['visitor'][$int__visitor_index]['company']=array('#type'=>'markup','#size'=>'','#value'=>$visitor['company'],'#prefix'=>'<div class="css__field_wrapper">');
		$form['visitor'][$int__visitor_index]['email']=array('#type'=>'markup','#value'=>$visitor['email'],'#prefix'=>'<div class="css__field_wrapper">');
		*/
		$form['visitor'][$int__visitor_index]['deletion_link']=array('#type'=>'markup', '#value'=>'<a class="css__deletion_link" href="javascript:RemoveVisitor('.$int__visitor_index.')"></a>');
		$form['visitor'][$int__visitor_index]['group_separator_end']=array('#type'=>'markup', '#value'=>'</div></td></tr></table>');
			
		$int__visitor_index++;
	}
	$form['visitor_wrapper_end'] = array('#type'=>'markup', '#value'=>'</div>');
    $form['visitor_add']=array('#type'=>'markup','#value'=>'<div class="submit css__no_right_padding"><div class="css__padding_1">&nbsp;</div><div class="css__padding_2"><input type="button" value="Add Visitor" id="visitor_add" onclick="add_visitor_string()"/></div></div>');
    
    $form['division']=array('#type'=>'select','#title'=>$params['division']['#title'],'#options'=>$divisionsData['options'], '#access' => $divisionsData['options']?true:false);
	$form['division_name'] = array('#type'=>'hidden', '#default_value'=>$tmpDivisions);

	$form['meetingNote']=array('#type'=>'textfield','#title'=>$params['meetingNote']['#title'],'#maxlength'=>255);
    

	$form['review']=array('#type'=>'submit','#value'=>'Submit');

    wssr_form_restore_defaults($form,$form_state);
    
//    $field_params = wssr_get_field_params('wssr_visitor_form', array());
    array_walk($form, 'wssr_attach_form_attributes', $params);

    return $form;
}

function _wssr_visitor_form_summary(&$form_state)
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
	
    $wrapper = wssr_summary_wrapper($form_state, 'wssr_visitor_form');
	$vals = $form_state['storage']['values'];
	$visitors = $vals['visitor'];
	
    $form['#attributes'] = array('class' => 'sr_form '.$wrapper['class']);
    
    $form['form_container_prefix']=array('#type'=>'markup','#value'=>'<div class="css__form_container">');
    $form['before'] = $wrapper['before'];

    $params = wssr_get_field_params(wssr_load_field_params('wssr_visitor_form'), 'wssr_visitor_form');

	//@karp: to set to real value
	$requestList = getCurrentUserRequestListTree($vals['origin_property_id'],'visitor');
	$requestList = get_request_list_as_flatlist($requestList);
	$requestName=$requestList[$vals['sr_category']];
	
	if (!$requestName)
		$requestName='Visitor';
	
    if($params['sr_category']['#access'])
    	$array__create_information[]=array('title'=>$params['sr_category']['#title'],'value'=>$requestName);

    if($params['requested_by']['#access'])
    	$array__create_information[]=array('title'=>$params['requested_by']['#title'],'value'=>$_SESSION['user_display_name']);    
    
    $array__create_information[]=array('title'=>$params['visiting_on']['#title'],'value'=>$vals['start_date'].' at '.$vals['start_time']);

    if($params['tenant']['#access'])
    	$array__location_description[]=array('title'=>$params['tenant']['#title'],'value'=>$_SESSION['wsUserInfo']['entity']['displayName']);

    if ($vals['division'] && $vals['division']!=-1)
	{
		$divisionsData = get_ws_user_divisions_list_options();
		//if ($divisionsData===false || get_last_ws_error_description())

		$divisionName = $divisionsData['options'][$vals['division']];
		$array__create_information[]=array('title'=>$params['division']['#title'],'value'=>$divisionName);
	}

	$array__create_information=array_merge($array__create_information,$array__location_description);
	
	/*	$form['main_info']=array('#type'=>'markup','#value'=>'<tr>
		<td class="css__left_column"><div class="css_div__left_column">'.WSSR_RenderReviewTableBlock("Create Information",$array__create_information).'</div></td>
		<td class="css__right_column"><div class="css_div__right_column">'.WSSR_RenderReviewTableBlock("Location Description",$array__location_description).'</div></td></tr>');
*/
//	$form['main_info']=array('#type'=>'markup','#value'=>'<tr>
//		<td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableBlock("Create Information",$array__create_information).'</td></tr>');
	$form['main_info']=array('#type'=>'markup','#value'=>'<tr>
		<td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableGridSummary("Create Information",$array__create_information,array('150px')).'</td></tr>');
	
//	prepare visitors titles array
    $array__title=array($params['visitor_first_name']['#title'],$params['visitor_last_name']['#title']);
    if ($params['visitor_company']['#access'])
    	$array__title[]=$params['visitor_company']['#title'];
    if ($params['visitor_email']['#access'])
    	$array__title[]=$params['visitor_email']['#title'];

//	prepare visitors titles array    
    $array__visitor=array();
	foreach ($visitors as $var__key=>$visitor)
    {
    	$array__visitor[$var__key][]=$visitor['first_name'];
    	$array__visitor[$var__key][]=$visitor['last_name'];
    	if ($params['visitor_company']['#access'])
    		$array__visitor[$var__key][]=$visitor['company'];
    	if ($params['visitor_email']['#access'])
    		$array__visitor[$var__key][]=$visitor['email'];
    }
    
    $form['h_separator-blank-0']=array('#type'=>'markup','#value'=>'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>');
    $form['form_visitor_container']=array('#type'=>'markup','#value'=>'<tr><td colspan="2">'.WSSR_RenderReviewTableGrid("Visitors Information",$array__title,$array__visitor).'</td></tr>');

	if ($vals['meetingNote'])
	{
		$form['h_separator-blank-1']=array('#type'=>'markup','#value'=>'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>');
		$form['form_content_comments']=array('#type'=>'markup','#value'=>
    	 	'<tr><td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableDescription($params['meetingNote']['#title'],$vals['meetingNote']).'</td></tr>');
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


function wssr_visitor_form_validate($form, &$form_state){
    if ($form_state['clicked_button']['#id'] == 'edit-review')
    {
    	/*if (strtotime($form_state['values']['start_date'].' '.$form_state['values']['start_time'])<time())
			form_set_error('start_time', "You can't select past time!");*/
			
        wssr_patch_visitor_values($form_state);
		if(empty($form_state['values']['visitor'])){
            form_set_error('first_name', 'At least one visitor should be added.');
        }
        foreach($form_state['values']['visitor'] as $visitor)
        {
            if (!$visitor['first_name'] || !$visitor['last_name'])
            {
            	form_set_error('first_name', 'Please complete the required fields.');
            }
            if ($visitor['email'] && !valid_email_address($visitor['email'])){
                form_set_error('email', 'Please input correct email address.');
            }
        }
    }

}

function wssr_patch_visitor_values(&$form_state){
    $form_state['values']['visitor'] = array();
    foreach($form_state['clicked_button']['#post']['visitor'] as $visitor){
        //$empty = true;
        //foreach($visitor as $check){
            //if(trim($check)!=''){
            //   $empty = false; break;
            //}
        //}
        //if($empty){
        //    continue;
        //}
        $form_state['values']['visitor'][] = $visitor;
    }
}

function wssr_visitor_form_submit($form, &$form_state){

    if ($form_state['clicked_button']['#id'] == 'edit-review') {
        $form_state['storage']['page'] = 'review';

        // Push data to storage
        $form_state['storage']['values'] = $form_state['values'];
        $form_state['storage']['summary'] = true;
    }
    else if ($form_state['clicked_button']['#id'] == 'edit-back') {
        $form_state['rebuild'] = true;
        unset($form_state['storage']['page']);
        $form_state['storage']['restore_defaults'] = true;
    }
    else if ($form_state['clicked_button']['#id'] == 'edit-confirm') {
        $form_state['storage']['page'] = 'confirm';
        $form_state['storage']['summary'] = true;
        //some data processing before send it to main app needed.
        $data = $form_state['storage']['values'];
		$data24=date("H:i",strtotime($data['start_time']));
		$propertyId=$data['origin_property_id'];

        $meetingTime = sprintf('%s %s',$data['start_date'], $data24);

        if($data['division'] == -1)
            $data['division'] = NULL;
            
        $args = array(
            "propertyId"=> $propertyId,
            "spaceId"=> $data['origin_suite'],
            "typeId"=> "9020",
            //"entityId"=> $data['company'],
			"createdFor"=>get_ws_user_id(),
            "division"=> $data['division'],
            //"profileId"=> $profileId,
            "meetingTime"=> $meetingTime
        );


        $rawData = '';
        $visitors = $data['visitor'];
        foreach($visitors as $visitor){ 
            $rawData .= '<participant first="'.xmlspecialchars($visitor['first_name']).'" last="'.xmlspecialchars($visitor['last_name']).'"'.' email="'.xmlspecialchars($visitor['email']).'"'.' company="'.xmlspecialchars($visitor['company']).'"'.'/>';
        }
		if ($data['meetingNote'] && $data['meetingNote']!='')
			$rawData .= '<meeting meetingNote="'.xmlspecialchars($data['meetingNote']).'"></meeting>';

        $form_state['storage']['reply'] = wssr_submit_request('createVisitorRequest', $args, $rawData);
    }
    // Finally
    else {
        // We must unset 'storage' to prevent form from rebuild. http://drupal.org/node/144132
        unset ($form_state['storage']);
    }
}

function wssr_visitor_form_map()
{
	return array
		(
            //'#admin-form-title'=>'Configure invite guest form settings',, 'admin_description'=>""

            'page_title'=>array('name'=>'Tenant Services','type'=>'core', 'admin_description'=>"Invite Guest page title"),
            'origin'=>array('name'=>'Change location','type'=>'core', 'admin_description'=>"'Location tree' popup link text"),
            'origin_tree'=>array('name'=>'Location','type'=>'core', 'admin_description'=>"'Location tree' popup title"),
			'visiting_on'=>array('name'=>'Visiting On','type'=>'core', 'admin_description'=>"'Meeting Date/Time' field on the Review/Confirm page"),
            'visitor_first_name'=>array('name'=>'First Name','type'=>'core', 'admin_description'=>"Visitor's 'First Name' field title"),
            'visitor_last_name'=>array('name'=>'Last Name','type'=>'core', 'admin_description'=>"Visitor's 'Last Name' field title"),
            'start_date'=>array('name'=>'Meeting Date','type'=>'core', 'admin_description'=>"'Meeting Date' field title"),
            'start_time'=>array('name'=>'Meeting Time','type'=>'core', 'admin_description'=>"'Meeting Time' field title"),
            'no_access'=>array('name'=>'No access', 'type'=>'core', 'admin_description'=>"Message if user has no permission to access Invite Guest page"),
			'new_request_button_title'=>array('name'=>'Invite another guest', 'type'=>'core', 'admin_description'=>"Label on the 'Invite new guest' button ('Confirmation' page)"),
			'modify_request_button_title'=>array('name'=>'Modify', 'type'=>'core', 'admin_description'=>"Label on the 'Modify Invite Guest' button ('Confirmation' page, if failed)"),

            'sr_category'=>array('name'=>'SR Category','type'=>'custom', 'admin_description'=>"'SR Category' field title (Review/Confirm page)"),
			'requested_by'=>array('name'=>'Requested by','type'=>'custom', 'admin_description'=>"'Requested by user' field title (Review/Confirm page)"),
			'tenant'=>array('name'=>'Tenant','type'=>'custom', 'admin_description'=>"'Tenant Company name' field on the Review/Confirm page"),
			'visitor_company'=>array('name'=>'Company','type'=>'custom', 'admin_description'=>"Visitor's 'Company' field title"),
			'visitor_email'=>array('name'=>'Email','type'=>'custom', 'admin_description'=>"Visitor's 'Email' field title"),
			'division'=>array('name'=>'Division','type'=>'custom', 'admin_description'=>"'Division' field title"),
			'meetingNote'=>array('name'=>'Comments','type'=>'custom', 'admin_description'=>"'Comments' field title")
		);
}

function wssr_visitor_form_map_inherit(){
    return array(
        'map' => array(
            'wssr_review_visitor_form' => array(
                'visitor_first_name' => 'visitor_first_name',
                'visitor_last_name' => 'visitor_last_name',
                'visitor_company' => 'visitor_company',
            ),
        ),
        'forms' => array(
            'wssr_review_visitor_form'
        ),
    );
}

function theme_wssr_visitor_form($form)
{
    body_class('sr_visitor');

    if (isset($form['is_summary']) || isset($form['is_noaccess']))
        return drupal_render($form);

    $output = '<div class="css__form_container">';

    wssr_adjust_scripts_temp();
    //@karp - set condition!

    drupal_add_js("
        $(document).ready(function()
        {
            DeployTimePicker('id__visitor_time_picker_layer','edit-start-time');
            var shiftedTime = datetime_shift();
            init_timepicker('#edit-start-time', shiftedTime);
            init_datepicker('#edit-start-date', shiftedTime);
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

    $simpleArr=$form['originTreeStructure']['#value'];
    //@karp: $bShowOriginChangeLink=true; - for future use.
    $bShowOriginChangeLink=true;
    $output .= wssr_adjust_change_origin($simpleArr['default']['property'], $simpleArr['default']['address'],$bShowOriginChangeLink, 'wssr_visitor_form');

    wssr_add_js_property_tree($simpleArr);

    $output .=  '<div class="css__form_wrapper">';

    $review=drupal_render($form['review']);

    $output .= drupal_render($form);
    $output .= '</div><div class="css__bottom_border">&nbsp;</div><div class="submit"><div class="css__padding_1">&nbsp;</div><div class="css__padding_2">'.$review.'</div></div>';
    $output .= '</div>';
    return $output;
}

function wssr_visitor_generator(){
    ws_get_tz_array();

    wssr_adjust_scripts_temp();

    drupal_add_js("
        $(document).ready(function()
        {
            DeployTimePicker('id__visitor_time_picker_layer','edit-start-time');
            var shiftedTime = datetime_shift();
            init_timepicker('#edit-start-time', shiftedTime);
            init_datepicker('#edit-start-date', shiftedTime);
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

    $form['start_date']=array('#type'=>'textfield','#title'=>'start_date','#size'=>'','#required'=>true, '#attributes'=>array('class'=>'date-pick'));
	$form['start_time']=array('#type'=>'textfield','#title'=>'start_time','#size'=>'','#required'=>true, '#attributes'=>array('class'=>'time-pick','onclick'=>"ShowTimePicker(this,'id__visitor_time_picker_layer','edit-start-time')"));

    $form['review']=array('#type'=>'submit','#value'=>'Submit');

    return $form;
}

function wssr_visitor_generator_submit($form, &$form_state){
    set_time_limit(3600);

    $conf = array(
        'packs' => 6,

        'single' => 500,
        'double' => 250,
        'triple' => 100,

        'visitorsPerRequest' => 1,
        'addVisitorsPerRequest' => 0, //set 0 for constant visitorsPerRequest vistiors.

        'origin_property_id' => 282197, // 1543784,
        'origin_suite' => 282494, //259646709,
        'division' => -1,
    );

    $prefixes = array('Abc', 'Bcd', 'Cde', 'Def', 'Efg', 'Fgh', 'Ghi', 'Hij');

    $date = $form_state['values']['start_date'];
    $time = $form_state['values']['start_time'];

    $data = array(
        "origin_property_id"=> $conf['origin_property_id'],
        "origin_suite"=> $conf['origin_suite'],
        "typeId"=> "9020",
        "createdFor"=>get_ws_user_id(),
        "division"=> $conf['division'],
        'start_time' => $time,
        'start_date' => $date,
    );

    for($i=0; $i<$conf['packs']; $i++){
        echo "Init ".($i+1)." requests from ".$conf['packs']."<br>";
        $triple = $prefixes[$i];//ucfirst(rand_str(3));
        $double = substr($triple, 0, 2);
        $single = $triple[0];

        for($c=0; $c<$conf['single']; $c++){
            if (($c+1)%100 == 0){
                echo "Created ".(($i+1)*$conf['single']+$c+1)." stack from ".($conf['packs']*$conf['single'])."<br>";
                flush();
            }
            if($c < $conf['single'] - $conf['double'] - $conf['triple'])
                $prefix = $single;
            else if ($c < $conf['single'] - $conf['triple'])
                $prefix = $double;
            else
                $prefix = $triple;

            $data['visitor'] = array();
            for($x=0; $x<$conf['visitorsPerRequest']+rand(0,$conf['addVisitorsPerRequest']); $x++){
                $data['visitor'][] = array(
                    'first_name' => $prefix.rand_str(7),
                    'last_name' => $prefix.rand_str(5),
                    'company' => 'generator'
                );
            }
            $generator_form_state['storage']['values'] = $data;
            visitor_generator_confirm($generator_form_state);
        }
    }
    die('Task completed. Refresh page to proceed.');
}

function rand_str($length = 8){
  $chars = 'abcdefghijklmnopqrstuvwxyz';
  $numChars = strlen($chars);
  $string = '';
  for ($i = 0; $i < $length; $i++) {
    $string .= substr($chars, rand(1, $numChars) - 1, 1);
  }
  return $string;
}

function visitor_generator_confirm($form_state){
    $data = $form_state['storage']['values'];
    $data24=date("H:i",strtotime($data['start_time']));
    $propertyId=$data['origin_property_id'];

    $meetingTime = sprintf('%s %s',$data['start_date'], $data24);

    if($data['division'] == -1)
        $data['division'] = NULL;

    $args = array(
        "propertyId"=> $propertyId,
        "spaceId"=> $data['origin_suite'],
        "typeId"=> "9020",
        "createdFor"=>get_ws_user_id(),
        "division"=> $data['division'],
        "meetingTime"=> $meetingTime
    );


    $rawData = '';
    $visitors = $data['visitor'];
    foreach($visitors as $visitor){
        $rawData .= '<participant first="'.xmlspecialchars($visitor['first_name']).'" last="'.xmlspecialchars($visitor['last_name']).'"'.' email="'.xmlspecialchars($visitor['email']).'"'.' company="'.xmlspecialchars($visitor['company']).'"'.'/>';
    }
    if ($data['meetingNote'] && $data['meetingNote']!='')
        $rawData .= '<meeting meetingNote="'.xmlspecialchars($data['meetingNote']).'"></meeting>';

    $form_state['storage']['reply'] = wssr_submit_request('createVisitorRequest', $args, $rawData);
}