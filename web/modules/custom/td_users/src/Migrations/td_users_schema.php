<?php
/**
 * Implements hook_schema().
 */

function td_users_uninstall() {
  $db_connection = \Drupal::database();
  $db_connection->schema()->dropTable('td_users_countries');
}

function co_trivia_150_schema() {
    $schema['td_users_countries'] =  [
        'description' => 'Guarda los datos de los usuarios que realicen la trivia',
        'fields' => [
            'id' => [
                'type' => 'serial',
                'not null' => TRUE,
                'description' => 'primary key: Unique register ID',
            ],
            'optional_information' => [
                'type' => 'varchar',
                'length' => 255,
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
