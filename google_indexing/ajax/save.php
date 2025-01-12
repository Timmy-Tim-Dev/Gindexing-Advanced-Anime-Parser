<?php

/*
=====================================================
 Copyright (c) 2022 DLEPremium
=====================================================
 This code is protected by copyright
=====================================================
*/

dle_session();

require_once (DLEPlugins::Check(ENGINE_DIR . '/modules/sitelogin.php'));

date_default_timezone_set($config['date_adjust']);
$_TIME = time();

$_POST['user_hash'] = trim($_POST['user_hash']);
if ($_POST['user_hash'] == '' OR $_POST['user_hash'] != $dle_login_hash) {
	die('error');
}

if (!$is_logged && $member_id['user_group'] != 1) {
	die();
}

$action = isset($_POST['action']) ? trim(strip_tags($_POST['action'])) : false;
require_once ENGINE_DIR.'/mrdeath/aaparser/data/config.php';
if ( $action == 'save' ) {

	if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json') ) {
		$mod_settings = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json');
		$mod_settings = json_decode($mod_settings, true);
		if (isset($aaparser_config['settings_gindexing']['account'])){
			$mod_settings['account'] = $aaparser_config['settings_gindexing']['account'];
		} else {
			$mod_settings['account'] = $_POST['acc'];
		}
		file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json', json_encode($mod_settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
	}
	else {
		$mod_settings = [];
		$mod_settings['today_date'] = date('Y-m-d', time());
		if (isset($aaparser_config['settings_gindexing']['account'])){
			$mod_settings['today_limit'][$aaparser_config['settings_gindexing']['account']] = 0;
			$mod_settings['account'] = $aaparser_config['settings_gindexing']['account'];
		} else {
			$mod_settings['today_limit'][$_POST['acc']] = 0;
			$mod_settings['account'] = $_POST['acc'];
		}
		$mod_settings['all'] = 0;
		$mod_settings['updated'] = 0;
		$mod_settings['deleted'] = 0;
		$mod_settings['logs'][] = '';
		$fp = fopen(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json', "w+");
		fwrite($fp, json_encode($mod_settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
		fclose($fp);
	}

    echo json_encode(['success' => 'Ok']);

}
elseif ( $action == 'check' ) {
    $indexing_action = 'get';
    $indexing_url = $_POST['url'];
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/google_indexing/indexing.php'));
    if ( $result_log['urlNotificationMetadata']['latestUpdate']['status'] ) {
        $result_check = [];
        $result_check['status'] = $result_log['urlNotificationMetadata']['latestUpdate']['status'];
        $result_check['link'] = $result_log['latestUpdate']['url'];
        $result_check['type'] = $result_log['latestUpdate']['type'];
        $result_check['date'] = date('d.m.Y h:i', strtotime($result_log['latestUpdate']['notifyTime']));
        echo json_encode(['success' => 'Ok', 'result' => $result_check]);
    }
    else echo json_encode(['error' => 'Ok']);
}
elseif ( $action == 'mass' ) {
    $indexing_type = $_POST['kind'];
    $indexing_url = explode(PHP_EOL, $_POST['urls']);
    include_once (DLEPlugins::Check(ENGINE_DIR . '/mrdeath/aaparser/google_indexing/indexing_multiple.php'));
    echo json_encode(['success' => 'Ok']);
}
elseif ( $action == 'logspage' ) {
    $mod_settings = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json');
    $mod_settings = json_decode($mod_settings, true);
    $mod_settings['logs'] = array_slice($mod_settings['logs'], (20*intval($_POST['page'])), 20);
    
    $logs_list = [];
    foreach ( $mod_settings['logs'] as $log_num => $log_data ) {
        if ( $log_data ) $logs_list[] = '<tr>
                        <td class="hidden-xs text-nowrap text-center">200</td>
                        <td class="hidden-xs text-nowrap text-center">'.date('d.m.Y h:i', strtotime($log_data['notifyTime'])).'</td>
                        <td class="cursor-pointer text-center"><a>'.$log_data['url'].'</a></td>
                        <td class="hidden-xs text-nowrap text-center">'.$log_data['type'].'</td>
                    </tr>';
    }
    $logs_list = implode('', $logs_list);
    
    echo json_encode(['success' => 'Ok', 'result' => $logs_list]);
}
elseif ( $action == 'clear_logs' ) {
    $mod_settings = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json');
    $mod_settings = json_decode($mod_settings, true);
    unset($mod_settings['logs']);
    $mod_settings['logs'][] = '';
    file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json', json_encode($mod_settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
    
    echo json_encode(['success' => 'Ok']);
}

?>
