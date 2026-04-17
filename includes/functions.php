<?php
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function categoryIcon($category) {
    $map = [
        'Food' => 'assets/img/food.svg',
        'Travel' => 'assets/img/travel.svg',
        'Bills' => 'assets/img/bills.svg',
        'Shopping' => 'assets/img/shopping.svg',
        'Salary' => 'assets/img/salary.svg',
    ];

    if (isset($map[$category])) {
        return $map[$category];
    }

    return 'assets/img/custom.svg';
}