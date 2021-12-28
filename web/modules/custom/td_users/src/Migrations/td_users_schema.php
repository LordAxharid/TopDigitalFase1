<?php
/**
 * Implements hook_schema().
 */

function td_users_uninstall() {
  $db_connection = \Drupal::database();
  $db_connection->schema()->dropTable('td_users_countries');
}

function td_users_schema() {
    $schema['td_users_countries'] =  [
        'description' => 'Paises',
        'fields' => [
            'id' => [
            'type' => 'serial',
            'unsigned' => TRUE,
            'not null' => TRUE,
            ],
            'name' => [
                'type' => 'varchar',
                'length' => 100,
                'null' => TRUE,
            ],
            'prefix' => [
                'type' => 'varchar',
                'length' => 4,
                'null' => TRUE,
            ],
            'iso' => [
                'type' => 'varchar',
                'length' => 4,
                'null' => TRUE,
            ],
            'created_at' => [
                'mysql_type' => 'timestamp DEFAULT CURRENT_TIMESTAMP',
                'not null' => TRUE,
            ],
            'updated_at' => [
                'mysql_type' => 'timestamp DEFAULT CURRENT_TIMESTAMP',
                'not null' => TRUE,
            ],
        ],
        'primary key' => ['id'],
    ];

    $schema['td_users_cities'] =  [
        'description' => 'Ciudades',
        'fields' => [
            'id' => [
            'type' => 'serial',
            'unsigned' => TRUE,
            'not null' => TRUE,
            ],
            'cid' => [
                'type' => 'int',
                'size' => 'normal',
                'null' => TRUE,
                'description' => 'Countryid',
            ],
            'name' => [
                'type' => 'varchar',
                'length' => 60,
                'null' => TRUE,
            ],
            'created_at' => [
                'mysql_type' => 'timestamp DEFAULT CURRENT_TIMESTAMP',
                'not null' => TRUE,
            ],
            'updated_at' => [
                'mysql_type' => 'timestamp DEFAULT CURRENT_TIMESTAMP',
                'not null' => TRUE,
            ],
        ],
        'primary key' => ['id'],
    ];
    return $schema;
}
