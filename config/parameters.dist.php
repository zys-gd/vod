<?php

return [
    'DATABASE_HOST'     => '192.168.101.6',
    'DATABASE_PORT'     => '3306',
    'DATABASE_NAME'     => 'vod_main',
    'DATABASE_USER'     => 'root',
    'DATABASE_PASSWORD' => '123456',

    'SECRET' => 'ThisTokenIsNotSoSecretChangeIt',

    'CLOUDINARY_API_KEY'    => '187818276162186',
    'CLOUDINARY_API_SECRET' => 'sHmSwu7rTZqiAmfqFMM-XLl-r0k',
    'CLOUDINARY_CLOUD_NAME' => 'origindata',

    'HOST' => 'vod.local',

    'AWS_S3_KEY'     => 'AKIAJ5JFFS64EVJ423BQ',
    'AWS_S3_SECRET'  => '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',
    'AWS_S3_VERSION' => 'latest',
    'AWS_S3_REGION'  => 'eu-central-1',

    'IMAGES_BASE_URL'    => 'http://cdn.gameimages.store.playwing.com',
    'BILLING_API_HOST'   => 'http://billing.100sport.tv/api',
    'TOKEN_SESSION_NAME' => '',
    'APP_VERSION_HASH'   => 'default',

    'REDIS_HOST' => '192.168.101.3',
    'REDIS_PORT' => '6379',

    'PIWIK_HOST'                 => 'piwik.playwing.com',
    'PIWIK_ID_SITE'              => true,
    'PIWIK_TOKEN_AUTH'           => '6361344ea40168ef1092c05c19b07aec',
    'PIWIK_HTTPS'                => false,
    'PIWIK_ENABLE_JS_TRACKER'    => false,
    'PIWIK_ENABLE_LINK_TRACKING' => true,

    'RABBIT_MQ_HOST'             => 'rabbitmq.playwing.com',
    'RABBIT_MQ_PORT'             => 5672,
    'RABBIT_MQ_USER'             => 'admin',
    'RABBIT_MQ_PASSWORD'         => 'Pass1234',
    'RABBIT_MQ_VHOST'            => '/',

    'CAMPAIGN_SESSION_NAME'      => 'campaignData',
    'DEFAULT_REDIRECT_URL'       => 'https://www.google.com/',

    'MAILER_TRANSPORT'           => 'smtp',
    'MAILER_HOST'                => 'email-smtp.eu-west-1.amazonaws.com',
    'MAILER_USER'                => 'AKIAJSGBBIGYKCTG2D6A',
    'MAILER_PASSWORD'            => 'BCjVv8MEmA4IKrXF7Ps6T6VAbbdR5taVt3rNptvG365D',
    'MAILER_PORT'                => 587,
    'MAILER_ENCRYPTION'          => 'tls',

    'CAP_NOTIFICATION_MAIL_TO'     => 'vod-cap-alert@origin-data.com',
    'CAP_NOTIFICATION_MAIL_FROM'   => 'odintegrations@origin-data.com',
    'CONTACT_US_MAIL_TO'           => 'support@origin-data.com',
    'CONTACT_US_MAIL_FROM'         => 'support.form@origin-data.com',

    /*'MAILER_URL'          => 'MAILER_URL',*/
    'UPLOADS_PATH'        => 'uploads',
    'UPLOADS_BUILDS_PATH' => 'uploads/builds',

    'DRM_AUTHORIZE_KEY' =>  'NEbozf9AeS',
    'DRM_API_URL' =>  'http://drm-api-dev-env.utbtaavpnd.eu-west-1.elasticbeanstalk.com/web/api/',
    'S3_ROOT_URL' =>  'https://s3.eu-central-1.amazonaws.com/playwing-appstore',

    'REPORTING_STATS_API_HOST' => 'http://reporting.100sport.tv/a/api'
];