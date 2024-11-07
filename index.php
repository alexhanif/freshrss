<?php
declare(strict_types=1);

header('Location: p/', true, FreshRSS_HttpResponseCode::HTTP_301_MOVED_PERMANENTLY->value);
include('index.html');
