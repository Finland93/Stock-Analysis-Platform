<?php
/*
 * Server-side proxy for Alpha Vantage requests.
 *
 * Keeps $av_api_key on the server instead of exposing it in client-side
 * JavaScript. The browser calls:  app/api-proxy.php?type=chart|news&symbol=XXX
 */
require_once '../../app/db-config.php';   // provides $av_api_key + helpers

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Whitelist the ticker characters (letters, digits, dot, dash)
$symbol = strtoupper(preg_replace('/[^A-Za-z0-9.\-]/', '', (string) ($_GET['symbol'] ?? '')));
$type   = (string) ($_GET['type'] ?? '');

if ($symbol === '' || strlen($symbol) > 12 || !in_array($type, array('chart', 'news'), true)) {
    http_response_code(400);
    echo json_encode(array('error' => 'Invalid request'));
    exit;
}

if (!isset($av_api_key) || $av_api_key === '' || stripos($av_api_key, 'CHANGE') !== false) {
    error_log('Stock platform api-proxy: $av_api_key is not configured');
    http_response_code(500);
    echo json_encode(array('error' => 'Service unavailable'));
    exit;
}

if ($type === 'chart') {
    $url = 'https://www.alphavantage.co/query?function=TIME_SERIES_DAILY_ADJUSTED&symbol='
        . urlencode($symbol) . '&outputsize=compact&apikey=' . urlencode($av_api_key);
} else {
    $url = 'https://www.alphavantage.co/query?function=NEWS_SENTIMENT&tickers='
        . urlencode($symbol) . '&apikey=' . urlencode($av_api_key);
}

$ctx  = stream_context_create(array('http' => array('timeout' => 15, 'ignore_errors' => true)));
$data = @file_get_contents($url, false, $ctx);

if ($data === false) {
    http_response_code(502);
    echo json_encode(array('error' => 'Upstream request failed'));
    exit;
}

// Pass Alpha Vantage's JSON straight through to the browser
echo $data;
