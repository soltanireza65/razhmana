<?php
require_once 'autoload.php';
Poster::updateToExpiredFromCronJob();
Poster::updateToExpiredStatusFromCronJob();
Poster::deleteToExpiredStatusFromCronJob();