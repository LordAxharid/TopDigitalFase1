<?php
//phpinfo();die;
 $configuration = [
    [
        'domain' => 'top-digital.local',
        'driver' => 'mysql',
        'database' => 'top_digital',
        'username' => 'root',
        'password' => '',
        'host' => 'localhost',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
     ],
];
/**
 * File Config settings Local 
 * Dont remove any code Line
 * Developer By David Martinez
 */
foreach ($configuration as $key => $value) {
    $confDB = $value;
    if($value['domain'] == $_SERVER['HTTP_HOST']) break;
}
$databases['default']['default'] = $confDB;

//ROUTES FROM FILES
$settings['file_public_path'] = "sites/default/files/".$confDB['files']."/files";
//SHOW ALL ERRORS IN BROWSER
$config['system.logging']['error_level'] = 'verbose';
//DISABLED CACHE
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['cache']['bins']['render'] = 'cache.backend.null';            //Disable the render cache.
$settings['cache']['bins']['page'] = 'cache.backend.null';              //Disable Internal Page Cache.
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';//Disable Dynamic Page Cache.
$settings['rebuild_access'] = TRUE;
                                     //Enable access to rebuild.php.