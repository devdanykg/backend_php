<?php

// Expire time in milliseconds
return [
    'Mongo' => function($container) {
        return (new \MongoDB\Client)->open_api;
    }
];