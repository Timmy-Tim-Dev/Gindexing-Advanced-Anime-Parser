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
            

            require_once ENGINE_DIR.'/mrdeath/aaparser/google_indexing/vendor/autoload.php';

            $client = new Google_Client();
            

            $client->setAuthConfig(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/accounts/'.$indexing_settings['account']);
            $client->addScope('https://www.googleapis.com/auth/indexing');
            $client->setUseBatch(true);
            
            $service = new Google_Service_Indexing($client);
            $batch = $service->createBatch();
            
            foreach ( $indexing_url as $url ) {
                if ( !$url ) continue;
                if (!filter_var($url, FILTER_VALIDATE_URL)) continue;
                
                $postBody = new Google_Service_Indexing_UrlNotification();
                $postBody->setType($indexing_type);
                $postBody->setUrl($url);
                $batch ->add($service->urlNotifications->publish($postBody));
                
                if ( $indexing_type == 'URL_UPDATED' ) $indexing_settings['updated']++;
                else $indexing_settings['deleted']++;
                $indexing_settings['all']++;
                $indexing_settings['today_limit'][$indexing_settings['account']]++;
            }
            
            $results = $batch->execute();
            
            if ( isset( $result_log['urlNotificationMetadata']['latestUpdate'] ) ) {
                foreach ( $results as $result_log ) {
                    $result_log['urlNotificationMetadata']['latestUpdate']['status'] = 200;
                    array_unshift($indexing_settings['logs'], $result_log['urlNotificationMetadata']['latestUpdate']);
                }
            }
            else {
                foreach ( $indexing_url as $url ) {
                    if ( !$url ) continue;
                    $result_indexing = [];
                    $result_indexing['notifyTime'] = date('d.m.Y h:i', time());
                    $result_indexing['type'] = $indexing_type;
                    $result_indexing['url'] = $url;
                    $result_indexing['status'] = 'error';
                    array_unshift($indexing_settings['logs'], $result_indexing);
                    unset($result_indexing);
                }
            }
            
            file_put_contents(ENGINE_DIR.'/mrdeath/aaparser/google_indexing/data/indexing.json', json_encode($indexing_settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ));
            
        }
    }
}