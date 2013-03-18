<div id="review_single_sr" class="sr_form css_div__review_details css_div__review_single">
<?
	$array__fitlering_option=array();
	$string__search_type="";
    if (intval($_GET['property_id'])!==0)
    	$array__fitlering_option[]='property_id='.$_GET['property_id'];
    if (intval($_GET['type_id'])!==0)
    	$array__fitlering_option[]='type_id='.$_GET['type_id'];
    if (intval($_GET['status_id'])!==0)
    	$array__fitlering_option[]='status_id='.$_GET['status_id'];
    if ($_GET['searchType'])
    	$string__search_type=$_GET['searchType'];
	
    if (count($array__fitlering_option)>0)
		$string__fitlering_options='?'.implode('&',$array__fitlering_option);
	
	
    $params=wssr_get_field_params(wssr_load_field_params('wssr_review_details_form'), 'wssr_review_details_form');

            /*wssr_render_string('Created Date', $sr['srDetails']['createdDate']);
            wssr_render_string('Modified Date', $sr['srDetails']['modifiedDate']);*/


            //array('Space', $sr['srDetails']['space']['id']),

        /*
         * Conference Center, Freight Elevator - Reservation Information
         * Service Request History
         * Exit polls (Freight Elevator)
         *
         *  ..should render blocks with tables, all data with single template * n by page
         *
         */
    if (!$sr['srDetails']['id'])
    {
    	$string_html__container=
			'<div class="css__form_container">
	       	<div class="css__form_wrapper">
	       		<div class="css_div__error-id_not_found"><h2>'.str_replace('_id_','<span>'.$sr['requested_sr_id'].'</span>',$params['id_not_found']['#title']).'</h2></div>
			</div>
			<div class="css__bottom_border">&nbsp;</div>
			<div class="submit">
				<div class="css__padding_1">&nbsp;</div>
				<div class="css__padding_2 css__back"><input type="button" class="form-submit" value="Back" id="edit-back" onclick="location.href=\''.base_path().'wsservicerequest/review/'.$string__search_type.$string__fitlering_options.'\'"></div>
			</div>';
	
		echo $string_html__container;
    }
    else
    {
    	

	$array__table_row=array();
	
	$array__common_information=array();
	$array__common_information[]=array('title'=>"ID#",'value'=>$sr['srDetails']['id']);
	$array__common_information[]=array('title'=>"Property name",'value'=>$sr['srDetails']['property_name']);
	$array__common_information[]=array('title'=>"Type",'value'=>$sr['srDetails']['type']);
// dublicate, neech check!!!!! already on 
    if($sr['srDetails']['location_details'] && $sr['srDetails']['shared_resource_flag']==='Y')
        $array__common_information[]=array('title'=>"Resource Name",'value'=>$sr['srDetails']['location_details']);
	$array__common_information[]=array('title'=>"Status",'value'=>$sr['srDetails']['status']);
	$array__common_information[]=array('title'=>"Next Step",'value'=>$sr['srDetails']['nextStep']);
	$array__common_information[]=array('title'=>"To Be Performed By",'value'=>$sr['srDetails']['performed_by']);
	
	if ($sr['completion'])
	{
		$array__completion_information=array();
		$array__completion_information[]=array('title'=>"Completed by",'value'=>$sr['completion']['completed_by']['first'].' '.$sr['completion']['completed_by']['last']);
		$array__completion_information[]=array('title'=>"Work Summary",'value'=>$sr['completion']['summary']);
	}

    $initiator = $sr['history'][0]['user'];
	$array__create_information=array();
    $phone = $sr['srDetails']['initiated_by']['phone']?' ('.$sr['srDetails']['initiated_by']['phone'].')':'';
    $array__create_information[]=array('title'=>"Initiated by",'value'=>'<a href="mailto:'.$sr['srDetails']['initiated_by']['email'].'">'.$sr['srDetails']['initiated_by']['first'].' '.$sr['srDetails']['initiated_by']['last'].'</a>'.$phone);

    if($sr['hvac']){
        $fromDate = ws_parse_time($sr['hvac']['FromDate']);
        $toDate = ws_parse_time($sr['hvac']['ToDate']);
        $array__create_information[]=array('title'=>"From Date",'value'=>$fromDate['date'].' at '.$fromDate['time12']);
        $array__create_information[]=array('title'=>"To date",'value'=>$toDate['date'].' at '.$toDate['time12']);
    }

    if ($sr['srDetails']['created_for']['entity_description'])
		$array__create_information[]=array('title'=>"For",'value'=>$sr['srDetails']['created_for']['entity_description']);
	if ($sr['srDetails']['on_behalf_of']['first'])
		$array__create_information[]=array('title'=>"On Behalf Of",'value'=>$sr['srDetails']['on_behalf_of']['first'].' '.$sr['srDetails']['on_behalf_of']['last']);
	$array__create_information[]=array('title'=>"Division",'value'=>$sr['srDetails']['division']);
	$array__create_information[]=array('title'=>"Cost Center Id",'value'=>$sr['srDetails']['cost_center']);
	
	$array__location_description=array();
	$array__location_description[]=array('title'=>"Building",'value'=>$sr['srDetails']['space']['building']);
	$array__location_description[]=array('title'=>"Block",'value'=>$sr['srDetails']['space']['block']);
	$array__location_description[]=array('title'=>"Floor",'value'=>$sr['srDetails']['space']['floor']);
	$array__location_description[]=array('title'=>"Suite/Space",'value'=>$sr['srDetails']['space']['id']);
	
	
	
	$array__table_row[]=
			'<tr>
				<td class="css__left_column">
     				<div id="review_common" class="css_div__left_column">'.WSSR_RenderReviewTableBlock($params['common_information']['#title'],$array__common_information).'</div>
       			</td>
				<td class="css__right_column">
       				<div id="completion" class="css_div__right_column">'.WSSR_RenderReviewTableBlock($params['completition_information']['#title'],$array__completion_information).'</div>
       			</td>
       		</tr>
       		<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	
	
	if ($sr['srDetails']['description'])
		$array__table_row[]='<tr><td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableDescription($params['brief_description']['#title'],nl2br($sr['srDetails']['description'])).'</td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	
	if ($sr['srDetails']['location_details'] || $sr['srDetails']['upon_arrival_ask_for'])
	{
		$array__temp=array();
		if ($sr['srDetails']['shared_resource_flag']!=='Y')
			$array__temp[]=array('title'=>"Location Details",'value'=>$sr['srDetails']['location_details']);
		$array__temp[]=array('title'=>"Upon Arrival Ask For",'value'=>$sr['srDetails']['upon_arrival_ask_for']);
		$array__table_row[]='<tr><td class="css__left_column" colspan="2">'.WSSR_RenderReviewTableBlock($params['additional_information']['#title'],$array__temp).'</td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}
	
	if (true)
	{
		$array__table_row[]=
			'<tr>
				<td class="css__left_column">
     				<div id="create_info" class="css_div__left_column">'.WSSR_RenderReviewTableBlock($params['create_information']['#title'],$array__create_information).'</div>
       			</td>
				<td class="css__right_column">
       				<div class="css_div__right_column">'.WSSR_RenderReviewTableBlock($params['location_description']['#title'],$array__location_description).'</div>
       			</td>
       		</tr>'.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}
	
	
	if ($sr['access_card'])
	{
		$array__temp_result=array();
		$array__temp_result[]=implode(' ',$sr['access_card']['user']['name']);
		$array__temp_result[]=$sr['access_card']['user']['entity']['name'];
		
		if ($sr['access_card']['space_summary']['space_hierarchy']['block'])
			$array__temp_result[]='Block '.$sr['access_card']['space_summary']['space_hierarchy']['block'];
		if ($sr['access_card']['space_summary']['space_hierarchy']['floor'])
			$array__temp_result[]='Floor '.$sr['access_card']['space_summary']['space_hierarchy']['block'];
		if ($sr['access_card']['space_summary']['description'])
			$array__temp_result[]='Unit '.$sr['access_card']['space_summary']['description'];
		
		$string__building_pass_for=implode(', ',$array__temp_result);
		
		$array__access_card=array();
		$array__access_card[]=array('title'=>"Issue Building Pass for",'value'=>$string__building_pass_for);
		$array__access_card[]=array('title'=>"Phone",'value'=>$sr['access_card']['user']['phone']['number']);
		$array__access_card[]=array('title'=>"Email",'value'=>$sr['access_card']['user']['email']);
		$array__access_card[]=array('title'=>"Building Access",'value'=>$sr['access_card']['building_access']);
		$array__access_card[]=array('title'=>"Building Access Privileges",'value'=>$sr['access_card']['building_access_privileges']);
		$array__access_card[]=array('title'=>"Workspeed Privileges",'value'=>$sr['access_card']['workspeed_privileges']==='yes'?'Host Visitors':'');
		$array__access_card[]=array('title'=>"Access Hours",'value'=>$sr['access_card']['access_hours']==='ROUND_THE_CLOCK'?"24 hours / 7 days a week":$sr['access_card']['access_hours']);
		$array__access_card[]=array('title'=>"Additional Info",'value'=>$sr['access_card']['additional_info']);
		$array__access_card[]=array('title'=>"Building Pass#",'value'=>$sr['access_card']['badge_numger']);
		

		$array__table_row[]=
			'<tr>
				<td class="css__left_column">
     				<div id="access_card" class="css_div__left_column">'.WSSR_RenderReviewTableBlock($params['access_card']['#title'],$array__access_card).'</div>
       			</td>
				<td class="css__right_column">
       				<div class="css_div__right_column">'.'</div>
       			</td>
       		</tr>'.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}
	
	if ($sr['reservation'])
		$array__table_row[]='<tr><td class="css__left_column" colspan="2"><div id="reservation">'
			.WSSR_RenderReviewTableGrid($params['reservation_information']['#title'],array('Date', 'Time'),wssr_flatten_reservation($sr['reservation'])).'</div></td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	
    
	if ($sr['history'])
	{
/*		$array__sr_history=array();
		$array__sr_history[]=array('title'=>"Initiated by",'value'=>'<a href="mailto:'.$sr['srDetails']['initiated_by']['email'].'">'.$sr['srDetails']['initiated_by']['first'].' '.$sr['srDetails']['initiated_by']['last'].'</a>');
		$array__sr_history[]=array('title'=>"For",'value'=>'<a href="mailto:'.$sr['srDetails']['created_for']['email'].'">'.$sr['srDetails']['created_for']['first'].' '.$sr['srDetails']['created_for']['last'].'</a>');
		
		$array__table_row[]=
			'<tr>
				<td class="css__left_column">
     				<div id="history" class="css_div__left_column">'.WSSR_RenderReviewTableBlock($params['sr_history_summary']['#title'],$array__sr_history).'</div>
       			</td>
				<td class="css__right_column">
       				<div class="css_div__right_column">'.'</div>
       			</td>
       		</tr>'.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';*/
		$array__table_row[]='<tr><td class="css__left_column" colspan="2"><div id="review_history">'
			.WSSR_RenderReviewTableGrid($params['sr_history']['#title'],array('Date','Time','Action','Performed By','Company','Status'),wssr_flatten_history_array($sr['history'])).'</div></td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}
	
    if (count($sr['sr_comment_list'])>0)
	{
		$array__table_row[]='<tr><td class="css__left_column" colspan="2"><div id="review_history">'
			.WSSR_RenderReviewTableGrid($params['sr_comments']['#title'],array('Date','Time','Performed By','Text'),$sr['sr_comment_list'],array(0=>'78px',1=>'60px',2=>'136px')).'</div></td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}
	
	
    
    
/*
 * wrong array key
	if ($sr['sr_history'])
	{
		$array__table_row[]='<tr><td class="css__left_column" colspan="2"><div id="sr_history">'
			.WSSR_RenderReviewTableGrid($params['sr_history']['#title'],array('Date','Time','Action','Performed By','Company','Status'),wssr_flatten_history_array($sr['sr_history'])).'</div></td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}*/
	
/*
	Temporary disabled
	if ($sr['users'])
	{
		$array__user=array();
		foreach($sr['users'] as $key=>$row)
        	$array__user[]=array($row['first'],$row['last']);
    
    
		$array__table_row[]='<tr><td class="css__left_column" colspan="2"><div id="users">'
			.WSSR_RenderReviewTableGrid($params['visitors']['#title'],array('First Name','Last Name'),$array__user).'</div></td></tr>'
			.'<tr><td colspan="2"><div class="css_div__h_separator-blank"></div></td></tr>';
	}
*/
	
	if ($sr['attachments'])
	{
		$array__document=array();
		foreach ($sr['attachments'] as $row)
			$array__document[]=array('url'=>base_path().'wsservicerequest/getdocument/'.$row['id'],'title'=>$row['title']);

		$array__table_row[]=
			'<tr>
				<td class="css__left_column">
     				<div id="related-documents" class="css_div__left_column">'.WSSR_RenderReviewTableDocumentBlock($params['documents']['#title'],$array__document).'</div>
       			</td>
				<td class="css__right_column">
       				<div class="css_div__right_column">'.'</div>
       			</td>
       		</tr>';
	}
	
	
	$string_html__container=
		'<div class="css__form_container">
       	<div class="css__form_wrapper">
       		<table class="css__fields" cellpadding="0" cellspacing="0">
       			'.implode('',$array__table_row).'
			</table>
		</div>
		<div class="css__bottom_border">&nbsp;</div>
		<div class="submit">
			<div class="css__padding_1">&nbsp;</div>
			<div class="css__padding_2 css__back"><input type="button" class="form-submit" value="Back" id="edit-back" onclick="location.href=\''.base_path().'wsservicerequest/review/'.$string__search_type.$string__fitlering_options.'\'"></div>
			</div>
		</div>';

	echo $string_html__container;

    }
?>
</div>
