<?php
	error_reporting(E_ALL);
	require_once('mysql_class.php');
	$sql = new db;
	$sql->db_Connect($mySQLserver, $mySQLuser, $mySQLpassword, $mySQLdefaultdb); 

	$res = $sql->Db_Select('user_data');
	if (isset($_REQUEST['action'])) {
		$action = $_REQUEST['action'];
		
		switch ($action) {
			case 'add_videos' :
				if (isset($_REQUEST['fb_user_id']) && isset($_REQUEST['self']) && isset($_REQUEST['videos']) && isset($_REQUEST['videos_name']) && isset($_REQUEST['videos_times'])) {
					$fb_user_id = $_REQUEST['fb_user_id'];
					$self = $_REQUEST['self'];
					$videos = explode(',', $_REQUEST['videos']);
					$videos_name = explode('|~|', $_REQUEST['videos_name']);
					$videos_times = explode('|~|', $_REQUEST['videos_times']);
					$rid = array();
					foreach ($videos as $k=>$v) {
						if ($v) {
							$rid[] = $sql->db_Insert('user_data (user_id, add_date, video_id, video_name, own_grabbed)', '"'.$fb_user_id.'", '.strtotime($videos_times[$k]).', "'.$v.'", "'.$videos_name[$k].'", '.(($self == $fb_user_id)?1:0));
						}
					}
					// echo implode('/', $rid);
				}
			break;
			
			case 'load_videos':
				if (isset($_REQUEST['fb_user_id']) && isset($_REQUEST['self'])) {
					$out = array();
					$own_grabbed = 1;
					$fb_user_id = $_REQUEST['fb_user_id'];
					$self = $_REQUEST['self'];
					$res = $sql->db_Select('user_data', '*', 'WHERE deleted = 0 AND user_id = "'.$fb_user_id.'" AND video_id NOT IN (SELECT video_id FROM deleted_data WHERE user_id = "'.$self.'") AND own_grabbed = 1 ORDER BY add_date DESC','', FALSE);
					$rows = $sql->Db_Rows();
					if (($rows==0) && ($fb_user_id != $self)) {
						$res = $sql->db_Select('user_data', '*', 'WHERE deleted = 0 AND user_id = "'.$fb_user_id.'" AND video_id NOT IN (SELECT video_id FROM deleted_data WHERE user_id = "'.$self.'") AND own_grabbed = 0 ORDER BY add_date DESC','', FALSE);
						$own_grabbed = 0;
					}
					while ($r = $sql->Db_Fetch()) {
						$out[] = array('name'=>$r['video_name'], 'type'=>'video', 'link'=>'http://youtube.com/watch?v='.$r['video_id'], 'video_id'=>$r['video_id']);
					}
					$res = $sql->db_Select('user_data', 'MAX(add_date) as last_timestamp', 'WHERE user_id = "'.$fb_user_id.'" AND own_grabbed = '.$own_grabbed.' ORDER BY id ASC','', FALSE);
					$r = $sql->Db_Fetch();
					
					header('Content-type: application/json');
					echo json_encode(array('data'=>$out, 'last_timestamp'=>$r['last_timestamp']));
				} else {
					echo 'nok';
				}
			break;
			
			case 'del_video':
				if (isset($_REQUEST['fb_user_id']) && isset($_REQUEST['video_id'])) {
					$fb_user_id = $_REQUEST['fb_user_id'];
					$video_id = $_REQUEST['video_id'];
					// $res = $sql->db_Update('user_data', 'deleted = 1 WHERE video_id = "'.$video_id.'" AND user_id = "'.$fb_user_id.'"', FALSE);
					$res = $sql->db_Insert('deleted_data (user_id, video_id)', '"'.$fb_user_id.'", "'.$video_id.'"', FALSE);
					echo 'ok';
				} else {
					echo 'nok';
				}
			break;

            case 'get_friends':
                if (isset($_REQUEST['fb_user_id'])&& isset($_REQUEST['friends'])) {
                    $fb_user_id = $_REQUEST['fb_user_id'];
                    $res =$sql->db_Select('user_data', 'DISTINCT user_id as `user_id`');
                    $friends = array();
                    while ($r = $sql->db_Fetch()) {
                        $friends[] = $r['user_id'];
                    }
                    header('Content-type: application/json');
                    echo json_encode(array('result'=>'ok', 'visible_friends'=>array_intersect(explode(',', $_REQUEST['friends']), $friends)));
                } else {
                    echo 'nok';
                }
                break;
		}
	}
	
	die();
?>