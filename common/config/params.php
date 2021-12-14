<?php

$gridViewLayout = <<<HTML
    <div class="block-box border-default mb-4">
        <div class="block-box__header mb-2">Сортировка</div>
        <div class="block-box__body">{sorter}</div>
    </div>
    <div class="block-box border-default">
        <div class="block-box__header">Фильтрация</div>
        <div class="block-box__body block-box__body_no-padding p-0 mb-2 position-relative" style="overflow-x: auto;">
            {items}
        </div>
        <div class="block-box__footer clearfix text-center">{pager}{summary}</div>
    </div>
HTML;

return [
    'bsVersion' => '4.x',
    'gridViewLayout' => $gridViewLayout,
    'pagerParams' => [
        'options' => ['class' => 'pagination justify-content-center'],
        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
        'linkContainerOptions' => ['class' => 'page-item'],
        'linkOptions' => ['class' => 'page-link'],
    ],

    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'emailActivation' => true,
];
