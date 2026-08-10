#!/usr/bin/env python3

import asyncio, os, re, urllib.request, urllib.parse
from bs4 import BeautifulSoup
from playwright.async_api import async_playwright

URL = "https://lanzarote-vulkane.de"
DOMAIN = "lanzarote-vulkane.de"
DOMAIN_WWW = "www.lanzarote-vulkane.de"
OUT = "pages/wordpress"
LOCAL_DOMAINS = [DOMAIN, DOMAIN_WWW]

EXTERNAL_KEEP = [
    "youtube.com",
    "www.youtube.com",
    "youtu.be",
    "vimeo.com",
    "player.vimeo.com",
    "maps.googleapis.com",
    "maps.google.com",
    "platform.twitter.com",
    "twitter.com",
    "instagram.com",
    "www.instagram.com",
    "facebook.com",
    "www.facebook.com",
    "google.com/maps",
]

os.makedirs(OUT, exist_ok=True)

resources = {}
stats = {"html": 0, "css": 0, "js": 0, "img": 0, "font": 0, "other": 0}
saved_html_files = set()


def save_resource(url: str, body: bytes):
    if url in resources:
        return
    if any(d in url for d in EXTERNAL_KEEP):
        return
    resources[url] = body

    parsed = urllib.parse.urlparse(url)
    host = parsed.netloc
    path = parsed.path or "/"

    if path.endswith("/"):
        path += "index.html"

    if host in LOCAL_DOMAINS:
        local_path = path.lstrip("/")
    else:
        local_path = f"{host}{path}"

    local = os.path.join(OUT, local_path)
    os.makedirs(os.path.dirname(local), exist_ok=True)
    with open(local, "wb") as f:
        f.write(body)

    ext = os.path.splitext(path)[1].lower()
    if ext in (".html", ".htm"):
        stats["html"] += 1
    elif ext == ".css":
        stats["css"] += 1
    elif ext == ".js":
        stats["js"] += 1
    elif ext in (".jpg", ".jpeg", ".png", ".gif", ".webp", ".svg", ".ico", ".avif"):
        stats["img"] += 1
    elif ext in (".woff", ".woff2", ".ttf", ".otf", ".eot"):
        stats["font"] += 1
    else:
        stats["other"] += 1


def local_path_for(url: str) -> str:
    parsed = urllib.parse.urlparse(url)
    host = parsed.netloc
    path = parsed.path or "/"
    if path.endswith("/"):
        path += "index.html"
    if host in LOCAL_DOMAINS:
        return path.lstrip("/")
    return f"{host}{path}"


def save_page_html(html: str, page_url: str):
    parsed = urllib.parse.urlparse(page_url)
    path = parsed.path or "/"
    if path.endswith("/"):
        path += "index.html"
    local = os.path.join(OUT, path.lstrip("/"))
    os.makedirs(os.path.dirname(local), exist_ok=True)
    with open(local, "w", encoding="utf-8") as f:
        f.write(html)
    saved_html_files.add(local)


def rewrite_all_html():
    print("\nRewriting URLs in all HTML files ...")
    total_rewrites = 0
    rewritten_files = 0

    html_files = []
    for root, dirs, files in os.walk(OUT):
        for f in files:
            if f.endswith(".html") or f.endswith(".htm"):
                html_files.append(os.path.join(root, f))
    html_files.sort()

    for html_path in html_files:
        with open(html_path, "r", encoding="utf-8", errors="replace") as f:
            html = f.read()
        original = html

        # Pass 1: brute-force string replace on raw HTML (catches everything)
        for domain in LOCAL_DOMAINS:
            html = html.replace(f"https://{domain}", "")
            html = html.replace(f"http://{domain}", "")
            html = html.replace(f"//{domain}", "")
            html = html.replace(f"https%3A%2F%2F{domain}%2F", "%2F")
            html = html.replace(f"http%3A%2F%2F{domain}%2F", "%2F")
            html = html.replace(f"https%3A%2F%2F{domain}", "")
            html = html.replace(f"http%3A%2F%2F{domain}", "")
            html = html.replace(f"https:%252F%252F{domain}%252F", "%252F")
            html = html.replace(f"http:%252F%252F{domain}%252F", "%252F")
            html = html.replace(f"https:%252F%252F{domain}", "")
            html = html.replace(f"http:%252F%252F{domain}", "")
            html = html.replace(f"https:\\/\\/{domain}", "")
            html = html.replace(f"http:\\/\\/{domain}", "")

        soup = BeautifulSoup(html, "html.parser")

        def _is_external(url):
            return any(d in url for d in EXTERNAL_KEEP)

        rewrite_count = 0
        for tag in soup.find_all(True):
            for attr in list(tag.attrs):
                val = tag[attr]
                values = val if isinstance(val, list) else [val]
                new_values = []
                changed = False
                for v in values:
                    if not isinstance(v, str):
                        new_values.append(v)
                        continue
                    if v.startswith("file://"):
                        new_values.append("")
                        changed = True
                    elif v.startswith(("http://", "https://", "//")):
                        normalized = v
                        if normalized.startswith("//"):
                            normalized = "https:" + normalized
                        if _is_external(normalized):
                            new_values.append(v)
                        elif any(d in normalized for d in LOCAL_DOMAINS):
                            parsed = urllib.parse.urlparse(normalized)
                            relative = parsed.path or "/"
                            if parsed.query:
                                relative += "?" + parsed.query
                            if parsed.fragment:
                                relative += "#" + parsed.fragment
                            new_values.append(relative)
                            changed = True
                        elif normalized in resources:
                            new_values.append("/" + local_path_for(normalized))
                            changed = True
                        else:
                            new_values.append(v)
                    else:
                        new_values.append(v)
                if changed:
                    if isinstance(val, list):
                        tag[attr] = new_values
                    else:
                        tag[attr] = new_values[0]
                    rewrite_count += 1

        html = str(soup)
        total_rewrites += rewrite_count

        html = re.sub(r'file://[^\s"\'<>&]+', "", html)

        # Replace CF7 AJAX scripts with static mailto handler
        cf7_before = r'<script id="contact-form-7-js-before">.*?</script>\s*<script id="contact-form-7-js" src="/wp-content/plugins/contact-form-7/includes/js/index\.js\?ver=[^"]*"></script>\s*<script id="contact-form-7-js-after">\n'
        cf7_after = r"\n//# sourceURL=contact-form-7-js-after\n</script>"
        cf7_match = re.search(cf7_before, html, re.DOTALL)
        cf7_end_match = None
        cf7_start = 0
        cf7_end = 0
        if cf7_match:
            cf7_start = cf7_match.start()
            cf7_end_match = re.search(cf7_after, html[cf7_start:], re.DOTALL)
        if cf7_match and cf7_end_match:
            cf7_end = cf7_start + cf7_end_match.end()
            cf7_replacement = (
                '<script id="contact-form-7-js-before">\n</script>\n'
                '<script id="contact-form-7-js">\n'
                "(function() {\n"
                "  function handleSubmit(e) {\n"
                "    var form = e.target;\n"
                "    if (!form.checkValidity()) return;\n"
                "    e.preventDefault();\n"
                "    var data = {};\n"
                "    var elements = form.elements;\n"
                "    for (var i = 0; i < elements.length; i++) {\n"
                "      var el = elements[i];\n"
                '      if (el.name && !el.name.startsWith("_")) {\n'
                "        data[el.name] = el.value;\n"
                "      }\n"
                "    }\n"
                '    var body = "";\n'
                "    for (var key in data) {\n"
                "      if (data.hasOwnProperty(key)) {\n"
                '        body += key + ": " + data[key] + "\\n";\n'
                "      }\n"
                "    }\n"
                '    var mailto = form.getAttribute("data-mailto") || "office@127.0.0.1";\n'
                '    var subject = form.getAttribute("data-mailto-subject") || "Contact Form Submission";\n'
                '    window.location.href = "mailto:" + mailto + "?subject=" + encodeURIComponent(subject) + "&body=" + encodeURIComponent(body);\n'
                "  }\n"
                '  document.addEventListener("DOMContentLoaded", function() {\n'
                '    var forms = document.querySelectorAll(".wpcf7-form");\n'
                "    for (var i = 0; i < forms.length; i++) {\n"
                '      forms[i].addEventListener("submit", handleSubmit);\n'
                "    }\n"
                "  });\n"
                "})();\n"
                "</script>\n"
                '<script id="contact-form-7-js-after">\n</script>'
            )
            html = html[:cf7_start] + cf7_replacement + html[cf7_end:]
            print("    Replaced CF7 AJAX with static mailto handler")
        elif cf7_match:
            print("    Skipped CF7 replacement (end marker not found)")

        if html != original:
            with open(html_path, "w", encoding="utf-8") as f:
                f.write(html)

    print(f"  Rewrote {total_rewrites} URL references across {len(html_files)} files")


def download_missing_files():
    print("\nDownloading missing files ...")
    missing_downloaded = 0
    missing_paths = set()

    missing_re = re.compile(
        r"(?:\.\./|/)(?:wp-content|wp-includes|uploads|fonts)/[^\s\"'<>]+"
    )

    all_html = []
    for root, dirs, files in os.walk(OUT):
        for f in files:
            if f.endswith(".html") or f.endswith(".htm"):
                all_html.append(os.path.join(root, f))

    for html_path in all_html:
        with open(html_path, "r", encoding="utf-8", errors="replace") as f:
            html = f.read()
        soup = BeautifulSoup(html, "html.parser")

        for tag in soup.find_all(True):
            for attr in list(tag.attrs):
                val = tag[attr]
                values = val if isinstance(val, list) else [val]
                for v in values:
                    if not isinstance(v, str):
                        continue
                    for m in missing_re.finditer(v):
                        p = urllib.parse.unquote(m.group(0))
                        p = re.sub(r"^\.\.+/", "/", p)
                        missing_paths.add(p)

        for m in re.finditer(
            r"(?:\.\./|/)(?:wp-content|wp-includes|uploads|fonts)/[-.%+\w\d/]+",
            html,
        ):
            p = urllib.parse.unquote(m.group(0))
            p = re.sub(r"^\.\.+/", "/", p)
            missing_paths.add(p)

    missing_paths = sorted(missing_paths)
    print(f"  Found {len(missing_paths)} referenced paths")

    for path in missing_paths:
        local = os.path.join(OUT, path.lstrip("/"))
        if os.path.exists(local) and os.path.getsize(local) > 0:
            continue
        url = f"http://{DOMAIN}{path}"
        try:
            req = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0"})
            with urllib.request.urlopen(req, timeout=30) as resp:
                body = resp.read()
            if body:
                os.makedirs(os.path.dirname(local), exist_ok=True)
                with open(local, "wb") as f:
                    f.write(body)
                missing_downloaded += 1
                print(f"  Downloaded: {path} ({len(body) // 1024}KB)")
        except Exception as e:
            print(f"  FAILED: {path} – {e}")

    print(f"  Downloaded {missing_downloaded} missing files")


def rewrite_all_assets():
    print("\nRewriting domain URLs in CSS/JS assets ...")
    total = 0
    for root, dirs, files in os.walk(OUT):
        for f in files:
            if not f.endswith((".css", ".js")):
                continue
            path = os.path.join(root, f)
            try:
                with open(path, "r", encoding="utf-8", errors="replace") as fh:
                    content = fh.read()
            except:
                continue
            original = content
            for domain in LOCAL_DOMAINS:
                content = content.replace(f"https://{domain}", "")
                content = content.replace(f"http://{domain}", "")
                content = content.replace(f"//{domain}", "")
                content = content.replace(f"https%3A%2F%2F{domain}%2F", "%2F")
                content = content.replace(f"http%3A%2F%2F{domain}%2F", "%2F")
                content = content.replace(f"https%3A%2F%2F{domain}", "")
                content = content.replace(f"http%3A%2F%2F{domain}", "")
                content = content.replace(f"https:%252F%252F{domain}%252F", "%252F")
                content = content.replace(f"http:%252F%252F{domain}%252F", "%252F")
                content = content.replace(f"https:%252F%252F{domain}", "")
                content = content.replace(f"http:%252F%252F{domain}", "")
                content = re.sub(
                    re.escape(f"https:\\/\\/{domain}"),
                    "",
                    content,
                )
                content = re.sub(
                    re.escape(f"http:\\/\\/{domain}"),
                    "",
                    content,
                )
            if content != original:
                with open(path, "w", encoding="utf-8") as fh:
                    fh.write(content)
                total += 1
    print(f"  Rewrote {total} CSS/JS files")


async def main():
    async with async_playwright() as p:
        try:
            browser = await p.chromium.launch(headless=True)
        except Exception as e:
            print(f"Failed to launch Chromium: {e}")
            print("Install the Playwright browser first:")
            print("  poetry run playwright install chromium")
            raise
        ctx = await browser.new_context(ignore_https_errors=True)
        page = await ctx.new_page()

        async def capture(response):
            u = response.url
            if u in resources or not u.startswith("http"):
                return
            try:
                body = await response.body()
                save_resource(u, body)
            except Exception:
                pass

        page.on("response", lambda r: asyncio.create_task(capture(r)))

        all_urls = [URL]

        print("Loading homepage to discover sub-pages ...")
        await page.goto(URL, wait_until="networkidle", timeout=60000)

        print("Scrolling page ...")
        await page.evaluate("""
            async () => {
                const delay = ms => new Promise(r => setTimeout(r, ms));
                const step = window.innerHeight;
                for (let y = 0; y < document.body.scrollHeight; y += step) {
                    window.scrollTo(0, y);
                    await delay(400);
                }
                window.scrollTo(0, 0);
            }
        """)
        await page.wait_for_timeout(3000)

        print("Clicking gallery elements ...")
        try:
            gallery_els = await page.query_selector_all(
                "a[rel^=lightbox], a[data-elementor-lightbox], "
                ".elementor-gallery-item, .gallery-icon a, "
                "[data-gallery] a, .wp-block-gallery a"
            )
            for el in gallery_els[:30]:
                try:
                    await el.click()
                    await page.wait_for_timeout(1000)
                    await page.keyboard.press("Escape")
                    await page.wait_for_timeout(500)
                except:
                    pass
        except:
            pass

        await page.wait_for_timeout(5000)

        html = await page.content()
        save_page_html(html, URL)

        base_url = URL.rstrip("/")
        sub_links = await page.eval_on_selector_all(
            "a[href]",
            f"els => els.map(el => el.href).filter(h => "
            f"  h.startsWith('{base_url}/') && "
            f"  !h.includes('#') && !h.match(/\\.(jpg|jpeg|png|gif|webp|css|js|svg|ico|pdf|zip)$/i) && "
            f"  new URL(h).pathname !== '/' && "
            f"  new URL(h).pathname.split('/').filter(Boolean).length <= 2 "
            f")",
        )
        all_urls.extend(sorted(set(sub_links)))
        print(f"  Will visit {len(all_urls)} pages")

        for i, page_url in enumerate(all_urls[1:], 1):
            print(f"\n[{i}/{len(all_urls) - 1}] {page_url}")
            try:
                await page.goto(page_url, wait_until="networkidle", timeout=60000)
                await page.evaluate("""
                    async () => {
                        const delay = ms => new Promise(r => setTimeout(r, ms));
                        const step = window.innerHeight;
                        for (let y = 0; y < document.body.scrollHeight; y += step) {
                            window.scrollTo(0, y);
                            await delay(400);
                        }
                        window.scrollTo(0, 0);
                    }
                """)
                await page.wait_for_timeout(2000)
                try:
                    gallery_els = await page.query_selector_all(
                        "a[rel^=lightbox], a[data-elementor-lightbox], "
                        ".elementor-gallery-item, .gallery-icon a, "
                        "[data-gallery] a, .wp-block-gallery a"
                    )
                    for el in gallery_els[:30]:
                        try:
                            await el.click()
                            await page.wait_for_timeout(1000)
                            await page.keyboard.press("Escape")
                            await page.wait_for_timeout(500)
                        except:
                            pass
                except:
                    pass
                await page.wait_for_timeout(3000)
                html = await page.content()
                save_page_html(html, page_url)
            except Exception as e:
                print(f"    FAILED: {e}")

        print(f"\nCaptured resources:")
        for kind, count in stats.items():
            print(f"  {kind}: {count}")
        print(f"  total: {len(resources)}")

        await browser.close()

    rewrite_all_html()
    download_missing_files()
    rewrite_all_assets()

    print(f"\nDone. Open {OUT}/index.html locally.")
    print(f"  python -m http.server 8000 -d {OUT}")
    print(f"  → http://localhost:8000/")


asyncio.run(main())
