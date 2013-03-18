<?php
function get_unsupported_srcat_ids($filter) {
    if ($filter=='hide_from_list') {
        return array();
    } elseif ($filter=='hide_details') {
        //return array('9170');
        //unfortunately we have to process Vendor Appointment SRs (9170)
        return array();
    } else
        return null;
}

function wssr_file_request($filter, $params, $rawData = null, $fpath = null, $fname = null){
    $wsUser = ws_get_user_data();
    $login=htmlentities($wsUser['login']);
    $passwd = htmlentities($wsUser['passwd']);
	$propertyId=($params && !empty($params['property_id'])) ? $params['property_id'] : null;
    if ($propertyId==null)
		$propertyId=wssr_get_property_id();
	$url=wsGetWSActionUrl();
	$bdebug=false;
    $rawDataTags="";
    $action = '';
    switch($filter){
        case 'getAnnouncementDocument':
            $anncId=$params['announcementId'];
            $actionArgs = "announcementId=\"$anncId\"";
            $action='getfile';
            break;
        case 'getfile' :
            $fileId=$params['fileId'];
            $actionArgs = " fileId=\"$fileId\"";
            $action='getfile';
            break;
        break;
    }

    $userInfoXML = <<<req
<?xml version="1.0" encoding="UTF-8"?><workspeed><loginname>$login</loginname><password>$passwd</password><emailAddress>aaa@workspeed.com</emailAddress><portalURL>http://www.rrr.com/</portalURL><errorURL>http://www.rrr.com/</errorURL><uniqueId>1234</uniqueId><action $actionArgs>$action</action>$rawDataTags</workspeed>
req;
    $globalErrorURL = "http://portalapps.buildingportalprod.com/sso/workspeed_sso_error_buckhead.asp";
    $data = 'userInfoXML='.urlencode($userInfoXML);
    $ch = curl_init();
    //$uuu=$url.'&'.$data;
    //getfile
    //$res=file_get_contents('http://localhost:7001/workspeed/jsp/filedocdownload.jsp?fileid=790765678');
    //$res=file_get_contents($uuu);
    //print_r($res);
    //die();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    //curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //$fname = 'karp.pdf';
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, "readHeader");
    $fo = fopen($fpath.'/'.$fname,'w');
    curl_setopt($ch, CURLOPT_FILE, $fo);
    //$res=curl_exec($ch);
    curl_exec($ch);
    //$filestream = curl_exec($ch);
    //reset_last_curl_error(array('description'=>curl_error($ch),'number'=>curl_errno($ch)));
	reset_last_curl_error($ch);
    $headers=lastCurlHeader('get');
    //$headers = curl_getinfo($ch);
    curl_close($ch);

     fclose($fo);
    lastCurlheader('clear');
    return array('fpath'=>$fpath, 'fname'=>$fname, 'info'=>array('attachment_filename'=>$headers['attachment_filename']));
}


function wssr_image_request($filter, $params, $rawData = null, $fpath = null, $fname = null)
{
    $wsUser = ws_get_user_data();
    $login=htmlentities($wsUser['login']);
    $passwd = htmlentities($wsUser['passwd']);
	$propertyId=($params && !empty($params['property_id'])) ? $params['property_id'] : null;
    if ($propertyId==null)
		$propertyId=wssr_get_property_id();
	$url=wsGetWSActionUrl();
	$bdebug=false;
    $rawDataTags="";
    $actionArgs="";
    $action = '';
    switch($filter)
    {
        case 'participant' :
            $rawDataTags = '<criteria filter_name="image_id" filter_value="'.$params['image_id'].'"></criteria>';
			$rawDataTags .= '<criteria filter_name="image_key" filter_value="'.$params['image_key'].'"></criteria>';
			$action='getimage';
            break;
    }
	
    $userInfoXML = <<<req
<?xml version="1.0" encoding="UTF-8"?><workspeed><loginname>$login</loginname><password>$passwd</password><emailAddress>aaa@workspeed.com</emailAddress><portalURL>http://www.rrr.com/</portalURL><errorURL>http://www.rrr.com/</errorURL><uniqueId>1234</uniqueId><action $actionArgs>$action</action>$rawDataTags</workspeed>
req;

    $globalErrorURL = "http://portalapps.buildingportalprod.com/sso/workspeed_sso_error_buckhead.asp";
    $data = 'userInfoXML='.urlencode($userInfoXML);
    $ch = curl_init();
//    print $userInfoXML;
//    die();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    //curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, "readHeader");

    //curl_setopt($ch, CURLOPT_FILE);
    //$res=curl_exec($ch);
    header("Content-type:image/x");
    print curl_exec($ch);
    //$filestream = curl_exec($ch);
    //reset_last_curl_error(array('description'=>curl_error($ch),'number'=>curl_errno($ch)));
    
	reset_last_curl_error($ch);
    //$headers=lastCurlHeader('get');
    //$headers = curl_getinfo($ch);
    curl_close($ch);

    lastCurlheader('clear');
    //return array('fpath'=>$fpath, 'fname'=>$fname, 'info'=>array('attachment_filename'=>$headers['attachment_filename']));
    //return;
}

function wssr_data_request($filter, $params = null, $rawData = null){
    $wsUser = ws_get_user_data();
    $login=htmlentities($wsUser['login']);
    $passwd = htmlentities($wsUser['passwd']);
	$propertyId=($params && !empty($params['property_id'])) ? $params['property_id'] : null;
    if ($propertyId==null)
		$propertyId=wssr_get_property_id();
	$url=wsGetWSActionUrl();
	$bdebug=false;
    $rawDataTags="";
    switch($filter){
        case 'userInfoAllProp':
			if ($propertyId!=null&&$propertyId!='')
				$actionArgs = "propertyId=\"$propertyId\"";
            $action = "userInfoAllProp";
            break;
        case 'userInfoAllPortalProp':
			if ($propertyId!=null&&$propertyId!='')
				$actionArgs = "propertyId=\"$propertyId\"";
            $action = "userInfoAllPortalProp";
            break;
        case 'buildingStructureAll':
            $action = "buildingStructureAll";
			//$bdebug=true;
            break;
/*			
        case 'getSubSpacesSubtree':
            $actionArgs = "propertyId=\"$propertyId\"";
            $action = "getSubSpacesSubtree";
            break;
        case 'buildingList':
            $actionArgs = "propertyId=\"$propertyId\"";
            $action = "buildingList";
            break;

        case 'floorList':
            $actionArgs = "propertyId=\"$propertyId\" blockId=\"$data\"";
            $action = "floorList";
            break;
        case 'suiteList':
            $actionArgs = "propertyId=\"$propertyId\" floorId=\"$data\"";
            $action = "suiteList";
            break;
*/
        case 'divisionsList':
            $actionArgs = "propertyId=\"$propertyId\"";
            $action = "divisionsList";
			//$bdebug=true;
            break;
        case 'requestType': //additional data param for filtering.
            $actionArgs = "propertyId=\"$propertyId\"";
            $action = "requestType";
            break;
        case 'entitiesList': //additional data param for filtering.
            $actionArgs = "propertyId=\"$propertyId\"";
            $action = "entitiesList";
            break;
        case 'srlist': //additional data param for filtering.
            //$actionArgs = "propertyId=\"$propertyId\"";
            $action = "srlist";
            break;
        case 'srcatlist': //additional data param for filtering.
            //$actionArgs = "propertyId=\"$propertyId\"";
            $action = "srcatlist";
            break;
        case 'srdetails': //additional data param for filtering.
            //$actionArgs = "propertyId=\"$propertyId\"";
            $action = "srdetails";
            $srId=$params['srId'];
            $actionArgs = "srId=\"$srId\"";
            break;
        case 'my_visitors': //additional data param for filtering.
            //$actionArgs = "propertyId=\"$propertyId\"";
            $action = "my_visitors";
            //$startDate=$params["startDate"];
            //if (empty($startDate))
            //    $startDate='today';
            //$startDate='yesterday';
            //$actionArgs = "startDate=\"$startDate\"";
            break;
        case 'all_visitors': //additional data param for filtering.
            //$actionArgs = "propertyId=\"$propertyId\"";
            $action = "all_visitors";
            //$startDate=$params["startDate"];
            //if (empty($startDate))
            //    $startDate='today';
            //$startDate='yesterday';
            //$actionArgs = "startDate=\"$startDate\"";
            break;
        case 'visitorSrDetails':
            $action = "visitorSrDetails";
            $srid = $params['srid'];
            $propertyId = $params['propertyId'];
            $actionArgs = "srId=\"$srid\"";
            $actionArgs .= " propertyId=\"$propertyId\"";
            break;
        case 'announcements':
            $action = "announcements";
            break;
        case 'announcementDetails':
            $action = "announcementDetails";
            $anncId=$params['announcementId'];
            $actionArgs = "announcementId=\"$anncId\"";
            break;
        case 'shrschedule':
            $action = "shrschedule";
            $resourceId = $params['resourceId'];
            //$propertyId = $params['propertyId'];
            $startDate = $params['startDate'];
            $actionArgs =  "resourceId=\"$resourceId\" propertyId=\"$propertyId\" startDate=\"$startDate\"";
            break;
        case 'SHRCategoryList':
            $action = "SHRCategoryList";
            break;
        case 'propertySHRInfoAll':
            $action = "propertySHRInfoAll";
            $actionArgs = "propertyId=\"$propertyId\"";
            break;
    }

    if (!empty($rawData)) { //?! Shouldn't it be out of switch? @Lex.
        $rawDataTags=$rawData;
    }
    $userInfoXML = <<<req
<?xml version="1.0" encoding="UTF-8"?><workspeed><loginname>$login</loginname><password>$passwd</password><emailAddress>aaa@workspeed.com</emailAddress><portalURL>http://www.rrr.com/</portalURL><errorURL>http://www.rrr.com/</errorURL><uniqueId>1234</uniqueId><action $actionArgs>$action</action>$rawDataTags</workspeed>
req;
    $globalErrorURL = "http://portalapps.buildingportalprod.com/sso/workspeed_sso_error_buckhead.asp";
    $data = 'userInfoXML='.urlencode($userInfoXML);
    $ch = curl_init();


    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $xml = curl_exec($ch);
    //reset_last_curl_error(array('description'=>curl_error($ch),'number'=>curl_errno($ch)));
	reset_last_curl_error($ch);
    curl_close($ch);

    //here should be data request settings before execution cURL query
    //return (xml2array($xml));
    //$bdebug=true;
	if ($bdebug) {
		print_r($xml);
		die();
	}
	if (is_ws_error()) {
        watchdog('wssr_curl', serialize(last_ws_error('get')));
		return false;
    }
	$respar=WSXMLHelper::xmlstring2array($xml);
	//get_last_ws_error_description()
	if ($respar===false || (!ws_check_data_request_result($respar))) {
		raise_ws_error_msg("Workspeed Service Not Available");
		return false;
	}
	return $respar;
}
function get_request_list_as_options($list) {
	$opt=array();
	foreach($list as $grpname=>$grplist) {
		$opt[$grpname]=array();
		foreach($grplist as $key=>$valar) {
			$opt[$grpname][$key]=$valar['description'];
		}
	}
	return $opt;
}
function get_request_list_as_flatlist($list) {
	$opt=array();
	foreach($list as $grpname=>$grplist) {
		foreach($grplist as $key=>$valar) {
			$opt[$key]=$valar['description'];
		}
	}
	return $opt;
}
function filter_request_list($list, $filterType) {
	$a=variable_get('enableBPRequest',null);
	$includeBP = !empty($a);
	
	if ($filterType=='standard') {
		foreach($list as $keygrp=>$srlist) {
			if ($srlist['9020']) //visitor
				unset($list[$keygrp]['9020']);
			if ($srlist['9039']) //purchase
				unset($list[$keygrp]['9039']);
			if ($srlist['9040']) //purchase
				unset($list[$keygrp]['9040']);
			if ($srlist['9331']) //purchase
				unset($list[$keygrp]['9331']);
			if ($srlist['9027']) //utility bill
				unset($list[$keygrp]['9027']);
			if ($srlist['9170']) //vendor appointment
				unset($list[$keygrp]['9170']);
			if ($srlist['9015'] && ($srlist['9015']['template']!='Standard' || 
										$srlist['9015']['data']=='external')) {//building pass
				unset($list[$keygrp]['9015']);
			}
			if ($srlist['9010']) {
				//if ($srlist['9010'] && $srlist['9010']['sharedYN']=='Y')
					unset($list[$keygrp]['9010']);
			}
			
			foreach($srlist as $srid=>$vals) {
				if ($vals['sharedYN']=='Y') {
					unset($list[$keygrp][$srid]);
				}
				elseif ($vals['template']=='Standard' || 
							$vals['template']=='EnhancedDirect' || 
							$vals['template']=='Direct') {
					$a='ok'; //it's ok
				} else {
					unset($list[$keygrp][$srid]);
				}
			}
			if ($list[$keygrp]==null || count($list[$keygrp])==0 ) {
				unset($list[$keygrp]);
			}
		}
	} elseif ($filterType=='visitor') {
		foreach($list as $keygrp=>$srlist) {
			if ($srlist['9020']) {
				return array(0=>array($keygrp=>array('9020'=>$srlist['9020'])));
			}
		}
		return null;
	} elseif ($filterType=='hvac') {
		foreach($list as $keygrp=>$srlist) {
			if ($srlist['9010']) {
				if ($srlist['9010'] && $srlist['9010']['sharedYN']=='Y')
					return array(0=>array($keygrp=>array('9010'=>$srlist['9010'])));

				return $srlist['9010'];
			}
		}
		return null;
	} elseif ($filterType=='shared') {
        $res=array();
        foreach($list as $keygrp=>$srlist) {
            foreach($srlist as $srid=>$srinfo) {
                if ($srid!='9010' && $srinfo['sharedYN']=='Y') {
                    $res[$srid] = $srinfo;
                }
            }
       }
       return empty($res) ? null : $res;
	}
	return $list;
}
function getCurrentUserRequestListTree($propertyId,$form_name) {
	$user_id=get_ws_user_id();
	if (!$user_id) //no ws_id - no SR
		return null;
	$requestList = ws_get_cache($propertyId, $user_id, null, 'sr_list_all');
	if (is_null($requestList)) {
	$requestList = wssr_get_request_list($propertyId);
		if (is_ws_error())
			return false;
		ws_set_cache_nonull($propertyId, $user_id, null, 'sr_list_all', $requestList);
	}
	if ($requestList)
	$requestList = filter_request_list($requestList,$form_name);
	return $requestList; //rerquest list tree
}

function wssr_get_request_list($propertyId){
    $data = wssr_data_request('requestType', array('property_id' => $propertyId) );
	
	if (is_ws_error())
    	return false;
    $srGroups = $data['Content'][0][SXML_CHILDLIST]['ServiceRequestTypeList'][0][SXML_CHILDLIST]['ServiceRequestGroup'];
    foreach($srGroups as $gData){
		$groupChilds=$gData[SXML_CHILDLIST];
        $groupDesc = $groupChilds['Description'][0][SXML_VALUE];
        foreach($groupChilds['ServiceRequestCategory'] as $cData){
            $catAttrs=$cData[SXML_ATTR];
			$catId = $catAttrs['id'];
            $catDesc = $cData[SXML_CHILDLIST]['ShortDescription'][0][SXML_VALUE];
            $catExtDesc = $cData[SXML_CHILDLIST]['ExtensiveDescription'][0][SXML_VALUE];
            $requests[$groupDesc][$catId] = array('description'=>$catDesc,
                                                    'extensiveDescription'=> $catExtDesc,
													'group'=>$catAttrs['group'],
													'id'=>$catAttrs['id'],
													'name'=>$catAttrs['name'],
													'sharedYN'=>$catAttrs['sharedYN'],
													'template'=>$catAttrs['template'],
													'data'=>$catAttrs['data'],
            );
        }
    }
    return $requests;
}

function wssr_get_shared_categories_list(){
    $sharedCat = array(
        'categories' => array(), //category data
        'sr' => array() //linked SR categories
    );
    $data = wssr_data_request('SHRCategoryList');
    $categories = $data['Content'][0][SXML_CHILDLIST]['PropertyResourceCategoryList'][0][SXML_CHILDLIST]['PropertyResourceCategorySummary'];

    foreach($categories as $catData){
        $cat = $catData[SXML_ATTR];
        $catName = $catData[SXML_VALUE];
        $sharedCat['categories'][$cat['id']] = array(
            'name' => $cat['name'],
            'required_cert_expiration_yn' => $cat['required_cert_expiration_yn'],
            'value' => $catName,
        );
        $sharedCat['sr'][$cat['service_request_category']] = $cat['id'];
    }

    return $sharedCat;
}

function wssr_get_shared_resources_list($sharedCatList, $propertyId){
    $defaultReservationBlock = 60; //use constant
    $sharedResources = array();
    $data = wssr_data_request('propertySHRInfoAll', array('property_id' => $propertyId));

    $resourcesData = $data['Content'][0][SXML_CHILDLIST]['PropertyResourceList'][0][SXML_CHILDLIST]['PropertyResourceSummary'];
    foreach($resourcesData as $resourceSummary){
        $resource = $resourceSummary[SXML_CHILDLIST];
        $catId = $resource['PropertyResourceCategory'][0][SXML_ATTR]['id'];

        //check if Shared Category is available
        if(empty($sharedCatList['categories'][$catId]))
            continue;

        $reservationBlock = $resourceSummary[SXML_ATTR]['reservationBlock'];

        //depends on tree view use categoryId->resourceId->data or spaceId->resourceId->data
        $sharedResources[$resourceSummary[SXML_ATTR]['id']] = array(
            'description' => $resource['Description'][0][SXML_VALUE],
            'reservationBlock' => $reservationBlock?$reservationBlock:$defaultReservationBlock,
            'categoryId' => $catId,
            'spaceId' => $resource['Space'][0][SXML_ATTR]['id'],

            'shared' => $resourceSummary[SXML_ATTR]['shared'],
            'amenity' => $resourceSummary[SXML_ATTR]['amenity'],
        );
    }

    return $sharedResources;
}

function getCurrentUserSharedResTree($propertyId) {
	$user_id=get_ws_user_id();
    $sharedResList = ws_get_cache($propertyId, $user_id, null, 'shres_list_all');
    //if we have list for current property - return
    if (!empty($sharedResList))
        return $sharedResList;


    $sharedCatList = wssr_get_shared_categories_list();
    $requestList = wssr_get_request_list($propertyId);
	if (empty($user_id) || empty($requestList)) //no ws_id or not propIds - no SR
		return null;
    if (is_ws_error())
        return false;
    $requestList = filter_request_list($requestList, 'shared');

    $sharedCat = array();
    foreach($requestList as $group){
        foreach($group as $sr=>$tmp){
            $catId = $sharedCatList['sr'][$sr];
            $sharedCat = $sharedCatList['categories'][$catId];
        }
    }

    $sharedResList = wssr_get_shared_resources_list($sharedCat, $propertyId);
    
	return $sharedResList;
}
//defaults to add JSON
//@karp: is not used?
function wssr_get_full_tree_data(){
    $data = wssr_data_request('buildingStructureAll', null); 
	if (is_ws_error())
    	return false;
	$buildingListData = $data['Content'][0][SXML_CHILDLIST]['PropertyBuildings'][0][SXML_CHILDLIST]['BuildingList'];
    //move it to cache.
    $tree = array();
    foreach($buildingListData as $pData){
		$propId=$pData[SXML_ATTR]['property_id'];
		$tree[$propId] = wssr_parse_property_data($pData);
		//@karp: replace with correct checking
		$tree[$propId]['name']=$_SESSION['wsUserInfo']['properties'][$propId]['property_name'];
    }
    return $tree;
}
function wssr_full_tree_simple($tree, $defSuiteId=null){
    $simpleTree = array();
    $default = false;
    foreach($tree as $pId=>$property){
        $simpleTree[$pId]['text'] = $property['name'];
        $simpleTree[$pId]['class'] = 'property';
        $buildings = array();
		foreach($property['buildings'] as $bldId=>$building){
			$buildings[$bldId]['text'] = $building['name'];
            $buildings[$bldId]['class'] = 'building';
            $blocks = array();
			foreach($building['blocks'] as $blkId=>$block){
				$blocks[$blkId]['text'] = $block['name'];
                $blocks[$blkId]['class'] = 'block';
                $floors = array();
				foreach($block['floors'] as $flId=>$floor){
					$floors[$flId]['text'] = $floor['name'];
                    $floors[$flId]['class'] = 'floor';
                    $suites = array();
					foreach($floor['suites'] as $suiteId=>$suite){
						$suiteName = $suite['name'];
						$suites[$suiteId]['text'] = $suiteName;
                        $suites[$suiteId]['class'] = 'suite';
						if (!$default) {
							if ($defSuiteId !=null && $defSuiteId == $suiteId ) {
								$default = array(
                                    'property_id' => $pId,
                                    'path' => array($pId, $bldId, $blkId, $flId, $suiteId),
								);
							} 
						}
					}
                    $floors[$flId]['items'] = $suites;
				}
                $blocks[$blkId]['items'] = $floors;
			}
            $buildings[$bldId]['items'] = $blocks;
		}
        $simpleTree[$pId]['items'] = $buildings;
    }
	if (!$default) {
		reset($tree);
		$prop=current($tree);
		$pId=key($tree);
		$bld=current($prop['buildings']);
		$bldId=key($prop['buildings']);
		$blk=current($bld['blocks']);
		$blkId=key($bld['blocks']);
		$fl=current($blk['floors']);
		$flId=key($blk['floors']);
		$s=current($fl['suites']);
		$sId=key($fl['suites']);
		$default = array(
			'property' => $prop['name'],
			'property_id' => $pId,
			'address' => $bld['name'].', '.$blk['name'].', '.$fl['name'].', '.$s['name'],
			'suiteId' => $sId,
			'path' => array($pId, $bldId, $blkId, $flId, $sId),
		);
	}
    return array('simpleTree' => $simpleTree, 'default' => $default);
}


/*
 * Return defaults for the tree
 */
/* current:
[default] => Array
        (
            [property] => Cinema Paradiso
            [property_id] => 282197
            [address] => BlgJura, BlgJura, B, 01
            [suiteId] => 282458
            [path] => Array
                (
                    [0] => 282197
                    [1] => 282455
                    [2] => 282456
                    [3] => 282457
                    [4] => 282458
                )

        )
*/
function ws_get_tree_defaults($tree, $defaultValue = null){

    if($defaultValue === null){
        $chain = ws_tree_linked_items($tree, 'root', true);
        foreach($chain as $key => $val){
            $path[] = $key;
        }
    } else {
        $chain = ws_tree_linked_items($tree);
        $path = ws_shared_tree_path($chain, array($defaultValue));
        $path = array_reverse($path);
    }

    //Quickfix. Also, we should return real data as array and prepare it before rendering.
    $addrArray = ws_get_tree_defaults_by_path($tree, array_reverse($path));
    $property = array_shift($addrArray);
    //$property = ''; // uncomment if property changed too.
    //$address = implode(', ', $addrArray);
    $address = end($addrArray);
    $property_id = $path[0];
    $suiteId = $path[count($path) - 1];

    return array(
        'property' => $property,
        'property_id' => $property_id,
        'address' => $address,
        'suiteId' => $suiteId,
        'path' => $path,
    );
}

function ws_get_tree_defaults_by_path($tree, $path){
    $result = array();
    $next = array_pop($path);
    if(count($path) > 0){
        $result = array($tree[$next]['text']);
        $result = array_merge($result, ws_get_tree_defaults_by_path($tree[$next]['items'], $path));
    } else {
        $result = array($tree[$next]['text']);
    }
    return $result;
}


/*
 * Return array with links child => parent. Used in shared resources merge with origin tree.
 * $returnDefault = true for taking default element
 */
function ws_tree_linked_items($tree, $parent='root', $returnDefault = false){
    $result = array();
    foreach($tree as $id=>$elem){
        $result[$id] = $parent;
        if(!empty($elem['items'])){
            //do NOT use array_merge() cause it rewrite numeric keys.
            $result = $result + ws_tree_linked_items($elem['items'], $id, $returnDefault);
        }
        if($returnDefault){ return $result; }
    }

    return $result;
}

function ws_resource_to_top($tree){
    $result = array();
    $temp = array();
    foreach($tree as $id=>$item){
        if(!empty($item['items']))
            $item['items'] = ws_resource_to_top($item['items']);

        if($item['class'] == 'shared')
            $result[$id] = $item;
        else
            $temp[$id] = $item;
    }

    foreach($temp as $id => $item)
        $result[$id] = $item;

    return $result;
}

/*
 * Merge shared resources with origin's simpleTree.
 * //ToDo: put trees getter into the current function.
 * //ToDo: getCurrentUserRequestListTree should be fixed and used (or move code with cache reading, categories filtering etc).
 *
 * ToDo: BUGS!
 * ? last element in $path is a string, other - int. Why? Is it trouble?
 */
function ws_shared_resources_tree($simpleTree, $shResData, $resourceToTop = true){
    $chain = ws_tree_linked_items($simpleTree);
    //propId currently not used, we could remove it.
    foreach($shResData as $propId => $shProp){
        foreach($shProp as $shId => $shared){//print_r($chain); die;
            $shared['id'] = $shId;
            $path = ws_shared_tree_path($chain, array($shared['spaceId']));
            //if spaceId not in the tree
            if($path == null) { $path = array($propId); }
            ws_shared_tree_append($simpleTree, $shared, $path);
       }
    }

    ws_shared_resources_tree_cleanup($simpleTree);

    if($resourceToTop)
        $simpleTree = ws_resource_to_top($simpleTree);
    return $simpleTree;
}

/*
 * Iterate from bottom to top with $chain array, return path for element attachment
 * Path is reversed. array_shift and _unshift could be used too, but it looks like slower solution.
 */
function ws_shared_tree_path($chain, $path){
    if(!$chain[end($path)]){ return null; }
    if($chain[end($path)] != 'root'){
        array_push($path, $chain[end($path)]);
        $path = ws_shared_tree_path($chain, $path);
        //reset($path); //maybe not needed, resets internal pointer
    }
    return $path;
}

/*
 * Iterate through array, adding USED flag, and attach Shared Resource to the branch
 */
function ws_shared_tree_append(&$shResBranch, $shared, $path){
    $next = array_pop($path);
    $shResBranch[$next]['used'] = true;
    if(count($path)>0){
        ws_shared_tree_append($shResBranch[$next]['items'], $shared, $path);
    } else {
        $shResBranch[$next]['items'][$shared['id']] = array(
            'used' => true,
            'text' => $shared['description'],
            'class' => 'shared',
        );
    }
}

/*
 * Iterate through the tree, and remove unused elements
 */
function ws_shared_resources_tree_cleanup(&$shResBranch){
    foreach($shResBranch as $id=>$branch){
        if(!$branch['used']){
            unset($shResBranch[$id]);
        } else {
            unset($shResBranch[$id]['used']);
            if($branch['items']) {
                ws_shared_resources_tree_cleanup($shResBranch[$id]['items']);
            }
        }
    }
}

function ws_parse_leases($availableLeases) {
    $all_leases = array();
    foreach($availableLeases['LeaseList'] as $leaseList) {
		//property level 
		$pId=$leaseList[SXML_ATTR]['property_id'];
		$property_leases = array();
		foreach($leaseList[SXML_CHILDLIST]['LeaseSummary'] as $leaseSummary) {
			$entityId=$leaseSummary[SXML_CHILDLIST]['EntityId'][0][SXML_VALUE];
			$leaseActive=$leaseSummary[SXML_ATTR]['active'];
			$leaseId=$leaseSummary[SXML_ATTR]['id'];
			if ($leaseActive=='yes') {
				$property_leases[$entityId][]=$leaseId;
			}
		}
		$all_leases[$pId] = empty($property_leases) ? false : $property_leases;
	}
	return empty($all_leases) ? null : $all_leases;
}
function wssr_parse_property_data($data){
    $property = array();
	$property['buildings'] = array();
    foreach($data[SXML_CHILDLIST]['BuildingSummary'] as $bData){
        $buildingId = $bData[SXML_ATTR]['id'];
        $property['buildings'][$buildingId]['name'] = $bData[SXML_CHILDLIST]['Description'][0][SXML_VALUE];
        $property['buildings'][$buildingId]['blocks'] = array();
        foreach($bData[SXML_CHILDLIST]['BlockList'][0][SXML_CHILDLIST]['BlockSummary'] as $blockData){
            $blockId = $blockData['@attributes']['id'];
            $tBlockData['name'] = $blockData[SXML_CHILDLIST]['Description'][0][SXML_VALUE];
            $tBlockData['floors'] = array();
            foreach($blockData[SXML_CHILDLIST]['FloorList'][0][SXML_CHILDLIST]['FloorSummary'] as $fData)
			{
                $floorId = $fData['@attributes']['id'];
                $tFloorData['name'] = $fData[SXML_CHILDLIST]['Description'][0][SXML_VALUE];
				$tFloorData['suites'] = array();
                if($fData[SXML_CHILDLIST]['SuiteList'][0][SXML_CHILDLIST]){
                    foreach($fData[SXML_CHILDLIST]['SuiteList'][0][SXML_CHILDLIST]['SuiteSummary'] as $sData){
						$leaseId = $sData[SXML_ATTR]['lease_id'];
						$tSuiteData = array();
						$tSuiteData['name'] = $sData[SXML_CHILDLIST]['Description'][0][SXML_VALUE];
						$tSuiteData['space_category'] = $sData[SXML_ATTR]['space_category_id'];
						if ($leaseId)
							$tSuiteData['lease_id']=$leaseId;
                        $tFloorData['suites'][$sData[SXML_ATTR]['id']] = $tSuiteData;
                    }
                }
                if($fData[SXML_CHILDLIST]['CommonSpaceList'][0][SXML_CHILDLIST]){
					//$tFloorData['common_spaces'] = array();
					//$tFloorData['suites'] = array();
                    foreach($fData[SXML_CHILDLIST]['CommonSpaceList'][0][SXML_CHILDLIST]['CommonSpaceSummary'] as $sData){
                        $suiteId = $sData['@attributes']['id'];
						$leaseId = $sData[SXML_ATTR]['lease_id'];
                        $suiteName =  $sData[SXML_CHILDLIST]['Description'][0][SXML_VALUE];
                        $tFloorData['suites'][$suiteId] = array('name'=>$suiteName);
						if ($leaseId)
							$tFloorData['suites'][$suiteId]['lease_id']=$leaseId;
						$tFloorData['suites'][$suiteId]['space_category'] = $sData[SXML_ATTR]['space_category_id'];
                    }
                }
                $tBlockData['floors'][$floorId] = $tFloorData;
            }
            $property['buildings'][$buildingId]['blocks'][$blockId] = $tBlockData;
        }

    }
    return $property;
}


function get_ws_leases($property_id){
	$data = ws_get_cache($property_id, null, null, 'entity_leases');
	if (is_null($data)) {
		//$data=ws_get_user_building_tree_all();
		$data = ws_get_cache($property_id, null, null, 'entity_leases');
	}
	return $data;
}

/*
function get_ws_leases_all(){
	$userProps=get_user_properties();
	foreach($userProps as $pId=>$pInfo) {
		$data = ws_get_cache($pId, null, null, 'entity_leases');
		//if ($data)
	}
	
	$data = ws_get_cache($property_id, null, null, 'entity_leases');
	if (!$data) {
		
		//print_r(' -- $data from cache is null - requesting  -- ');
	}
	return $data;
}
function ws_get_user_building_tree_all($userId, $entityId) {
	$data = ws_get_cache(-1, -1, -1, 'building_structure');
	if (!$data) {
		//print_r(' -- $data from cache is null - requesting  -- ');
		$data = wssr_data_request('buildingStructureAll', null);
		$data_leases=ws_parse_leases($data['Content'][0][SXML_CHILDLIST]['AvailableLeases'][0][SXML_CHILDLIST]);

		if ($data_leases) {
			ws_set_cache($property_id, null, null, 'entity_leases', $data_leases);
		} else {
			ws_set_cache($property_id, null, null, 'entity_leases', false);
		}
		
		if ($data_leases) {
			ws_set_cache($property_id, null, null, 'building_structure', $data);
			ws_set_cache($property_id, null, null, 'entity_leases', $data_leases);
		} else {
			ws_set_cache($property_id, null, null, 'building_structure', false);
			ws_set_cache($property_id, null, null, 'entity_leases', false);
		}
	}
	return $data;
}
*/
//@karp: approved
function wsapp_get_building_structure_all($getType=null, $propId=null) {
	//$getType=null, $propId=null - are just params for return values, 
	//	not for the request to the WS Application
	$data = wssr_data_request('buildingStructureAll', null);
	if (is_ws_error())
    	return false;
	$data_leases=ws_parse_leases($data['Content'][0][SXML_CHILDLIST]['AvailableLeases'][0][SXML_CHILDLIST]);
	$data_building=ws_parse_building_all($data['Content'][0][SXML_CHILDLIST]['PropertyBuildings'][0][SXML_CHILDLIST]['BuildingList']);
	$userProps=get_user_properties();
	if (empty($userProps) || !is_array($userProps))
		return null;
	foreach($userProps as $pId=>$pInfo) {
		if ($data_building[$pId])
			ws_set_cache($pId, null, null, 'building_structure', $data_building[$pId]);
		else
			ws_set_cache($pId, null, null, 'building_structure', false);
		
		if ($data_leases[$pId]) {
			ws_set_cache($pId, null, null, 'entity_leases', $data_leases[$pId]);
		} else {
			ws_set_cache($pId, null, null, 'entity_leases', false);
		}
	}
	if ($getType=='entity_leases'){
		return empty($propId) ? $data_leases : $data_leases[$propId];
	} elseif ($getType=='building_structure') {
		return empty($propId) ? $data_building : $data_building[$propId];
	} else { return empty($propId) ? array('building_structure'=>$data_building, 
						'entity_leases'=>$data_leases) :
						array('building_structure'=>$data_building[$propId], 
						'entity_leases'=>$data_leases[$propId]);
	}
	return null;
}

function ws_parse_building_all($buildingListData){
    $tree = array();
    foreach($buildingListData as $pData){
		$propId=$pData[SXML_ATTR]['property_id'];
		$tree[$propId] = wssr_parse_property_data($pData);
		//@karp: replace with correct checking
		$tree[$propId]['name']=$_SESSION['wsUserInfo']['properties'][$propId]['property_name'];
    }
    return $tree;
}
function ws_get_user_leases() {
	$res = array();
	$props=get_user_properties();
	if (empty($props) || !is_array($props))
		return null;
	//$propIds=array_keys($props);
	foreach($props as $pId=>$pInfo) {
		$pData = ws_get_cache($pId, null, null, 'entity_leases');
		if (is_null($pData)) {
			$pData=wsapp_get_building_structure_all('entity_leases', $pId);
			if (is_ws_error())
				return false;
		}
		if ($pData)
			$res[$pId]=$pData;
	}
	
	return $res;
}
//@karp: approved
function ws_get_user_building_structure() {
	$res = array();
	$props=get_user_properties();
	if (empty($props) || !is_array($props))
		return null;
	//$propIds=array_keys($props);
	foreach($props as $pId=>$pInfo) {
		$pData = ws_get_cache($pId, null, null, 'building_structure');
		if (is_null($pData)) {
			$pData=wsapp_get_building_structure_all('building_structure', $pId);
			if ($pData===false)
				return false;
		}
		if ($pData)
			$res[$pId]=$pData;
	}
	return $res;
}
//@karp: approved
function ws_get_filtered_user_building_structure($mode=null, $srFilter=null) {
	$all_leases=ws_get_user_leases();
	if (is_ws_error())
		return false;
	if (empty($all_leases))
		return null;
		
	$user_leases=get_entity_leases($all_leases, get_user_entity_id());
	$building_structure_all=ws_get_user_building_structure();
	if (is_ws_error())
		return false;
	if (empty($building_structure_all))
		return null;
    $propList=null;
    if (!empty($srTypeStr)) {
        $pIds=array_keys($building_structure_all);
        $propList=ws_filter_properies_by_sr_category($propList, $srFilter);
    }
	$stats=array();
	$user_building_structure=filter_building_structure($building_structure_all, $user_leases, $stats, $mode, $propList);
	return $user_building_structure;
}
function ws_get_structure_and_sr() {
	$all_leases=ws_get_user_leases();
	$user_leases=get_entity_leases($all_leases, get_user_entity_id());
	$building_structure_all=ws_get_user_building_structure();
	$stats=array();
	$user_building_structure=filter_building_structure($building_structure_all, $user_leases, $stats);
	if ($user_building_structure===false)
		return false;
	$pIds=array_keys($user_building_structure);
	$bStd=false;
	$bVis=false;
	$bHvac=false;
    $bShared=false;
	foreach($pIds as $pid) {
		if (!$bStd) {
			$sr=getCurrentUserRequestListTree($pid,'standard');
			$bStd=!empty($sr);
		}
		if (!$bVis) {
			$sr=getCurrentUserRequestListTree($pid,'visitor');
			$bVis=!empty($sr);
		}
		if (!$bHvac) {
			$sr=getCurrentUserRequestListTree($pid,'hvac');
			$bHvac=!empty($sr);
		}
        if (!$bShared) {
            $sr=getCurrentUserRequestListTree($pid,'shared');
            $bShared=!empty($sr);
	}
	}
    //"sr_view_all", "sr_view_for_route_to_entity", "sr_cancheck_in_visitors_strict", "sr_manage_visitors"};
    //private String[] TENANT_VISITOR_INFO_ATTRS = {SwitchesConstants.SR_CANCHECK_IN_VISITORS_STRICT,
    //        "sr_manage_visitors"};)
    $allSR=false;
    $allVisitor=false;
    $allSRProps=get_user_properties_with_attribute(array('sr_view_all', 'sr_view_for_route_to_entity'));
    $allVisitorProps=get_user_properties_with_attribute(array('sr_cancheck_in_visitors_strict', 'sr_manage_visitors'));
    $allSR = !empty($allSRProps);
    $allVisitor = !empty($allVisitorProps);
    $myVisitors = $allVisitor;
    if (!$myVisitors) {
        $myVisitorsProps=get_user_properties_with_attribute(array('sr_view_visitor_list'));
        $myVisitors = !empty($myVisitorsProps);
    }
	return array('standard'=>$bStd, 'visitor'=>$bVis, 'hvac'=>$bHvac, 'shared'=>$bShared,
                 'all_visitors'=>$allVisitor, 'all_requests'=>$allSR, 'my_visitors'=>$myVisitors);
}

//@karp: approved
function get_entity_leases($all_leases, $entity_id) {
	$res=array();
	foreach ($all_leases as $pId=>$propLeases) {
		if ($propLeases[$entity_id])
			$res=array_merge($res, $propLeases[$entity_id]);
	}
	return $res;
}
//@karp: approved
function filter_building_structure($building_structure, $leases, &$stats, $mode=null, $propList=null) {
	$res=array();
	$bRemoveAllCommon=false;
	$bRemoveFloorsWithOnlyCommon=true;
	if ($mode=='no_common_spaces')
		$bRemoveAllCommon=true;
	if ($mode=='all_common_spaces')
		$bRemoveFloorsWithOnlyCommon=false;

	foreach($building_structure as $pId=>$pInfo) {
        if ($propList!==null && !in_array($pId, $propList))
            continue;

		$new_bld=array();
		foreach($pInfo['buildings'] as $bldId=>$bldInfo) {
			$new_blk=array();
			foreach($bldInfo['blocks'] as $blkId=>$blkInfo) {
				$new_floors=array();			
				foreach($blkInfo['floors'] as $fId=>$fInfo) {
					$new_suites=array();
					$suiteExists=false;
					foreach($fInfo['suites'] as $sId=>$sInfo) {
						if (($sInfo['lease_id'] && in_array($sInfo['lease_id'], $leases) ) || 
							((!$bRemoveAllCommon) && $sInfo['space_category']!='2001') ) {
								//print_r($fInfo['suites'][$sId]);
								$new_suites[$sId]=$sInfo;
								if ($sInfo['space_category']=='2001')
									$suiteExists=true;
								//unset($building_structure[$pId]['buildings'][$bldId]['blocks'][$blkId]['floors'][$fId]['suites'][$sId]);
								//unset($fInfo['suites'][$sId]);
								//print_r(' - ');
								//print_r($fInfo['suites'][$sId]);
								//print_r(' - ;');
						}
					}
					//unset($building_structure[$pId]['buildings'][$bldId]['blocks'][$blkId]['floors'][$fId]['suites'][$sId]);
					if (!empty($new_suites)) {
						if ( $suiteExists || (!$bRemoveFloorsWithOnlyCommon) )
						$new_floors[$fId]=array('name'=>$fInfo['name'], 'suites'=>$new_suites);
					}
					//if (empty($fInfo['suites']) || count($fInfo['suites'])==0) {
					//	unset($building_structure[$pId]['buildings'][$bldId]['blocks'][$blkId]['floors'][$fId]);
					//}
					//if (empty($fInfo['suites']))
				}
				
				//if (empty($blkInfo['floors'])) unset($bldInfo['blocks'][$blkId]);
				//if (empty($blkInfo['floors'])) unset($building_structure[$pId]['buildings'][$bldId]['blocks'][$blkId]);
				if (!empty($new_floors)) {
					$new_blk[$blkId]=array('name'=>$blkInfo['name'], 'floors'=>$new_floors);
				}
			}
			//if (empty($bldInfo['blocks'])) unset($pInfo['buildings'][$bldId]);
			//if (empty($bldInfo['blocks'])) unset($building_structure[$pId]['buildings'][$bldId]);
			if (!empty($new_blk)) {
				$new_bld[$bldId]=array('name'=>$bldInfo['name'], 'blocks'=>$new_blk);
			}
		}
		//if (empty($pInfo['buildings'])) unset($building_structure[$pId]);
		//if (empty($pInfo['buildings'])) unset($building_structure[$pId]);
		if (!empty($new_bld))
			$res[$pId]=array('name'=>$pInfo['name'], 'buildings'=>$new_bld);
	}
    return empty($res) ? false : $res;
}

function last_ws_error($action, $val=null) {
	static $array__cached_error;
	static $ws_error_flag; //is not used
	
	if ($action=='set') {
		$array__cached_error=$val;
		$ws_error_flag=true;
	} elseif ($action=='get') {
		return $array__cached_error;
	} elseif ($action=='clear') {
		unset($array__cached_error);
		unset($ws_error_flag);
	} elseif($action=='state') {
		return !empty($array__cached_error);
	}
}

function clear_last_ws_error() {
	return last_ws_error('clear');
}

function reset_last_curl_error($ch)
{
	if (curl_errno($ch)!=0)
		last_ws_error('set',array('source'=>'curl', 'description'=>curl_error($ch),'number'=>curl_errno($ch)));
	else
		last_ws_error('clear');	
}
/*
function set_ws_error($array__error)
	{
		if ($array__error)
			last_ws_error('set',$array__error);
		else
			last_ws_error('clear');
}
*/
function reset_last_ws_error($array__error)
{
    if ($array__error && ($array__error['number']!=0 || isset($array__error['description']))) {
    	last_ws_error('set',$array__error);
	} else {
    	clear_last_ws_error();
	}
}

function raise_ws_error_msg($msg)
{
	if ($msg) {
		last_ws_error('set',array('description'=>$msg,'number'=>-1));
	}
}

function get_last_ws_error_description()
{
	$array__error=last_ws_error('get');
	if ($array__error) {
		if ($array__error['source']=='curl' && (($array__error['number']>=1 && 
						$array__error['number']<10)
				|| $array__error['number']==22 
				|| $array__error['number']==28 || $array__error['number']==52))
		return "Workspeed Service Not Available";
	else
			return $array__error['description']; 
}
	else
		return ""; 
}

function ws_check_data_request_result(&$result)
	{
		if (!$result)
			return null;
		else if (is_array($result))
		{
			if ($result['Content'])
				return true;
			else
				return null;
		}
		else
			return null;
	}
function is_ws_error() { //true - if error
	return last_ws_error('state'); //or is_set()?
}
	
function check_ws_response($resp_array) { //true - correct
	return (!get_last_ws_error_description() && ws_check_data_request_result($resp_array));
}

function is_failed(&$val) {
	return ($val===false);
}

//
function ws_filter_properies_by_sr_category($propList, $srTypeStr) {
    //$pIds=array_keys($user_building_structure);
    if (empty($propList))
        return null;
    $res=array();
    foreach($propList as $pid) {
        $sr=getCurrentUserRequestListTree($pid,$srTypeStr);
        if (!empty($sr))
            $res[]=$pid;
    }
}

//

	function WSSR_RenderReviewTableGrid($string__title,$array__column_title,$array__row,$array__column_width=null)
	{
		if (count($array__row)==0)
			return "";
		
		$string_html__thead="";
		if (count($array__column_title)>0)
		{
			$array__thead=array();
		    $int__temp_index=0;
		    foreach ($array__column_title as $var__value)
		    {
		    	$array__thead[]='<th class="flexiheader css_th__column-'.$int__temp_index.($int__temp_index==count($array__column_title)-1?' css_th__last_column':'').'"><div>'.$var__value.'</div></th>';
		    	$int__temp_index++;
		    }
		    $array__thead[]='<th class="flexiheader css_th__blank_column"><div></div></th>';
		    $string_html__thead='<tr>'.implode('',$array__thead).'</tr>';
		}
		
		
	    $array__tbody=array();
		foreach ($array__row as $var__key=>$array__current_row)
	    {
	    	$int__index=0;
	    	foreach ($array__current_row as $string__cell)
	    	{
	    		$array__tbody[$var__key][]='<td '.($array__column_width[$int__index]?'style="width:'.$array__column_width[$int__index].'"':'').'><div>'.$string__cell.'</div></td>';
	    		$int__index++;
	    	}
	    	
	    	if (count($array__current_row)>1)
	    		$array__tbody[$var__key][]='<td><div></div></td>';
			
	    	$array__tbody[$var__key]='<tr>'.implode('',$array__tbody[$var__key]).'</tr>';
	    }
		
		$string_html__result=
	   		'<div class="flexigrid">
				<div class="hDiv">
					<div class="hDivBox">
						<table cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th><div>'.$string__title.':</div></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="bDiv">
					<table class="css_table__review_control" cellpadding="0" cellspacing="0">
						'.$string_html__thead.'
						'.implode('',$array__tbody).'
					</table>
				</div>
			</div>';
		return $string_html__result;
	}
	
	function WSSR_RenderReviewTableGridAnnouncement($string__title,$array__column_title,$array__row_group,$array__column_width=null)
	{
		if (count($array__row_group)==0)
			return "";
		
		$array__property_list=wsann_get_property_list();
		
		$string_html__columns_title_row="";
		if (count($array__column_title)>0)
		{
			foreach ($array__column_title as $var__value)
				$string_html__columns_title_row.='<th><div>'.$var__value.'</div></th>';
			$string_html__columns_title_row='<tr class="css_tr__columns_title_row">'.$string_html__columns_title_row."</tr>";
		}
		
	    $array__tbody=array();
	    foreach ($array__row_group as $var__key=>$var__value)
	    {
	    	$int__property_id=$var__key;
	    	$array__tbody[$int__property_id]='<tr><th class="flexiheader" colspan="4"><div>'.$array__property_list[$int__property_id]['property_name'].'</div></th></tr>';
			foreach ($var__value as $var__key_=>$array__current_row)
		    {
		    	$int__index=0;
		    	$array__temp=array();
		    	foreach ($array__current_row as $string__cell)
		    	{
		    		$array__temp[]='<td '.($array__column_width[$int__index]?'style="width:'.$array__column_width[$int__index].'"':'').'><div>'.($int__index==0?$string__cell:$string__cell).'</div></td>';
		    		$int__index++;
		    	}
		    	
		    	$array__tbody[$int__property_id].='<tr>'.implode('',$array__temp).'</tr>';
		    }
	    }
	    
	    $string_html__title="";
	    if ($string__title)
	    	$string_html__title='<div class="hDiv">
					<div class="hDivBox">
						<table cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th><div>'.$string__title.':</div></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>';
		
		$string_html__result=
	   		'<div class="flexigrid">
				'.$string_html__title
				.'<div class="bDiv">
					<table class="css_table__review_control" cellpadding="0" cellspacing="0">
						'.$string_html__columns_title_row
						.implode('',$array__tbody).'
					</table>
				</div>
			</div>';
		return $string_html__result;
	}

    //it looks totally strange with row structure like {name=>..., value=>..., attribute=>...}
	function WSSR_RenderReviewTableGridSummary($string__title,$array__row,$array__column_width=null)
	{
		if (count($array__row)==0)
			return "";
		
		$string_html__thead='<tr><th class="flexiheader" colspan="2"><div>&nbsp;</div></th></tr>';
		
	    $array__tbody=array();
		foreach ($array__row as $var__key=>$array__current_row)
	    {
            $attrString = '';
            if(!empty($array__current_row['attribute'])){
                foreach($array__current_row['attribute'] as $attrName => $attrVal)
                    $attrString .= " '$attrName'='$attrVal'";
                unset($array__current_row['attribute']);
            }

	    	$int__index=0;
	    	foreach ($array__current_row as $string__cell)
	    	{
	    		$array__tbody[$var__key][]='<td '.($array__column_width[$int__index]?'style="width:'.$array__column_width[$int__index].'"':'').'><div>'.($int__index==0?'<b>'.$string__cell.':</b>':$string__cell).'</div></td>';
	    		$int__index++;
	    	}
	    	
	    	$array__tbody[$var__key]='<tr'.$attrString.'>'.implode('',$array__tbody[$var__key]).'</tr>';
	    }
		
		$string_html__result=
	   		'<div class="flexigrid">
				<div class="hDiv">
					<div class="hDivBox">
						<table cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th><div>'.$string__title.':</div></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="bDiv">
					<table class="css_table__review_control" cellpadding="0" cellspacing="0">
						'.$string_html__thead.'
						'.implode('',$array__tbody).'
					</table>
				</div>
			</div>';
		return $string_html__result;
	}
	
	function WSSR_RenderReviewTableBlock($string__title,$array__row)
	{
		if (count($array__row)==0)
			return "";
		$array__tbody=array();
		foreach ($array__row as $var__key=>$array__current_row)
			if ($array__current_row['title'] && $array__current_row['value'])
	    		$array__tbody[$var__key]='<tr '.($array__current_row['attribute']['class']?'class="'.$array__current_row['attribute']['class'].'"':'').'><td class="css_td__title"><div>'.$array__current_row['title'].':</div></td><td class="css_td__value"><div>'.$array__current_row['value'].'</div></td></tr>';
	    if (count($array__tbody)==0)
			return "";
		
		$string_html__result=
	   		'<div class="flexigrid css_div__flexigrid-block">
				<div class="hDiv">
					<div class="hDivBox">
						<table cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th><div>'.$string__title.'</div></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="css_div__group">
					<table cellpadding="0" cellspacing="0">
						'.implode('',$array__tbody).'
					</table>
				</div>
			</div>';
		return $string_html__result;
	}
	
	function WSSR_RenderReviewTableDescription($string__title,$string__source)
	{
		if (!is_string($string__source))
			return "";	    
		
		$string_html__result=
	   		'<div class="flexigrid css_div__flexigrid-description">
				<div class="hDiv">
					<div class="hDivBox">
						<table cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th><div>'.$string__title.'</div></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="css_div__group css_div__group-description">
					'.$string__source.'
				</div>
			</div>';
		return $string_html__result;
	}
	
	function WSSR_RenderReviewTableDocumentBlock($string__title,$array__link,$bool__icon_presence_status=true)
	{
		if (count($array__link)==0)
			return "";
		$array__tbody=array();
		foreach ($array__link as $var__key=>$array__value)
	    	$array__tbody[$var__key]='<tr><td class="css_td__value"><div><a href="'.$array__value['url'].'" title="'.$array__value['title'].'">'.$array__value['title'].'</a></div></td></tr>';
	    
		
		$string_html__result=
	   		'<div class="flexigrid css_div__flexigrid-block '.($bool__icon_presence_status?'css_div__flexigrid-block-document':'').'">
				<div class="hDiv">
					<div class="hDivBox">
						<table cellpadding="0" cellspacing="0">
							<thead>
								<tr>
									<th><div>'.$string__title.'</div></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="css_div__group">
					<table cellpadding="0" cellspacing="0">
						'.implode('',$array__tbody).'
					</table>
				</div>
			</div>';
		return $string_html__result;
	}


