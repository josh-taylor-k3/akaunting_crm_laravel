<?php

return [

    'company' => [
        'name'              => 'Emri',
        'email'             => 'Email',
        'phone'             => 'Telefoni',
        'address'           => 'Adresa',
        'logo'              => 'Logoja',
    ],
    'localisation' => [
        'tab'               => 'Lokalizimi',
        'date' => [
            'format'        => 'Formati i Datës',
            'separator'     => 'Ndarës i Datës',
            'dash'          => 'Vizë (-)',
            'dot'           => 'Pikë (.)',
            'comma'         => 'Presje (,)',
            'slash'         => 'Prerje (/)',
            'space'         => 'Hapësirë ( )',
        ],
        'timezone'          => 'Zona Kohore',
        'percent' => [
            'title'         => 'Percent (%) Position',
            'before'        => 'Before Number',
            'after'         => 'After Number',
        ],
    ],
    'invoice' => [
        'tab'               => 'Faturë',
        'prefix'            => 'Parashtesa e numrit',
        'digit'             => 'Gjatësia a numrit',
        'next'              => 'Numri tjetër',
        'logo'              => 'Logoja',
    ],
    'default' => [
        'tab'               => 'Parazgjedhjet',
        'account'           => 'Llogaria e Parazgjedhur',
        'currency'          => 'Valuta e Parazgjedhur',
        'tax'               => 'Norma Tatimore e Parazgjedhur',
        'payment'           => 'Metoda e Pagesës e Parazgjedhur',
        'language'          => 'Gjuha e Parazgjedhur',
    ],
    'email' => [
        'protocol'          => 'Protokolli',
        'php'               => 'PHP Email',
        'smtp' => [
            'name'          => 'SMTP',
            'host'          => 'SMTP Host',
            'port'          => 'SMTP Port',
            'username'      => 'Emri i Përdorimit SMTP',
            'password'      => 'Fjalëkalimi i SMTP',
            'encryption'    => 'Siguria SMTP',
            'none'          => 'Asnjë',
        ],
        'sendmail'          => 'Sendmail',
        'sendmail_path'     => 'Sendmail Path',
        'log'               => 'Logo Emailet',
    ],
    'scheduling' => [
        'tab'               => 'Planifikimi',
        'send_invoice'      => 'Dërgo Faturën Rikujtimor',
        'invoice_days'      => 'Dërgo Pas Ditëve të Duhura',
        'send_bill'         => 'Dërgo Faturën Rikujtimor',
        'bill_days'         => 'Dërgo Para Ditëve të Duhura',
        'cron_command'      => 'Komanda Cron',
        'schedule_time'     => 'Ora për të Kandiduar',
    ],
    'appearance' => [
        'tab'               => 'Pamja',
        'theme'             => 'Tema',
        'light'             => 'E Çelur',
        'dark'              => 'E Errët',
        'list_limit'        => 'Regjistrimet Në Faqe',
        'use_gravatar'      => 'Përdorni Gravatar',
    ],
    'system' => [
        'tab'               => 'Sistemi',
        'session' => [
            'lifetime'      => 'Jetëgjatësia e Sesionit (Minuta)',
            'handler'       => 'Menaxheri i Sesionit',
            'file'          => 'Skedar',
            'database'      => 'Database',
        ],
        'file_size'         => 'Madhësia e Skedarit Maksimal (MB)',
        'file_types'        => 'Llojet Skedare të Lejuara',
    ],

];
