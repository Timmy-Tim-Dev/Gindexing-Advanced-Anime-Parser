<?php

if ( file_exists(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json') ) {
    $indexing_settings = file_get_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json');
    $indexing_settings = json_decode($indexing_settings, true);
    if ( $indexing_settings['account'] ) {
        
        $indexing_acc = $indexing_settings['account'];
        
        if ( !isset($indexing_settings['today_limit'][$indexing_settings['account']]) ) {
            $indexing_settings['today_limit'][$indexing_settings['account']] = 0;
        }
        if ( date('Y-m-d', time()) != $indexing_settings['today_date'] ) {
            foreach ( $indexing_settings['today_limit'] as $num => $acc_name ) {
                $indexing_settings['today_limit'][$num] = 0;
            }
            $indexing_settings['today_date'] = date('Y-m-d', time());
        }
        if ( $indexing_settings['today_limit'][$indexing_settings['account']] < 200 ) {
            
            $indexing_settings['today_limit'][$indexing_settings['account']]++;

            require_once ENGINE_DIR.'/mrdeath/aaparser/google_indexing/vendor/autoload.php';

            $indexing_result = [
                'result' => 'success'
            ];

            if (!filter_var($indexing_url, FILTER_VALIDATE_URL)) {
                $indexing_result['result'] = 'error';
                $indexing_result['error'] = 'URL не является корректным.';
                echo json_encode($indexing_result);
                exit();
            }

            $client = new Google_Client();

            $client->setAuthConfig(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/accounts/'.$indexing_settings['account']);
            $client->addScope('https://www.googleapis.com/auth/indexing');
            $httpClient = $client->authorize();
            $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
            if ($indexing_action == 'get') {
                $response = $httpClient->get('https://indexing.googleapis.com/v3/urlNotifications/metadata?url=' . urlencode($indexing_url));
            } else {
                $content = json_encode([
                    'url' => $indexing_url,
                    'type' => $indexing_type
                ]);
                $response = $httpClient->post($endpoint, ['body' => $content]);
            }

            $result_log = (string) $response->getBody();
            $result_log = json_decode($result_log, true);
            
            $status_code = $response->getStatusCode();
            
            if ( $indexing_type == 'URL_UPDATED' || $indexing_action == 'get' ) $indexing_settings['updated']++;
            else $indexing_settings['deleted']++;
            $indexing_settings['all']++;
            
            
            if ( $status_code == 200 ) {
                $result_log['urlNotificationMetadata']['latestUpdate']['status'] = 200;
                if ( $indexing_action != 'get' ) array_unshift($indexing_settings['logs'], $result_log['urlNotificationMetadata']['latestUpdate']);
            }
            elseif ($indexing_action != 'get') {
                $result_indexing = [];
                $result_indexing['notifyTime'] = date('d.m.Y h:i', time());
                $result_indexing['type'] = $indexing_type;
                $result_indexing['url'] = $indexing_url;
                $result_indexing['status'] = $status_code;
                array_unshift($indexing_settings['logs'], $result_indexing);
            }
            
            file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json', json_encode($indexing_settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));

        }
    }
}