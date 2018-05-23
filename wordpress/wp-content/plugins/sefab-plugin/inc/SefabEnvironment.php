<?php
/**
 * @package Sefab Plugin
 */
namespace Inc;

class SefabEnvironment
{
    //Plugin Settings
    //Local
    public $pluginUrl;
    public $pluginPath;
    public $sefabRoute = "sefab-api/v1";
    public $isSmsEnabled = false;
    public $isPushNotificationsEnabled = false;
    public $isEmailEnabled = true;
    public $isLoggingEnabled = true;
    public $isFormEmailsEnabled = false;
    public $isReportEmailsEnabled = true;
    public $useLocalSettings = true;

    //Live
    // public $pluginUrl;
    // public $pluginPath;
    // public $sefabRoute = "sefab-api/v1";
    // public $isSmsEnabled = true;
    // public $isPushNotificationsEnabled = true;
    // public $isEmailEnabled = true;
    // public $isLoggingEnabled = true;
    // public $isFormEmailsEnabled = false;
    // public $isReportEmailsEnabled = true;
    // public $useLocalSettings = false;

    //Sms Settings
    public $countryCode = "+63";
    public $smsAccountSid = "AC6dd824ad3a75fcc0684d78f1fbb594d4";
    public $smsAuthToken = "c782667518672eec64804b6e8ae079c6";
    public $smsPhoneNumber = "+46769449951";

    //Push Notification Settings
    public $pushAppId = "619bfade-fc6a-48f2-99e2-40add0ba39e8";
    public $pushKey = "ODE3MWQ2M2UtNWZhNS00MTZkLTk5ZjEtZWMwZDA3OWYxNTli";
    public $pushApiUrl = "https://onesignal.com/api/v1/notifications";

    //Db Tables
    public $postTable = [
        'tableName' => 'sefab_post',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'wp_post_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'title',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'user_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'time_stamp',
                'isPrimary' => false,
                'type' => 'DATETIME',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'time_stamp_updated',
                'isPrimary' => false,
                'type' => 'DATETIME',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'category',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'is_deleted',
                'isPrimary' => false,
                'type' => 'BOOLEAN',
                'isNull' => 'NOT NULL',
                'extra' => 'DEFAULT FALSE',
            ],
        ],
    ];

    public $paragraphTable = [
        'tableName' => 'sefab_paragraph',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'post_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'content',
                'isPrimary' => false,
                'type' => 'TEXT(100000)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'header',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'position',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'is_deleted',
                'isPrimary' => false,
                'type' => 'BOOLEAN',
                'isNull' => 'NOT NULL',
                'extra' => 'DEFAULT FALSE',
            ],
        ],
    ];

    public $formTable = [
        'tableName' => 'sefab_form',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'wp_form_id',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'post_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'title',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'form_description',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'position',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'is_deleted',
                'isPrimary' => false,
                'type' => 'BOOLEAN',
                'isNull' => 'NOT NULL',
                'extra' => 'DEFAULT FALSE',
            ],
        ],
    ];

    public $questionTable = [
        'tableName' => 'sefab_question',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'wp_question_id',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'wp_form_id',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'form_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'form_title',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'form_type',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'is_require',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'form_description',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'is_deleted',
                'isPrimary' => false,
                'type' => 'BOOLEAN',
                'isNull' => 'NOT NULL',
                'extra' => 'DEFAULT FALSE',
            ],
        ],
    ];

    public $optionTable = [
        'tableName' => 'sefab_option',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],

            [
                'name' => 'question_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'option_value',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'is_deleted',
                'isPrimary' => false,
                'type' => 'BOOLEAN',
                'isNull' => 'NOT NULL',
                'extra' => 'DEFAULT FALSE',
            ],
        ],
    ];

    public $answerTable = [
        'tableName' => 'sefab_answer',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'question_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'user_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'post_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'answers',
                'isPrimary' => false,
                'type' => 'TEXT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'timestamp',
                'isPrimary' => false,
                'type' => 'DATETIME',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
        ],
    ];

    public $verificationCodeTable = [
        'tableName' => 'sefab_verification_code',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'user_id',
                'isPrimary' => false,
                'type' => 'int',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'verification_code',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'status',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'timestamp',
                'isPrimary' => false,
                'type' => 'DATETIME',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'is_deleted',
                'isPrimary' => false,
                'type' => 'BOOLEAN',
                'isNull' => 'NOT NULL',
                'extra' => 'DEFAULT FALSE',
            ],
        ],
    ];

    public $postNotificationDetailTable = [
        'tableName' => 'sefab_post_notification_detail',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'post_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'user_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'is_confirmed',
                'isPrimary' => false,
                'type' => 'BOOLEAN',
                'isNull' => 'NOT NULL',
                'extra' => 'DEFAULT FALSE',
            ],
            [
                'name' => 'timestamp',
                'isPrimary' => false,
                'type' => 'DATETIME',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
        ],
    ];

    public $viewTrackerTable = [
        'tableName' => 'sefab_view_tracker',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'post_id',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'user_id',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'timestamp',
                'isPrimary' => false,
                'type' => 'TIMESTAMP',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],

        ],
    ];

    public $emailTable = [
        'tableName' => 'sefab_email',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'user_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'receiver_email_address',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'subject',
                'isPrimary' => false,
                'type' => 'TEXT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'content',
                'isPrimary' => false,
                'type' => 'TEXT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'timestamp',
                'isPrimary' => false,
                'type' => 'DATETIME',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
        ],
    ];

    public $coordinatesTable = [
        'tableName' => 'sefab_coordinates',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'latitude',
                'isPrimary' => false,
                'type' => 'FLOAT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'longitude',
                'isPrimary' => false,
                'type' => 'FLOAT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'timestamp',
                'isPrimary' => false,
                'type' => 'DATETIME',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
        ],
    ];

    public $projectsTable = [
        'tableName' => 'sefab_projects',
        'columns' => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT',
            ],
            [
                'name' => 'coordinates_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'image_id',
                'isPrimary' => false,
                'type' => 'INT',
                'isNull' => 'NULL',
                'extra' => '',
            ],
            [
                'name' => 'name',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'description',
                'isPrimary' => false,
                'type' => 'TEXT',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'timestamp',
                'isPrimary' => false,
                'type' => 'DATETIME',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
            [
                'name' => 'is_deleted',
                'isPrimary' => false,
                'type' => 'BOOLEAN',
                'isNull' => 'NOT NULL',
                'extra' => '',
            ],
        ],
    ];

    public $fileTable = [
        'tableName' => 'sefab_files',
        'columns'   => [
            [
                'name' => 'id',
                'isPrimary' => true,
                'type' => 'INT',
                'isNull' => 'NOT NULL',
                'extra' => 'AUTO_INCREMENT'
            ],
            [
                'name' => 'name',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => ''
            ],
            [
                'name' => 'type',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => ''
            ],
            [
                'name' => 'size',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => ''
            ],
            [
                'name' => 'timestamp',
                'isPrimary' => false,
                'type' => 'DATETIME',
                'isNull' => 'NOT NULL',
                'extra' => ''
            ],
            [
                'name' => 'unique_name',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => ''
            ],
            [
                'name' => 'dir',
                'isPrimary' => false,
                'type' => 'VARCHAR(255)',
                'isNull' => 'NOT NULL',
                'extra' => ''
            ],
            [
                'name' => 'is_deleted',
                'isPrimary' => false,
                'type' => 'BOOLEAN',
                'isNull' => 'NOT NULL',
                'extra' => 'DEFAULT FALSE'
            ]
        ]
    ];
}
