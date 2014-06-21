<?php

$data = [];

for ($i = 1; $i <= 100; $i++) {
    $data[] = [
        'id' => $i,
        'name' => 'My Name ' . $i,
        'email' => 'my.name.' . $i . '@email.com',
        'created_at' => new DateTime(),
        'active' => $i % 2 == 0
    ];
}

return $data;