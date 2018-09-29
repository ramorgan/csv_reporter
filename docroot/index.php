<?php
include 'csv_report.php';

$app = new csv_report();
$app->create_reports();

echo "<span>Report created.</span>";

