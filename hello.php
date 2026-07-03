<?php
file_put_contents(
    __DIR__ . "/hello.txt",
    "Cron executed at " . date("Y-m-d H:i:s") . PHP_EOL,
    FILE_APPEND
);