<?php

$data = ['status' => true];

$data['data'] = [
    [
        'label' => 'Call us on Mobile',
        'link' => 'https://tel:'.DEF_SITE_PHONE
    ],
    [
        'label' => 'Chat with Support',
        'link' => DEF_SITE_CHAT
    ],
    [
        'label' => 'Send an Email',
        'link' => 'mailto:'.DEF_SITE_EMAIL
    ],
    [
        'label' => 'Chat us on WhatsApp',
        'link' => 'https://wa.me/'.DEF_SITE_WHATSAPP
    ],
    [
        'label' => 'Twitter',
        'link' => DEF_SITE_TWITTER_LINK
    ],
    [
        'label' => 'Instagram',
        'link' => DEF_SITE_INSTAGRAM_LINK
    ],
    [
        'label' => 'Facebook',
        'link' => DEF_SITE_FACEBOOK_LINK
    ],
    [
        'label' => 'LinkedIn',
        'link' => DEF_SITE_LINKEDIN_LINK
    ]
];

getJsonList($data);