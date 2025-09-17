<?php

return [
    'public_key' => getenv('STRIPE_PUBLIC_KEY') ?: '',
    'secret_key' => getenv('STRIPE_SECRET_KEY') ?: '',
    'webhook_secret' => getenv('STRIPE_WEBHOOK_SECRET') ?: '',
    'currency' => 'eur',
    'payment_methods' => ['card'],
    'payment_description' => 'Don à Msss',
    'success_url' => '/donation/success',
    'cancel_url' => '/donation/cancel',
];
