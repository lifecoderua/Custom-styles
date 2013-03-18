<?php
/*
 * $request: array
 *  page
 *  rp
 *  sortname
 *  sortorder
 *  [query] //user search data
 *  qtype //search column for user search
 *
 *  ? propertyId
 *  requestType
 *  requestStatus
 */
/* COMMON SR part -> */
function wssr_review_request_data($request){
    //here we should receive data - with cache, main app or both
    $data['params'] = array();
    $filterMode=empty($request['filterMode']) ? 'my_requests' : $request['filterMode'];
    //if(empty($srlistMode))
    //    $srlistMode='my_requests';
    //$srlistMode = 'my_requests';
    $resetPager=$request['resetPager'];
    if ($resetPager=='resetPager') {
        //unset($request['firstId']);
        //unset($request['lastId']);
        //unset($request['move_direction']);
        unset($request['page']);
    }
    if($request['reloadType'] == "true"){
        unset($request['requestType']);
    }
    $srlist=wssr_review_get_sr_list($request, $filterMode);

//	can be changed to object with property of CURL error message
    if (!$srlist)
    	return 'null';
    $data = wssr_flatten_sr_list($srlist, $request['rp']);
    //create links
    
    foreach($data['rows'] as $key=>$val)
    {
//		$data['rows'][$key]['cell'][0] = l($data['rows'][$key]['cell'][0], 'wsservicerequest/review/single/'.$data['rows'][$key]['cell'][0], array());//'<a href="'.$data[$key]['cell'][0].'">'.$data[$key]['cell'][0].'</a>';

    	if (array_search($val['category_id'],get_unsupported_srcat_ids('hide_details'))===false)
        $data['rows'][$key]['cell'][0] = '<a href="javascript:void(0)" onclick=ViewSRDetails("'.base_path().'wsservicerequest/review/single/'.$data['rows'][$key]['cell'][0].'")>'.$data['rows'][$key]['cell'][0].'</a>';
        else
        	$data['rows'][$key]['cell'][0] = '<span>'.$data['rows'][$key]['cell'][0].'</span>';
        
        $data['rows'][$key]['cell'][5]='&nbsp;';
    }
    //attach status
    //$data['params']['status'] = wssr_review_status_list($request['property_id']);
    if($request['reloadType'] == "true"){
        $userId = get_ws_user_id();
        $data['params']['type'] = '';
        //$data['params']['type'] = wssr_review_type_list($userId, $request['propertyId']);
        foreach(wssr_review_type_list($userId, $request['propertyId'], $filterMode) as $key=>$val){
            $data['params']['type'] .= "<option value='$key'>$val</option>";
        }
    }
    if (!empty($request['page']))
        $data['page']=$request['page'];
    if ($resetPager=='resetPager')
        $data['page']=1;

    $data['params']['gridHeaders'] = wssr_get_list_headers($srlist['list']);

    return json_encode($data);
}

function wssr_review_request_sr($id){
    //load from cache/main app full data about single request
    $request = wssr_review_get_single_sr($id);

    $data = array(
        'id' => $id,
        'type' => $request['type'],
        'status' => $request['status'],
        'history'=> array(
            'h_id' => $request['h_id'],
            'timestamp' => $request['timestamp'],
            'action' => $request['action'],
            'performed by' => $request['performed by'],
            'company' => $request['company'],
        ),
    );

    return $data;
}

/*
 * Creates option lists from main app data.
 */
function wssr_review_property_list(){
	$props=get_user_properties();
	if (empty($props) || !is_array($props))
		return null;
    return $props;
}

function wssr_review_status_list($propertyId){
    /*  
    $data = wssr_data_request('reviewStatusList', array('property_id' => $propertyId));
	if (is_ws_error())
		return false;
    $data = $data['Content'];
    foreach($data['PropertiesList'][0][SXML_CHILDLIST]['Property'] as $cData){
        $id = $cData[SXML_CHILDLIST]['id'];
        $name = $cData['Description'];
        $result[$id] = $name;
    }

    return $result;
     */

    return array('all' => 'Any', '1' => 'New', '2' => 'Pending', '3' => 'Completed', '4' => 'Closed', /*'5' => 'Archived'*/ '9' => 'OnHold');
}

function wssr_review_type_list($userId, $propertyId, $srlistMode=null){
    //@karp: $userId is for cache retrieving
    $data = wssr_review_get_type_list($propertyId, $srlistMode);
    $result['all'] = 'Any';
    if (empty($data))
        return $result;
    foreach($data as $id=>$val){
        $result[$id] = $val['name'].' ('.$val['count'].')';
    }
    return $result;
}

function wssr_view_created_sr(){
    $srStatus = explode(',', $_POST['requestStatus']);
    $request = array(
        'page' => $_POST['page'],
        'rp' => $_POST['rp'],
        'sortname' => $_POST['sortname'],
        'sortorder' => $_POST['sortorder'],
        'query' => $_POST['query'],
        'qtype' => $_POST['qtype'],

        'propertyId' => $_POST['propertyId'],
        'requestType' => $_POST['requestType'],
        'reloadType' => $_POST['reloadType'],
        'requestStatus' => $srStatus[0], /* explode(',', $_POST['requestStatus']), for array push */ //$_POST['requestStatus'],

        'firstId' => $_POST['firstId'],
        'lastId' => $_POST['lastId'],
        'resetPager' => $_POST['resetPager'],

        'move_direction' => $_POST['reviewPage'] < $_POST['page'] ? 'forward' : 'backward',

        'filterMode' => $_POST['filterMode'],
    );
    $reply = wssr_review_request_data($request);

    echo $reply;
    die;
}

function wssr_review($srlistMode = 'my')
{
    if($srlistMode != 'all')
        $srlistMode='my';
    $params=wssr_get_field_params(wssr_load_field_params('wssr_review_form'), 'wssr_review_form');

    drupal_add_js(drupal_get_path('module', 'wsservicerequest').'/wsservicerequest.js');
    general_add_css('flexigrid/css/flexigrid/flexigrid.css');
    general_add_js('flexigrid/flexigrid.js');
    drupal_add_js('
    function test(){
        /*
        console.log("test function on button trigger");
        */
    }
    $(document).ready(function()
    {
    	
    	var int__sr_review_form_width=parseInt($("#wssr-review-form").width());
    	var object__sr_review_table_configuration=
    	{
    		int__id_column:80,
    		int__type_column:int__sr_review_form_width-(80+70+164+130+16),
    		int__status_column:70,
    		int__originator_column:164,
    		int__created_column:130
		};
    		
        // Init grid
        $("#srReviewControl").flexigrid
			(
			{
			url: "'.base_path().'wsservicerequest/ajax/sr_view",
			dataType: "json",
			colModel : [
				{display: "'.$params['id']['#title'].'", name : "id", width : object__sr_review_table_configuration.int__id_column, sortable : false, align: "center"},
				{display: "'.$params['type']['#title'].'", name : "type", width : object__sr_review_table_configuration.int__type_column, sortable : false, align: "center"},
				{display: "'.$params['status']['#title'].'", name : "status", width : object__sr_review_table_configuration.int__status_column, sortable : false, align: "center"},
				{display: "'.$params['originator']['#title'].'", name : "originator", width : object__sr_review_table_configuration.int__originator_column, sortable : false, align: "center"/*, hide: true*/},
				{display: "'.$params['created']['#title'].'", name : "created", width : object__sr_review_table_configuration.int__created_column, sortable : false, align: "center"},
				{display: "", name : "scrollbar_separator", sortable : false, string__css_class_name:"css_td__last"}
				],
			/*searchitems : [
				{display: "ID #", name : "id"},
				{display: "Originator", name : "originator", isdefault: true}
				],*/
			nomsg:"'.$params['no_items']['#title'].'",
			errormsg: "'.$params['errormsg']['#title'].'",
			procmsg: "'.$params['procmsg']['#title'].'",
			showToggleBtn:false,
			sortname: "id",
			sortorder: "asc",
			usepager: true,
			singleSelect: true,
			title: "Service requests",
			useRp: true,
			rp: '.$params['items_per_page']['#config']['value'].',
			showTableToggleBtn: false,
            onSubmit: addSrReviewData,
			preProcess: getSrReviewData,
			
			height: "auto",/*662, /*610 + 2x26 for up to 2 property headers*/
			resizable: false,
			postprocess: set_grid_headers,
			pagestat: "Displaying <span> {from} - {to} </span> of {total} Service Requests"
			}
			);

			$("#edit-property").change(function(){
			    resetPager="resetPager";
			    reloadType = true;
                reloadSrReviewControl();
            });

            $("#edit-type").change(function(){
                resetPager="resetPager";
                reloadSrReviewControl();
            });

            $("#edit-status").change(function(){
                resetPager="resetPager";
                reloadSrReviewControl();
            });
    });

    /*
     * Manual control reload
     */
    var reloadType = false;

    function reloadSrReviewControl(){
        $("#srReviewControl").flexReload();
    }

    var firstId = null;
    var lastId = null;
    var reviewPage = 1;
    var resetPager = null;
    var gridHeaders = null;

    function addSrReviewData(){
        controlsDisable(["#edit-property","#edit-type","#edit-status"]);
        extra = [
          {name: "propertyId", value: $("#edit-property").val()},
          {name: "requestType", value: $("#edit-type").val()},
          {name: "reloadType", value: reloadType},
          {name: "requestStatus", value: $("#edit-status").val()},
          {name: "startId", value: firstId},
          {name: "lastId", value: lastId},
          {name: "resetPager", value: resetPager},
          {name: "reviewPage", value: reviewPage},
          {name: "filterMode", value: filterMode}
        ];
        reloadType = false;
        $("#srReviewControl").flexOptions({params: extra});
        return true;
    }

    function getSrReviewData(data)
    {
        controlsEnable(["#edit-property","#edit-type","#edit-status"]);
    	if (!data)
    		return null;
        resetPager=null;
        var p = data.params;
        if (p.type){
            $("#edit-type").empty();
            $("#edit-type").append( $(p.type) );
        }
        if (p.page)
            reviewPage = p.page;
        if (p.gridHeaders)
                gridHeaders = p.gridHeaders;
        if(data.rows != null)
        {
            firstId = p.firstId;
            lastId = p.lastId;
        }
        
        if (p.filterMode)
            filterMode = p.filterMode;
        return data;
    }
    ', 'inline');

    wssr_history_js(6);
    return drupal_get_form('wssr_review_form', array('srlistMode'=>$srlistMode));
}

function ws_filter_properties($propList, $filterList){
    $result = array();
    foreach($filterList as $propId)
        $result[$propId] = $propList[$propId];

    return $result;
}

function wssr_review_form($form, $data){
    $params=wssr_get_field_params(wssr_load_field_params('wssr_review_form'), 'wssr_review_form');
    $srlistMode = $data['srlistMode'];
    if($srlistMode == 'all')
        drupal_set_title($params['page_title_all']['#title']);
    else
    drupal_set_title($params['page_title']['#title']);
    switch($data['srlistMode']){
        case 'all':
            $srlistMode = 'all_requests';
            break;
        default:
            $srlistMode = 'my_requests';
            break;
    }

    //$srlistMode = 'my_requests';
    drupal_add_js('var filterMode = "'.$srlistMode.'"', 'inline');
    $propList = ($srlistMode == 'all_requests') ? ws_filter_properties(get_user_properties(), get_user_properties_with_attribute(array('sr_view_all','sr_view_for_route_to_entity'))) : get_user_properties();

    $userId = get_ws_user_id();
    if (empty($propList) || empty($userId)) {
        //process empty page
//        return null;
        return noaccess(null, $stub_var, 'wssr_review_form');
    }
    $pId=null;
    $propOptions = array();
    //$check_for_pre_default=null;

    //$var__selected_property_id=intval($_GET['property_id']);
    if ($srlistMode != 'all_requests' && count($propList)>1) {
        $propOptions['all']='All';
    }    
    $tmpPropOptions=null;
    foreach($propList as $pKey=>$pVal) {
        $tmpPropOptions[$pKey]=$pVal['property_name'];
    }
    if (!empty($tmpPropOptions) && count($tmpPropOptions)>0) {
        asort($tmpPropOptions);
    $propOptions = $propOptions + $tmpPropOptions;
    }
    $pId=((!empty($_GET['property_id'])) && array_key_exists($_GET['property_id'],$propOptions))?$_GET['property_id'] : null;
    //$pId=($check_for_pre_default) ? $check_for_pre_default :
    if (empty($pId))
        $pId=current(array_keys($propOptions));
    
    //@karp : just check if we need to keep this variable cross pages - $check_for_pre_default
    $typeList = wssr_review_type_list($userId, $pId, $srlistMode);
    //@karp: now with num of existing requests.
    //         ...but this number is changing each time we refresh the list from ws app
    //          that's why we must refresh this list in many situations or eliminate nums in brackets
    //          in addition - the list of types changes the same way (so we have ti change it anyway)

    /*if(count($typeList) !== 1){ //==1
        //"Any" option only -> no SR exists.
        $form['no_sr'] = array('#value' => '<h3 id="no_sr">There are no service requests matching the filter</h3>');
        return $form;
    }*/

    $defaultTypeId = ( !empty($_GET['type_id']) && array_key_exists($_GET['type_id'],$typeList) ) ?
                            $_GET['type_id'] : current(array_keys($typeList));

    $defaultStatusId=$_GET['status_id'];

    body_class('sr_review');
    $form['#attributes']=array('class'=>'sr_form sr_review');
    $form['selectors_start'] = array('#value' => '<div class="review_sr_list"><div class="css_div__filter_control_bar"><table cellpadding="0" cellspacing="0"><tr>');
    $form['property']=array('#type'=>'select','#title'=>/*'Select '.*/$params['select_property']['#title'],'#options'=>$propOptions, '#default_value'=>$pId,'#prefix'=>'<td>','#suffix'=>'</td>');
    $form['type']=array('#type'=>'select','#title'=>/*'Select '.*/$params['select_type']['#title'],'#options'=>$typeList,'#prefix'=>'<td>','#default_value'=>$defaultTypeId, '#suffix'=>'</td>');
//@karp: not for this release (action required is for PMs but not for regular tenants )
    $form['status']=array('#type'=>'select','#title'=>/*'Select '.*/$params['select_status']['#title'],'#options'=>wssr_review_status_list(null), '#default_value'=>$defaultStatusId,'#prefix'=>'<td class="css_td__last">','#suffix'=>'</td>');//, '#attributes1'=>array('multiple'=>"multiple"), );
/*
    if($srlistMode == 'all_requests')
    {
        $form['tenant']=array('#type'=>'select','#title'=>'Select Tenant','#options'=>wssr_review_get_tenant_list($pId),'#prefix'=>'</tr><tr><td>','#suffix'=>'</td>');
        $form['action_required']=array('#type'=>'select','#title'=>'Select Action Required','#options'=>wssr_review_get_action_required_list($pId),'#prefix'=>'<td>','#suffix'=>'</td>');
    }
*/
    $form['selectors_end'] = array('#value' => '</tr></table></div>');
    
    $form['search_type']=array('#type'=>'hidden','#default_value'=>$data['srlistMode']);

//    $form['no_sr'] = array('#value' => '<span id="no_sr"><h3>'.$params['no_matching']['#title'].'</h3><p><a href="javascript:void(0)" onclick="reloadSrReviewControl()">'.$params['reload']['#title'].'</a></p></span>');

    $form['reviewControl'] = array('#value'=>'<table id="srReviewControl"></table>');
    $form['closure'] = array('#value' => '</div>');
    return $form;
}

function wssr_review_single($id){
	body_class('sr_review_details');
	general_add_css('flexigrid/css/flexigrid/flexigrid.css');
	drupal_add_js(drupal_get_path('module', 'wsservicerequest').'/wsservicerequest.js');
    //should use different renders depending on request type.
    return theme('review_single', wssr_review_get_single_sr($id));
}

function wssr_review_single_visitor($id){
	body_class('sr_review_details');
	general_add_css('flexigrid/css/flexigrid/flexigrid.css');
	drupal_add_js(drupal_get_path('module', 'wsservicerequest').'/wsservicerequest.js');
    return theme('review_single_visitor', wssr_get_single_meeting($id));
}
/* COMMON SR part <- */


/* VISITORS part -> */
function wssr_review_request_data_visitors($request){
    //here we should receive data - with cache, main app or both
    $data['params'] = array();

    $resetPager=$request['resetPager'];
    if ($resetPager=='resetPager') {
        unset($request['page']);
    }
    $meetings=wssr_get_visitors($request);

//	can be changed to object with property of CURL error message
    if (!$meetings)
    	return 'null';
    $data = wssr_flatten_visitors_list($meetings);

    foreach($data['rows'] as $key=>$val){
//        $data['rows'][$key]['cell'][1] = l($data['rows'][$key]['cell'][1], 'wsservicerequest/review/single_visitor/'.$data['rows'][$key]['id'], array());//'<a href="'.$data[$key]['cell'][0].'">'.$data[$key]['cell'][0].'</a>';
        $data['rows'][$key]['cell'][1] = '<a href="javascript:void(0)" onclick=ViewVisitorDetails("'.base_path().'wsservicerequest/review/single_visitor/'.$data['rows'][$key]['id'].'")>'.$data['rows'][$key]['cell'][1].'</a>';
    }

    if($request['reloadType'] === true){
        $userId = get_ws_user_id();
        $data['params']['type'] = wssr_review_type_list($userId, $request['property_id']);
    }
    if (!empty($request['page']))
        $data['page']=$request['page'];
    if ($resetPager=='resetPager')
        $data['page']=1;

    $data['params']['gridHeaders'] = wssr_get_list_headers($meetings['MeetingList']);
    return json_encode($data);
}

function wssr_view_created_sr_visitors(){
    $request = array(
        'page' => $_POST['page'],
        'rp' => $_POST['rp'],
        'sortname' => $_POST['sortname'],
        'sortorder' => $_POST['sortorder'],
        'query' => $_POST['query'],
        'qtype' => $_POST['qtype'],

        'propertyId' => $_POST['propertyId'],
        'date' => $_POST['date'],
        'reloadType' => $_POST['reloadType'],

        'resetPager' => $_POST['resetPager'],
        'filterMode' => $_POST['filterMode'],
    );

    $reply = wssr_review_request_data_visitors($request);

    echo $reply;
    die;
}

function wssr_review_visitors_form($form, $srlistMode){
    $params=wssr_get_field_params(wssr_load_field_params('wssr_review_visitor_form'), 'wssr_review_visitor_form');
    if($srlistMode == 'all')
        drupal_set_title($params['page_title_all']['#title']);
    else
    drupal_set_title($params['page_title']['#title']);
    /*$srlistMode = 'my_visitors';
    drupal_add_js('var filterMode = "'.$srlistMode.'"', 'inline');*/
    //$propList = ($srlistMode == 'all') ? ws_filter_properties(get_user_properties(), get_user_properties_with_attribute(array("sr_cancheck_in_visitors_strict", "sr_manage_visitors"))) : get_user_properties();
    $propList = ($srlistMode == 'all') ? ws_filter_properties(get_user_properties(), get_user_properties_with_attribute(array("sr_cancheck_in_visitors_strict", "sr_manage_visitors"))) :
                            ws_filter_properties(get_user_properties(), get_user_properties_with_attribute(array("sr_view_visitor_list")));
    $userId = get_ws_user_id();
    if (empty($propList) || empty($userId)) {
        //process empty page
    	return noaccess(null, $temp_var, 'wssr_review_visitors_form');
    }
    $pId=null;
    $propOptions = array();
/*    $check_for_pre_default=null;
    if ($srlistMode != 'all' && count($propList)>1) {
        $propOptions['all']='All';
        $pId=($check_for_pre_default) ? $check_for_pre_default : 'all';
    } else {
        $pId=current(array_keys($propList));
    }
    foreach($propList as $pKey=>$pVal) {
        $propOptions[$pKey]=$pVal['property_name'];
    }
	
	$var__selected_property_id=intval($_GET['property_id']);
    if (!array_key_exists($var__selected_property_id,$propList))
    	$var__selected_property_id=$pId;
*/	
	if ($srlistMode != 'all' && count($propList)>1)
        $propOptions['all']='All';

    $tmpPropOptions=null;
    foreach($propList as $pKey=>$pVal)
        $tmpPropOptions[$pKey]=$pVal['property_name'];

    if (!empty($tmpPropOptions) && count($tmpPropOptions)>0) {
        asort($tmpPropOptions);
        $propOptions = $propOptions + $tmpPropOptions;
    }

    $pId=((!empty($_GET['property_id'])) && array_key_exists($_GET['property_id'],$propOptions))?$_GET['property_id'] : null;
    //$pId=($check_for_pre_default) ? $check_for_pre_default :
    if (empty($pId))
        $pId=current(array_keys($propOptions));
	
//    $var__selected_date="";
//    if (strtotime($_GET['date']))
    	$var__selected_date=$_GET['date'];
	
	body_class('sr_review_visitors');
    $form['#attributes']=array('class'=>'sr_form sr_review');
    $form['selectors_start'] = array('#value' => '<div class="review_sr_list"><div class="css_div__filter_control_bar"><table cellpadding="0" cellspacing="0"><tr>');
    $form['property']=array('#type'=>'select','#title'=>/*'Select '.*/$params['select_property']['#title'],'#options'=>$propOptions, '#default_value'=>$pId,'#prefix'=>'<td>','#suffix'=>'</td>');
    $form['date']=array('#type'=>'textfield','#title'=>$params['visitors_for']['#title'],'#size'=>'','#required'=>true, '#attributes'=>array('class'=>'date-pick'),'#default_value'=>$var__selected_date,'#prefix'=>'<td>','#suffix'=>'</td>');
    $form['selectors_end'] = array('#value' => '</tr></table></div>');
    
    $form['search_type']=array('#type'=>'hidden','#default_value'=>$srlistMode);
//    $form['no_sr'] = array('#value' => '<span id="no_sr"><h3>There are no service requests matching the filter</h3><p><a href="javascript:void(0)" onclick="reloadSrReviewControl()">Reload</a></p></span>');

    $form['reviewControl'] = array('#value'=>'<table id="srReviewControl"></table>');
    $form['closure'] = array('#value' => '</div>');
    return $form;
}

function wssr_review_visitors($srlistMode = 'my')
{
	$params=wssr_get_field_params(wssr_load_field_params('wssr_review_visitor_form'), 'wssr_review_visitor_form');
	//$nameParams=wssr_get_field_params(wssr_load_field_params('wssr_visitor_form'), 'wssr_visitor_form');
	
    if($srlistMode != 'all')
         $srlistMode = 'my';
    wssr_adjust_scripts_temp();
    general_add_css('flexigrid/css/flexigrid/flexigrid.css');
    general_add_js('flexigrid/flexigrid.js');
    drupal_add_js('
    var dateVal=0;
    $(document).ready(function()
    {
        init_datepicker("#edit-date","'.date('m/d/Y').'");
        $(".date-pick").datePicker({startDate:"01/01/1996"});
        dateVal = $("#edit-date").val();
    });

    $(document).ready(function()
    {
    	var int__sr_review_visitors_form_width=parseInt($("#wssr-review-visitors-form").width());
    	var object__sr_review_visitors_table_configuration=
    	{
    		int__time_column:100,
    		int__fio_column:(int__sr_review_visitors_form_width-(100+16))/2,
    		int__company_column:(int__sr_review_visitors_form_width-(100+16))/2
		};
		
        $("#srReviewControl").flexigrid
			(
			{
			url: "'.base_path().'wsservicerequest/ajax/sr_view_visitors",
			dataType: "json",
			colModel : [
				{display: "Time", name : "time", width : object__sr_review_visitors_table_configuration.int__time_column, sortable : false, align: "left"},
				{display: "'.$params['visitor_first_name']['#title'].' / '.$params['visitor_last_name']['#title'].'", name : "visitor", width : object__sr_review_visitors_table_configuration.int__fio_column, sortable : false, align: "left"},
				{display: "'.$params['visitor_company']['#title'].'", name : "company", width : object__sr_review_visitors_table_configuration.int__company_column, sortable : false, align: "left"},
				{display: "", name : "scrollbar_separator", sortable : false, string__css_class_name:"css_td__last"}
				],
			nomsg:"'.$params['no_items']['#title'].'",
			errormsg: "'.$params['errormsg']['#title'].'",
			procmsg: "'.$params['procmsg']['#title'].'",
			sortname: "time",
			sortorder: "asc",
			usepager: true,
			singleSelect: true,
			title: "Visits for",
			useRp: true,
			rp: '.$params['items_per_page']['#config']['value'].',
			showTableToggleBtn: false,
            onSubmit: addSrReviewData,
            postprocess: set_grid_headers,
			preProcess: getSrReviewData,
			
			height: "auto",/*662*/
			resizable: false,
			pagestat: "Displaying <span> {from} - {to} </span> of {total} Visitors"
			}
			);

			$("#edit-date").change(function(){
			    if (dateVal == $("#edit-date").val())
			        return;
			    dateVal = $("#edit-date").val();
			    resetPager="resetPager";
                reloadSrReviewControl();
            });

            $("#edit-property").change(function(){
			    resetPager="resetPager";
                reloadSrReviewControl();
            });


    });

    /*
     * Manual control reload
     */
    var reloadType = false;

    function reloadSrReviewControl(){
        $("#srReviewControl").flexReload();
    }

    var firstId = null;
    var lastId = null;
    var reviewPage = 1;
    var resetPager=null;
    var filterMode = "'.$srlistMode.'_visitors"; //should be inited by Drupal
    var gridHeaders = null;

    function addSrReviewData(){
        controlsDisable(["#edit-property","#edit-date"]);
        extra = [
          {name: "date", value: $("#edit-date").val()},
          {name: "reloadType", value: reloadType},
          {name: "startId", value: firstId},
          {name: "propertyId", value: $("#edit-property").val()},
          {name: "resetPager", value: resetPager},
          {name: "reviewPage", value: reviewPage},
          {name: "filterMode", value: filterMode}
        ];
        reloadType = false;
        $("#srReviewControl").flexOptions({params: extra});
        return true;
    }

    function getSrReviewData(data){
        controlsEnable(["#edit-property","#edit-date"]);
        resetPager=null;
        if (!data)
        	return null;
        var p = data.params;
        if (p.page)
            reviewPage = p.page;
        if(data.rows != null)
        {
            firstId = p.firstId;
            lastId = p.lastId;
        }

        if (p.filterMode)
            filterMode = p.filterMode;
        if (p.gridHeaders)
                gridHeaders = p.gridHeaders;
        return data;
    }
    ', 'inline');

    wssr_history_js(4);
    return drupal_get_form('wssr_review_visitors_form', $srlistMode);
}

/* VISITORS part <- */

/*
 * Data handling
 *  stubs, for re-check and change.
 */
//rename?
function wssr_review_get_type_list($propertyId, $srlistMode=null){//
    //$pId=($propertyId=='-1'||$propertyId=='all') ? null : $propertyId;
    if ($srlistMode=="all_requests")
        $rawData='<search searchType="REQUEST_TYPE" orderBy="ID" sortOrder="asc"></search>';
    else
    $rawData='<search searchType="HOME_REQUEST_TYPE" orderBy="ID" sortOrder="asc"></search>';
    if ($propertyId!=null)
        $rawData.='<criteria filter_name="propertyId" filter_value="'.$propertyId.'"></criteria>';
    $params=null;
    $entityId=get_user_entity_id();
    if ($srlistMode=="all_requests" && (!empty($entityId)) ) {
        $params=array();
        $params["entityId"]=$entityId;
    }
    $data = wssr_data_request('srcatlist', $params, $rawData);

	if (is_ws_error())
    	return false;
    $data = $data['Content'][0][SXML_CHILDLIST];
	//$requestData = $data['TotalList'][0][SXML_CHILDLIST]['Total'];
    //type="allrequest"
    //type="request"
    //type="recurringrequest"
    //maybe we need another 'switcher' - for these 3 types
    //it's better than to implement 2 sections (for request/recurringrequest0 as it was done
    //      in the existing mainApp   
    $totalList=$data['TotalList'];
    $requestDataList=array();
    foreach($totalList as $listData){
        if ($listData[SXML_ATTR]['type']) {
            $requestDataList[$listData[SXML_ATTR]['type']]=$listData[SXML_CHILDLIST]['Total'];
        } else {
            $requestDataList['some_type']=$listData[SXML_CHILDLIST]['Total'];
        }
    }
    $resultList=array();

    foreach($requestDataList as $type=>$requestData) {
    $result = array();
    foreach($requestData as $rData){
		$catId = $rData[SXML_ATTR]['categoryID'];
        $param = $rData[SXML_ATTR]['param'];
        $count = $rData[SXML_ATTR]['value'];
        $name = $rData[SXML_VALUE];

        $result[$catId] = array(
            'param' => $param,
            'count' => $count,
            'name'  => $name,
        );
    }
        $resultList[$type]=$result;
    }
    //Could we have more than one type? If no - eliminate [$type] and just return resulting array.
    if($srlistMode=="all_requests")
        return $resultList['allrequest'];
    else
    return $resultList['request'];
}

function wssr_review_get_sr_list($filters, $srlistMode=null){
    //$direction=$filters["move_direction"];
    $intPage=null;
    $lastId=null;
    $searchArgs = "";
    $intPageSize=25;
    if (!empty($filters['rp'])) {
        $intPageSize=intval($filters['rp']);
        $searchArgs .= " pageSize=\"$intPageSize\" ";
    }
    if (!empty($filters['page'])) {
        //$lastId=$filters["lastId"];
        $intPage=intval($filters['page']);
        $lastId=($intPage-2)*$intPageSize+1;
        if ($lastId<0)
            $lastId=null;
    }

    if (!empty($lastId)) {
        $searchArgs .= " startIndex=\"$lastId\" ";
    }
    $sortOrder=$filters['sortorder'];
    if (empty($sortOrder))
        $sortOrder='ASC';
    else
        $sortOrder=strtoupper($sortOrder);

    $rawData='<search searchType="'.$srlistMode.'" orderBy="id" sortOrder="'.$sortOrder.'" '.$searchArgs.'>';
    $propertyId=$filters["propertyId"];
    if (empty($propertyId))
        $propertyId="all";
    $rawData.='<criteria filter_name="propertyId" filter_value="'.$propertyId.'"></criteria>';
    $srType=$filters["requestType"];
    if (empty($srType))
        $srType="all";
    $rawData.='<criteria filter_name="srType" filter_value="'.$srType.'"></criteria>';
    /* work with array
     if (empty($srType))
        $srType=array("all");
     foreach($srType as $type){
        $rawData.='<criteria filter_name="srType" filter_value="'.$type.'"></criteria>';
     }
     */
    $status=$filters["requestStatus"];
    if (empty($status))
        $status="all";
    $rawData.='<criteria filter_name="status" filter_value="'.$status.'"></criteria>';
    $rawData.='</search>';

    $data = wssr_data_request('srlist', $filters, $rawData);

	if (is_ws_error())
    	return false;
	

	$srListData = $data['Content'][0][SXML_CHILDLIST]['ServiceRequestList'][0];
    $start = $srListData[SXML_ATTR]['startIndex'];
    $count = $srListData[SXML_ATTR]['total'];

    $list = array();
    $STATUS_ARRAY = array('NEW' => 'New', 'PENDING' => 'Pending', 'COMPLETED' => 'Completed',
                          'CLOSED' => 'Closed', 'ARCHIVED' => 'Archived', 'ONHOLD' => 'OnHold');
    foreach($srListData[SXML_CHILDLIST]['ServiceRequestSummary'] as $srData){
        //propertyId, authorized
        $id = $srData[SXML_ATTR]['id'];
        $status=$STATUS_ARRAY[$srData[SXML_ATTR]['status']];
        if (empty($status))
            $status=$srData[SXML_ATTR]['status'];
        $list[$id] = array(
            'action' => $srData[SXML_ATTR]['action'],
            'final_cost' => $srData[SXML_ATTR]['final_cost'],
            'immediate' => $srData[SXML_ATTR]['immediate'],
            'status' => $status, //$srData[SXML_ATTR]['status'],
            'propertyId' => $srData[SXML_ATTR]['propertyId'],
        );

        $list[$id]['created']['date'] = $srData[SXML_CHILDLIST]['Created'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE];

        $list[$id]['category'] = array(
            'group' => $srData[SXML_CHILDLIST]['ServiceRequestCategory'][0][SXML_ATTR]['group'],
            'catId' => $srData[SXML_CHILDLIST]['ServiceRequestCategory'][0][SXML_ATTR]['id'],
            'name' => $srData[SXML_CHILDLIST]['ServiceRequestCategory'][0][SXML_ATTR]['name'],
            'extDescription' => $srData[SXML_CHILDLIST]['ServiceRequestCategory'][0][SXML_CHILDLIST]['ShortDescription'][0][SXML_VALUE],
        );

        $list[$id]['user'] = array(
            'id' => $srData[SXML_CHILDLIST]['User'][0][SXML_ATTR]['id'],
            'login' => $srData[SXML_CHILDLIST]['User'][0][SXML_ATTR]['login'],
            'firstName' => $srData[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
            'lastName' => $srData[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
            'entityId' => $srData[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Entity'][0][SXML_ATTR]['id'],
            'entityName' => $srData[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Entity'][0][SXML_VALUE],
            'email' => $srData[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['EMail'][0][SXML_VALUE],
        );
    }
    return array('start'=>$start, 'count'=>$count, 'list'=>$list);
}
//@karp: junk !!!
function wssr_review_get_tenant_list($propertyId, $srlistMode=null){
    array('1' => 'Tenant 1', '2'=>'Tenant 2');
}

function wssr_review_get_action_required_list($propertyId, $srlistMode=null){
    array('1' => 'Action 1', '2'=>'Action 2');
}


function wssr_review_get_single_sr($srId){
    //single sr data parse
    $data = wssr_data_request('srdetails', array('srId' => $srId));
    if (is_ws_error())
    	return false;

	$requestData = $data['Content'][0][SXML_CHILDLIST]['Content'][0][SXML_CHILDLIST];

    $srDetails = $requestData['ServiceRequestDetail'][0];
    $srHistory = $requestData['ServiceRequestHistory'][0][SXML_CHILDLIST]['History'];
    $users = $requestData['UserList'][0][SXML_CHILDLIST]['User'];
    $spaceSummary = $srDetails[SXML_CHILDLIST]['SpaceSummary'][0][SXML_CHILDLIST];

    $attachments = $srDetails[SXML_CHILDLIST]['FileDocumentList'][0][SXML_CHILDLIST]['FileDocument'];
    $reservation = $srDetails[SXML_CHILDLIST]['Reservation'];
    
    $array__sr_comment=$requestData['ServiceInformationList'][0][SXML_CHILDLIST]['ServiceInformation'];

    $result['srDetails'] = array(
        'cost_center_id' => $srDetails[SXML_ATTR]['cost_center_id'],
        'id' => $srDetails[SXML_ATTR]['id'],
        'createdDate' => $srDetails[SXML_CHILDLIST]['Created'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE],
        'modifiedDate' => $srDetails[SXML_CHILDLIST]['Modified'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE],
        'type' => $srDetails[SXML_CHILDLIST]['ServiceRequestCategory'][0][SXML_CHILDLIST]['ShortDescription'][0][SXML_VALUE],
        'status' => $srDetails[SXML_CHILDLIST]['ServiceStatus'][0][SXML_VALUE],
        'description' => $srDetails[SXML_CHILDLIST]['ServiceDescription'][0][SXML_VALUE],
    	'location_details' => $srDetails[SXML_CHILDLIST]['LocationDescription'][0][SXML_VALUE],
    	'upon_arrival_ask_for' => $srDetails[SXML_CHILDLIST]['ContactDescription'][0][SXML_VALUE],
    	'shared_resource_flag' => $srDetails[SXML_CHILDLIST]['ServiceRequestCategory'][0][SXML_ATTR]['sharedYN'],

        'nextStep' => $srDetails[SXML_CHILDLIST]['NextAction'][0][SXML_VALUE],

    	'property_name'=>$srDetails[SXML_CHILDLIST]['PropertySummary'][0][SXML_CHILDLIST]['Description'][0][SXML_VALUE],
    
        //actor with name="initiated_by". Maybe another in the same tag but with name="requested_to"? Should we use login or name?
        
        'performed_by' => $requestData['UserList'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Entity'][0][SXML_VALUE],
    
        // requested by in mainApp
        'initiated_by' => array(
            'first' => $srDetails[SXML_CHILDLIST]['ActorList'][0][SXML_CHILDLIST]['Actor'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
            'last' => $srDetails[SXML_CHILDLIST]['ActorList'][0][SXML_CHILDLIST]['Actor'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
            'email' => $srDetails[SXML_CHILDLIST]['ActorList'][0][SXML_CHILDLIST]['Actor'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['EMail'][0][SXML_VALUE],
    		'phone' => $srDetails[SXML_CHILDLIST]['ActorList'][0][SXML_CHILDLIST]['Actor'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Phone'][0][SXML_VALUE],
    		'entity' => $srDetails[SXML_CHILDLIST]['ActorList'][0][SXML_CHILDLIST]['Actor'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Entity'][0][SXML_VALUE],
        ),
        
        'on_behalf_of' =>array(
            'first' => $srDetails[SXML_CHILDLIST]['CreatedFor'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
            'last' => $srDetails[SXML_CHILDLIST]['CreatedFor'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
            'email' => $srDetails[SXML_CHILDLIST]['CreatedFor'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['EMail'][0][SXML_VALUE],),

        'created_for' => array(
        	'entity_description' => $srDetails[SXML_CHILDLIST]['ForEntity'][0][SXML_CHILDLIST]['EntitySummary'][0][SXML_CHILDLIST]['Description'][0][SXML_VALUE],
        	'entity_address1' => $srDetails[SXML_CHILDLIST]['ForEntity'][0][SXML_CHILDLIST]['EntitySummary'][0][SXML_CHILDLIST]['Address'][0][SXML_CHILDLIST]['Address1'][0][SXML_VALUE],
        	'entity_address2' => $srDetails[SXML_CHILDLIST]['ForEntity'][0][SXML_CHILDLIST]['EntitySummary'][0][SXML_CHILDLIST]['Address'][0][SXML_CHILDLIST]['Address2'][0][SXML_VALUE],
        ),

        'division' => $srDetails[SXML_CHILDLIST]['DivisionSummary'][0][SXML_CHILDLIST]['Description'][0][SXML_VALUE],
        'cost_center' => $srDetails[SXML_CHILDLIST]['DivisionSummary'][0][SXML_CHILDLIST]['cost_center_id'][0][SXML_VALUE],
    );
    
    if ($requestData['BuildingPassRequestDetail'])
    {
//    	print_r($requestData['BuildingPassRequestDetail']);
    	
    	$array__temp=$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['SpaceSummary'][0][SXML_CHILDLIST]['SpaceHierarchy'];
    	$array__temp_result=array();
    	foreach ($array__temp as $var__value)
    		$array__temp_result[$var__value[SXML_ATTR]['name']]=$var__value[SXML_VALUE];
    	
    	$result['access_card']=array
    	(
    		'user'=>array
    		(
    			'name'=>array
    			(
    				'first'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
    				'last'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
    			),
    			'entity'=>array
    			(
    				'name'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Entity'][0][SXML_VALUE],
	    			'id'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Entity'][0][SXML_ATTR]['id'],
    			),
	    		'email'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['EMail'][0][SXML_VALUE],
    			'phone'=>array
    			(
    				'number'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Phone'][0][SXML_VALUE],
	    			'type'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Phone'][0][SXML_ATTR]['id'],
    			)
	    		
    		),
    		'building_access_privileges'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['AccessTo'][0][SXML_VALUE],
    		'workspeed_privileges'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_ATTR]['hostVisitorYN'],
    		'access_hours'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['AccessHours'][0][SXML_VALUE],
    		'space_summary'=>array
    		(
    			'description'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['SpaceSummary'][0][SXML_CHILDLIST]['Description'][0][SXML_VALUE],
    			'space_category'=>array
    			(
    				'value'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['SpaceSummary'][0][SXML_CHILDLIST]['SpaceCategory'][0][SXML_VALUE],
    				'id'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['SpaceSummary'][0][SXML_CHILDLIST]['SpaceCategory'][0][SXML_ATTR]['id'],
    				'name'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['SpaceSummary'][0][SXML_CHILDLIST]['SpaceCategory'][0][SXML_ATTR]['name'],
    			),
    			'space_hierarchy'=>$array__temp_result,
    			
    		),
    		'badge_numger'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['BadgeUser'][0][SXML_CHILDLIST]['UserProfileSummary'][0][SXML_CHILDLIST]['BadgeNumber'][0][SXML_VALUE],
    		'additional_info'=>$requestData['BuildingPassRequestDetail'][0][SXML_CHILDLIST]['AdditionalInfo'][0][SXML_VALUE],
    	);
    	
    	
    	
    
    }
    
	$result['requested_sr_id']=$srId;
    if($srDetails[SXML_CHILDLIST]['Hvac']){
        $hvac = $srDetails[SXML_CHILDLIST]['Hvac'][0][SXML_CHILDLIST];
        $result['hvac'] = array(
            'FromDate' => $hvac['FromDate'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE],
            'ToDate' => $hvac['ToDate'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE],
        );
    }

    $space['id'] = $spaceSummary['Description'][0][SXML_VALUE];
    foreach($spaceSummary['SpaceHierarchy'] as $spaceHierarchy){
        $space[ $spaceHierarchy[SXML_ATTR]['name'] ] = $spaceHierarchy[SXML_VALUE];
    }
    $result['srDetails']['space'] = $space;

    foreach($srHistory as $history)
    {
    	if ($history[SXML_CHILDLIST]['ActionHistory'][0][SXML_ATTR]['name']!=='commentadded')
        $result['history'][] = array(
            'date' => $history[SXML_CHILDLIST]['Date'][0][SXML_VALUE],
            'status' => $history[SXML_CHILDLIST]['ServiceStatus'][0][SXML_VALUE],
            'action' => $history[SXML_CHILDLIST]['ActionHistory'][0][SXML_VALUE],
            'user' => array(
                'first' => $history[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
                'last' => $history[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
        		'entity' => $history[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Entity'][0][SXML_VALUE],
                //'email' => $history[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Email'][0][SXML_VALUE],
            ),
        );
    }
    
    $result['sr_comment_list']=array();
    foreach($array__sr_comment as $var__value)
    {
    	foreach($srHistory as $history)
		{
			if ($history[SXML_ATTR]['informationId']===$var__value[SXML_ATTR]['id'])
			{
				$array__temp=array();
				$array__temp_=array();
				$array__temp_[]=$history[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE];
				$array__temp_[]=$history[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE];
				$date=ws_parse_time($history[SXML_CHILDLIST]['Date'][0][SXML_VALUE]);
				$array__temp['date']=$date['date'];
				$array__temp['time']=$date['time12'];
				$array__temp['user']=implode(' ',$array__temp_);
				$array__temp['comment']=$var__value[SXML_CHILDLIST]['Description'][0][SXML_VALUE];
				
				$result['sr_comment_list'][]=$array__temp;
			}
		}
    	
    	
    }

    foreach($users as $user){
        $result['users'][] = array(
            'first' => $user[SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
            'last' => $user[SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
        );
    }

    foreach($attachments as $attachment){
        $result['attachments'][] = array(
            'uploadDate' => $attachment[SXML_CHILDLIST]['UploadDate'][0][SXML_ATTR]['Date'],
            'clientPath' => $attachment[SXML_ATTR]['clientPath'],
            'id' => $attachment[SXML_ATTR]['id'],
            'title' => $attachment[SXML_ATTR]['title'],
        );
    }

    foreach($reservation[0][SXML_CHILDLIST]['Schedule'] as $chunk){
        $chunk = $chunk[SXML_CHILDLIST];
        $result['reservation'][] = array(
            'from' => $chunk['FromDate'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE],
            'to' => $chunk['ToDate'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE],
        );
    }
    /* ADDITIONAL FIELDS -> */

    /* ADDITIONAL FIELDS <- */

    return $result;
}

function wssr_get_visitors($request){
    $sortByMapping=array('time'=>"M.START_DATE", 'visitor'=>"Visitor", 'company'=>"MP.COMPANY_NAME");

    $reqSortBy=$request['sortname'];
    $sortBy=(empty($reqSortBy)) ? $sortByMapping['time'] : $sortByMapping[$reqSortBy];
    //else


    if (empty($sortOrder))
        $sortOrder='ASC';
    else
        $sortOrder=strtoupper($sortOrder);

    $propertyId=$request['propertyId'];
    $pageSize=$request['rp'];
    $sortOrder=$request['sortorder'];
    $pageNumber=$request['page'];
    $startDate=$request['date'];

    $filterMode=empty($request['filterMode']) ? 'my_visitors' : $request['filterMode'];

    //$rawData = '<search searchType="my_visitors"';
    $rawData = '<search searchType="'.$filterMode.'" ';
    if (!empty($pageSize))
        $rawData .= ' pageSize="'.$pageSize.'"';
    if (!empty($sortOrder))
        $rawData .= ' sortOrder="'.$sortOrder.'"';
    if (!empty($pageNumber))
        $rawData .= ' pageNumber="'.$pageNumber.'"';
    if (!empty($sortBy))
        $rawData .= ' sortBy="'.$sortBy.'"';
    if (!empty($startDate))
        $rawData .= ' startDate="'.$startDate.'"';
    $rawData .= '><criteria filter_name="propertyId" filter_value="'.$propertyId.'"></criteria></search>';

    //$data = wssr_data_request('my_visitors', array('startDate' => $request['date']), $rawData);
    $data = wssr_data_request($filterMode, array('startDate' => $request['date']), $rawData);
    
    if (is_ws_error())
    	return false;
    	
    $resultList=array();

    $requestData = $data['Content'][0][SXML_CHILDLIST];
    $meetingList = $requestData['MeetingList'][0][SXML_CHILDLIST];
    $pageSwitcher = $meetingList['PageSwitcher'][0];
    $total=$pageSwitcher[SXML_ATTR]['total'];
    $meetingSummaryList = $meetingList['MeetingSummary'];

    foreach ($meetingSummaryList as $meetingSummaryItem) {
            $result=array();
            $srId=$meetingSummaryItem[SXML_ATTR]['visitorSrId'];
        $propId=$meetingSummaryItem[SXML_ATTR]['propertyId'];
        $meetingSummary = $meetingSummaryItem[SXML_CHILDLIST];


        $createdBy = $meetingSummary['CreatedBy'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST];
        $entity = $meetingSummary['EntitySummary'][0][SXML_CHILDLIST];
        $meetingWith = $meetingSummary['MeetingWith'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST];
        $participants = $meetingSummary['ParticipantList'][0][SXML_CHILDLIST]['ParticipantSummary'];//[0][SXML_CHILDLIST];

        $result['createdBy'] = array(
            'first' => $createdBy['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
            'last' => $createdBy['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
            'email' => $createdBy['EMail'][0][SXML_VALUE],
            'entity' => $createdBy['Entity'][0][SXML_VALUE],
            'phone' => $createdBy['Phone'][0][SXML_VALUE], //should it be array with types?
            'title' => $createdBy['Title'][0][SXML_VALUE],
        );

        $result['entity'] = array(
            'Description' => $entity['Description'][0][SXML_VALUE],
            'TypeOfBusiness' => $entity['TypeOfBusiness'][0][SXML_VALUE],

            'address' => array(
                'Address1' => $entity['Address'][0][SXML_CHILDLIST]['Address1'][0][SXML_VALUE],
                'Address2' => $entity['Address'][0][SXML_CHILDLIST]['Address2'][0][SXML_VALUE],
                'City' => $entity['Address'][0][SXML_CHILDLIST]['City'][0][SXML_VALUE],
                'Zip' => $entity['Address'][0][SXML_CHILDLIST]['Zip'][0][SXML_VALUE],
                'State' => $entity['Address'][0][SXML_CHILDLIST]['State'][0][SXML_VALUE],
                'Country' => $entity['Address'][0][SXML_CHILDLIST]['Country'][0][SXML_VALUE],
                'BusinessPhone' => $entity['Address'][0][SXML_CHILDLIST]['BusinessPhone'][0][SXML_VALUE],
            ),

            'FederalId' => $entity['FederalId'][0][SXML_VALUE],
        );

        $result['meetingWith'] = array(
            'first' => $meetingWith['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
            'last' => $meetingWith['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
            'email' => $meetingWith['EMail'][0][SXML_VALUE],
            'entity' => $meetingWith['Entity'][0][SXML_VALUE],
            'phone' => $meetingWith['Phone'][0][SXML_VALUE], //should it be array with types?
            'title' => $meetingWith['Title'][0][SXML_VALUE],
            //DashBoard?
        );

        $result['FromDate'] = $meetingSummary['FromDate'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE];
        $result['LocationDescription'] = $meetingSummary['LocationDescription'][0][SXML_VALUE];
        $result['Phone'] = $meetingSummary['Phone'][0][SXML_VALUE];

        foreach($meetingSummary['Date'][0][SXML_VALUE]['SpaceDescription'] as $item){
            $result['space'][$item[SXML_ATTR]['name']] = $item[SXML_VALUE];
        }

        foreach($participants as $participant){
            $result['participants'][$participant[SXML_ATTR]['id']] = array( //do we need id/internal_visitor_id?
                'first' => $participant[SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
                'last' => $participant[SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
                'company' => $participant[SXML_CHILDLIST]['Company'][0][SXML_CHILDLIST]['Description'][0][SXML_VALUE],
                'internal_visitor_id' => $participant[SXML_ATTR]['internal_visitor_id']
            );
        }


        $result['VisitDate'] = $meetingList['VisitDate'][0][SXML_VALUE];
        $result['serviceRequestId']=$srId;
        $result['propertyId']=$propId;
        $resultList[$srId]=$result;
    }
    return array('MeetingList'=>$resultList, 'total'=>$total);
}

function wssr_get_single_meeting($srId){
    $data = wssr_data_request('visitorSrDetails', array('srid' => $srId));
     if (is_ws_error())
    	return false;

    $requestData = $data['Content'][0][SXML_CHILDLIST];
    $meetingDetail = $requestData['MeetingDetail'][0][SXML_CHILDLIST];

    $createdBy = $meetingDetail['CreatedBy'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST];
    $entity = $meetingDetail['EntitySummary'][0][SXML_CHILDLIST];
    $meetingWith = $meetingDetail['MeetingWith'][0][SXML_CHILDLIST]['User'][0][SXML_CHILDLIST];
    $participants = $meetingDetail['ParticipantList'][0][SXML_CHILDLIST]['ParticipantSummary'];

    $requestHistory = $requestData['ServiceRequestDetail'][0][SXML_CHILDLIST]['RequestHistoryList'][0][SXML_CHILDLIST]['RequestHistory'];

    $meeting['sr_comment_list']=array();
    foreach($requestHistory as $history)
    {
        if ($history[SXML_CHILDLIST]['Action'][0][SXML_ATTR]['name'] == 'commentadded')
        {
            $array__temp=array();
            $array__temp_=array();
            $array__temp_[]=$history[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE];
            $array__temp_[]=$history[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE];
            $date=ws_parse_time($history[SXML_CHILDLIST]['Date'][0][SXML_VALUE]);
            $array__temp['date']=$date['date'];
            $array__temp['time']=$date['time12'];
            $array__temp['user']=implode(' ',$array__temp_);
            $array__temp['comment']=$history[SXML_CHILDLIST]['RequestInformation'][0][SXML_CHILDLIST]['Description'][0][SXML_VALUE];

            $meeting['sr_comment_list'][]=$array__temp;
        }
    }

    $property_capture_settings=array();
    foreach ($requestData['PropertyCaptureSettings'][0][SXML_CHILDLIST]['CaptureSetup'] as $var__value)
    	$property_capture_settings[$var__value[SXML_ATTR]['type']]=array('width'=>$var__value[SXML_ATTR]['width'],'height'=>$var__value[SXML_ATTR]['height']);
    
	$meeting['property_name']=$requestData['ServiceRequestDetail'][0][SXML_CHILDLIST]['PropertySummary'][0][SXML_CHILDLIST]['Description'][0][SXML_VALUE];
    
    $attachments = $requestData['ServiceRequestDetail'][0][SXML_CHILDLIST]['FileDocumentList'][0][SXML_CHILDLIST]['FileDocument'];

    $meeting['srId'] = $requestData['MeetingDetail'][0][SXML_ATTR]['srid'];
    
    $meeting['requested_sr_id']=$srId;
    $meeting['createdBy'] = array(
        'first' => $createdBy['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
        'last' => $createdBy['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
        'email' => $createdBy['EMail'][0][SXML_VALUE],
        'entity' => $createdBy['Entity'][0][SXML_VALUE],
        'phone' => $createdBy['Phone'][0][SXML_VALUE], //should it be array with types?
        'title' => $createdBy['Title'][0][SXML_VALUE],
        //DashBoard?
    );

    $meeting['entity'] = array(
        'Description' => $entity['Description'][0][SXML_VALUE],
        'TypeOfBusiness' => $entity['TypeOfBusiness'][0][SXML_VALUE],

        'address' => array(
            'Address1' => $entity['Address'][0][SXML_CHILDLIST]['Address1'][0][SXML_VALUE],
            'Address2' => $entity['Address'][0][SXML_CHILDLIST]['Address2'][0][SXML_VALUE],
            'City' => $entity['Address'][0][SXML_CHILDLIST]['City'][0][SXML_VALUE],
            'Zip' => $entity['Address'][0][SXML_CHILDLIST]['Zip'][0][SXML_VALUE],
            'State' => $entity['Address'][0][SXML_CHILDLIST]['State'][0][SXML_VALUE],
            'Country' => $entity['Address'][0][SXML_CHILDLIST]['Country'][0][SXML_VALUE],
            'BusinessPhone' => $entity['Address'][0][SXML_CHILDLIST]['BusinessPhone'][0][SXML_VALUE],
        ),

        'FederalId' => $entity['FederalId'][0][SXML_VALUE],
    );

    $meeting['meetingWith'] = array(
        'first' => $meetingWith['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
        'last' => $meetingWith['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
        'email' => $meetingWith['EMail'][0][SXML_VALUE],
        'entity' => $meetingWith['Entity'][0][SXML_VALUE],
        'phone' => $meetingWith['Phone'][0][SXML_VALUE], //should it be array with types?
        'title' => $meetingWith['Title'][0][SXML_VALUE],
        //DashBoard?
    );

    $meeting['FromDate'] = $meetingDetail['FromDate'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE];
    $meeting['LocationDescription'] = $meetingDetail['LocationDescription'][0][SXML_VALUE];
    $meeting['MeetingNote'] = $meetingDetail['MeetingNote'][0][SXML_VALUE];
    $meeting['Phone'] = $meetingDetail['Phone'][0][SXML_VALUE];

    foreach($meetingDetail['SpaceDescription'] as $item){
        $meeting['space'][$item[SXML_ATTR]['name']] = $item[SXML_VALUE];
    }

    $meeting['MeetingLocationDescription'] = $meetingDetail['MeetingLocationDescription'][0][SXML_VALUE];
    $meeting['BriefMeetingLocationDescription'] = $meetingDetail['BriefMeetingLocationDescription'][0][SXML_VALUE];
    $meeting['MeetingDateDescription'] = $meetingDetail['MeetingDateDescription'][0][SXML_VALUE];

    foreach($participants as $participant){
        $meeting['participants'][$participant[SXML_ATTR]['id']] = array( //do we need id/internal_visitor_id?
            'first' => $participant[SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
            'last' => $participant[SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
            'company' => $participant[SXML_CHILDLIST]['Company'][0][SXML_CHILDLIST]['Description'][0][SXML_VALUE],
        	'participant_image' =>array('document_image_id'=> $participant[SXML_CHILDLIST]['ParticipantPhoto'][0][SXML_ATTR]['document_image_id'],
        								'document_image_key'=> $participant[SXML_CHILDLIST]['ParticipantPhoto'][0][SXML_ATTR]['document_image_key'],
        								'enface_image_id'=>$participant[SXML_CHILDLIST]['ParticipantPhoto'][0][SXML_ATTR]['enface_image_id'],
        								'enface_image_key'=>$participant[SXML_CHILDLIST]['ParticipantPhoto'][0][SXML_ATTR]['enface_image_key']),
            'internal_visitor_id' => $participant[SXML_ATTR]['internal_visitor_id'],
            'CheckIn' => $participant[SXML_CHILDLIST]['CheckIn'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE],
            'CheckOut' => $participant[SXML_CHILDLIST]['CheckOut'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE],

        );
    }

    //-----
    $serviceRequestDetail = $requestData['ServiceRequestDetail'][0][SXML_CHILDLIST];

    $ServiceRequestCategory = $serviceRequestDetail['ServiceRequestCategory'][0][SXML_CHILDLIST];
    $RequestHistoryList = $serviceRequestDetail['RequestHistoryList'][0][SXML_CHILDLIST];
    $ActorList = $serviceRequestDetail['ActorList'][0][SXML_CHILDLIST]['Actor'][0][SXML_CHILDLIST];
    $PropertySummary = $serviceRequestDetail['PropertySummary'][0][SXML_CHILDLIST];
    $SpaceSummary = $serviceRequestDetail['SpaceSummary'][0][SXML_CHILDLIST];
    $CreatedFor = $serviceRequestDetail['CreatedFor'][0][SXML_CHILDLIST];

    $srDetail['created'] = $serviceRequestDetail['Created'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE];
    $srDetail['modified'] = $serviceRequestDetail['Modified'][0][SXML_CHILDLIST]['Date'][0][SXML_VALUE];

    $srDetail['ServiceRequestCategory'] = array(
        'ShortDescription' => $ServiceRequestCategory['ShortDescription'][0][SXML_VALUE],
        'ExtensiveDescription' => $ServiceRequestCategory['ExtensiveDescription'][0][SXML_VALUE],
    );


    foreach($RequestHistoryList['RequestHistory'] as $requestHistory){
        $srDetail['RequestHistory'][] = array(
            'date' => $requestHistory[SXML_CHILDLIST]['Date'][0][SXML_VALUE],
            'action' => $requestHistory[SXML_CHILDLIST]['Action'][0][SXML_VALUE],
            'status' => $requestHistory[SXML_CHILDLIST]['RequestStatus'][0][SXML_VALUE],

            'user' => array(
                'first' => $requestHistory[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
                'last' => $requestHistory[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
                'email' => $requestHistory[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['EMail'][0][SXML_VALUE],
                'entity' => $requestHistory[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Entity'][0][SXML_VALUE],
                'phone' => $requestHistory[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Phone'][0][SXML_VALUE], //should it be array with types?
                'title' => $requestHistory[SXML_CHILDLIST]['User'][0][SXML_CHILDLIST]['Title'][0][SXML_VALUE],
            ),
        );
    }


    $srDetail['ActorList'] = array(
        'User' => array(
            'first' => $ActorList[SXML_CHILDLIST]['User'][0]['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
            'last' => $ActorList[SXML_CHILDLIST]['User'][0]['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
            'email' => $ActorList[SXML_CHILDLIST]['User'][0]['EMail'][0][SXML_VALUE],
            'entity' => $ActorList[SXML_CHILDLIST]['User'][0]['Entity'][0][SXML_VALUE],
            'phone' => $ActorList[SXML_CHILDLIST]['User'][0]['Phone'][0][SXML_VALUE], //should it be array with types?
            'title' => $ActorList[SXML_CHILDLIST]['User'][0]['Title'][0][SXML_VALUE],
            //DashBoard?
        ),

        'UserRole' => array(
            'UserRoleCategory' => $ActorList[SXML_CHILDLIST]['UserRole'][0]['UserRoleCategory'][0][SXML_VALUE],
            'Property' => $ActorList[SXML_CHILDLIST]['UserRole'][0]['Property'][0][SXML_VALUE],
        ),
        //Space
    );

    $srDetail['ForEntity'] = $serviceRequestDetail['ForEntity'][0][SXML_CHILDLIST]['Entity'][0][SXML_VALUE];


    $srDetail['PropertySummary'] = array(
        'description' => $PropertySummary['Description'][0][SXML_VALUE],

        'address' => array(
            'Address1' => $PropertySummary['Address'][0][SXML_CHILDLIST]['Address1'][0][SXML_VALUE],
            'Address2' => $PropertySummary['Address'][0][SXML_CHILDLIST]['Address2'][0][SXML_VALUE],
            'City' => $PropertySummary['Address'][0][SXML_CHILDLIST]['City'][0][SXML_VALUE],
            'Zip' => $PropertySummary['Address'][0][SXML_CHILDLIST]['Zip'][0][SXML_VALUE],
            'State' => $PropertySummary['Address'][0][SXML_CHILDLIST]['State'][0][SXML_VALUE],
            'Country' => $PropertySummary['Address'][0][SXML_CHILDLIST]['Country'][0][SXML_VALUE],
            'BusinessPhone' => $PropertySummary['Address'][0][SXML_CHILDLIST]['BusinessPhone'][0][SXML_VALUE],
        ),
    );

    foreach($serviceRequestDetail['SpaceSummary'][0][SXML_VALUE]['SpaceHierarchy'] as $item){
        $srDetail['space'][$item[SXML_ATTR]['name']] = $item[SXML_VALUE];
    }

    $srDetail['CreatedFor'] = array(
        'first' => $CreatedFor['PersonName'][0][SXML_CHILDLIST]['First'][0][SXML_VALUE],
        'last' => $CreatedFor['PersonName'][0][SXML_CHILDLIST]['Last'][0][SXML_VALUE],
        'email' => $CreatedFor['EMail'][0][SXML_VALUE],
        'entity' => $CreatedFor['Entity'][0][SXML_VALUE],
        'phone' => $CreatedFor['Phone'][0][SXML_VALUE], //should it be array with types?
        'title' => $CreatedFor['Title'][0][SXML_VALUE],
    );

    foreach($attachments as $attachment){
        $srDetail['attachments'][] = array(
            'uploadDate' => $attachment[SXML_CHILDLIST]['UploadDate'][0][SXML_ATTR]['Date'],
            'clientPath' => $attachment[SXML_ATTR]['clientPath'],
            'id' => $attachment[SXML_ATTR]['id'],
            'title' => $attachment[SXML_ATTR]['title'],
        );
    }

    return array('meetingDetail' => $meeting, 'serviceRequestDetail' => $srDetail, 'property_capture_settings'=>$property_capture_settings);
}

function wssr_flatten_visitors_list($meetings){
    foreach($meetings['MeetingList'] as $meeting){
        $time = ws_parse_time($meeting['FromDate']);
        $srId=$meeting['serviceRequestId'];
        foreach($meeting['participants'] as $key=>$row){
            $rows[] = array(
               'id' => $srId,
               'cell' =>
                  array (
                    0 => $time['time12'],
                    1 => $row['first'].' '.$row['last'],
                    2 => $row['company'] ? $row['company'] : '',
                    3 => '&nbsp;',
                  ),
            );
        }
    }

    $data = array(
        'total' => $meetings['total'],
        'rows' => $rows,
        'params' => array(
        ),
    );
    return $data;
}

function wssr_flatten_history_array($data){
    $history = array();
    foreach($data as $item){
        $time = ws_parse_time($item['date']);
        $history[] = array(
            $time['date'],
            $time['time12'],
            $item['action'],
            $item['user']['first'].' '.$item['user']['last'],
            $item['user']['entity'],
            $item['status'],
            );
    }
    return $history;
}

function wssr_flatten_participants_array($data){
    $participants = array();
    foreach($data as $participant)
    {
    	$string_html__photo_link="";
        $string_html__credential_link="";
    	if ($participant['participant_image']['document_image_id'])
    		$string_html__credential_link=
    			'<a href="javascript:void(0)" onclick=ShowParticipantImage("photo","'.$participant['participant_image']['document_image_id'].'","'.$participant['participant_image']['document_image_key'].'")>View</a>';

    	if ($participant['participant_image']['enface_image_id'])
    		$string_html__photo_link=
    			'<a href="javascript:void(0)" onclick=ShowParticipantImage("credential","'.$participant['participant_image']['enface_image_id'].'","'.$participant['participant_image']['enface_image_key'].'")>View</a>';
        $checkInTime = (empty($participant['CheckIn'])) ? '' : ws_parse_time($participant['CheckIn']);
        $checkOutTime = (empty($participant['CheckOut'])) ? '' : ws_parse_time($participant['CheckOut']);

        $participants[] = array(
            $participant['first'],
            $participant['last'],
            $participant['company'],
            $string_html__photo_link,
            $string_html__credential_link,
            ($checkInTime==null) ? '' : $checkInTime['time12'],
            ($checkOutTime==null) ? '' : $checkOutTime['time12'],
        );
    }
    return $participants;
}

function wssr_flatten_users_array($users){
    foreach($users as $key=>$row){
        $users[$key] = array($row['first'].' '.$row['last']);
    }
    return $users;
}

function wssr_flatten_sr_list($list, $perPage){
    $firstId = $lastId = NULL;

    foreach($list['list'] as $key=>$row){
        $time = ws_parse_time($row['created']['date']);
        if($firstId === NULL){ $firstId = $key; }
        $lastId = $key; /*without check because it is last*/
        $rows[] = array(
           'id' => $key,
		   'category_id'=>$row['category']['catId'],
           'cell' =>
              array (
                0 => $key,
                1 => $row['category']['extDescription'],//['group'],
                2 => $row['status'],
                3 => $row['user']['firstName'].' '.$row['user']['lastName'],
                4 => $time['date'].' '.$time['time12']
              ),
        );
    }

    $data = array(
        //'page' => 1+floor($list['start']/$perPage),
        'total' => $list['count'],//1+floor($list['count']/$perPage), //TODO: check this is total value, not for current page
        'rows' => $rows,
        'params' => array(
            'firstId' => $firstId,
            'lastId' => $lastId,
        ),
    );
    return $data;
}

function wssr_flatten_attachments($list){
    $result = array();

    foreach($list as $row){
        $date = ws_parse_time($row['uploadDate']);
        $result[] = array(
            l($row['title'], 'getdocument/'.$row['id']),
            $date['date'],
        );
    }

    return $result;
}

function wssr_flatten_reservation($list){
    $result = array();
	$array__temp=array();
	
	$array__temp_=$list[0];
    for ($int__index=1;$int__index<count($list);$int__index++)
    {
    	if ($list[$int__index]['from']!==$array__temp_['to'])
    	{
    		$array__temp[]=$array__temp_;
    		$array__temp_=$list[$int__index];
    	}
    	else
    		$array__temp_['to']=$list[$int__index]['to'];
    }
	
    $array__temp[]=$array__temp_;
    
    
    $list=$array__temp;
    
    foreach($list as $row){
        $from = ws_parse_time($row['from']);
        $to = ws_parse_time($row['to']);

        $result[$from['date']][] = array(
            $from['date'],
            $from['time12'].' - '.$to['time12'],
        );
    }
    ksort($result);
    $array__temp=array();
    $int__index=0;
    foreach ($result as $key=>$row)
    {
    	$array__temp[$int__index][]=$key;
    	$array__temp_=array();
    	foreach ($row as $value)
    		$array__temp_[]=$value[1];
    	
    	$array__temp[$int__index][]=implode(', ',$array__temp_);
    	$int__index++;
    }
    $result=$array__temp;
    
/*    $temp=array();
	foreach ($result as $var__value)
    	foreach ($var__value as $var__value_)
    		$temp[]=$var__value_;
    $result=$temp;*/

    return $result;
}

function wssr_render_string($label, $data, $class = '', $check = true, $out = true){
    $result = '';
    $flag =  !$check || ($check && !empty($data));

    if($flag){
        $result = "<p class='$class'>$label: <span>$data</span></p>";
    }
    if($out){
        echo $result;
    }

    return $result;
}

function wssr_get_list_headers($srlist){
    $propertyList = get_user_properties();

    $list = array();
    $property = NULL;
    foreach($srlist as $id=>$item){
        if($item['propertyId'] !== $property){
            $property = $item['propertyId'];
            $list[$id] = $propertyList[$item['propertyId']]['property_name'];
        }
    }

    $listObject = array();
    foreach($list as $id=>$property){
        $listObject[] = array('#row'.$id, $property);//"['#row$id', '$property']";
    }

    return $listObject;
}

//Add common JS functions for SR lists and history
function wssr_history_js($colspan){
    drupal_add_js("
        function set_grid_headers(){
        headersList = gridHeaders;
            for(var id in headersList){
                $(headersList[id][0]).before('<tr><th colspan=\"".$colspan."\" class=\"flexiheader\"><div>'+headersList[id][1]+'</div></th></tr>');
            }
        }
    ", 'inline');
}
//should be common and declared in wsgeneral.
function add_jquery_multi(){
    general_add_css('jquery-ui/css/ui-lightness/jquery-ui-1.8.14.custom.css');

    general_add_js('jquery-162/jquery-162.min.js');
    general_add_js('jquery-safe/add-jquery-162.js');
    general_add_js('jquery-ui/js/jquery-ui-1.8.14.custom.min.js');
}

function js_multi_selector(){
    add_jquery_multi();
    general_add_css('multiselect/jquery.multiselect.css');
    general_add_js('multiselect/jquery.multiselect.js');
}


	function get_participant_image($image_id, $image_key)
    {
        $result=wssr_image_request("participant", array('image_id' => $image_id,'image_key' => $image_key));
    }

