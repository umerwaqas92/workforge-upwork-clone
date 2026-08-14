#!/usr/bin/env bash
# =============================================================================
# WorkForge — one-shot deploy (adapted from MZK POS)
#   1) Build frontend (Vite)
#   2) Optional: run Laravel migrations (local and/or remote)
#   3) Upload code + frontend via FTP
#   4) Optional: remote install.php migrate + smoke test
#
# Usage:
#   ./scripts/deploy.sh
#   ./scripts/deploy.sh --skip-build
#   ./scripts/deploy.sh --local-db
#   ./scripts/deploy.sh --remote-install
#   ./scripts/deploy.sh --ftp-only
#   ./scripts/deploy.sh --help
# =============================================================================
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="${DEPLOY_ENV:-$ROOT/scripts/deploy.env}"
EXAMPLE="$ROOT/scripts/deploy.env.example"

SKIP_BUILD=0
FORCE_LOCAL_DB=0
FORCE_REMOTE_INSTALL=0
FTP_ONLY=0
SKIP_FTP=0

die() { echo "ERROR: $*" >&2; exit 1; }
info() { echo "==> $*"; }
ok() { echo "    ✓ $*"; }

usage() {
  cat <<EOF
WorkForge deploy script

Usage: $(basename "$0") [options]

Options:
  --skip-build         Don't run npm build
  --local-db           Apply Laravel migrations to LOCAL MySQL
  --remote-install     After FTP, hit SITE_URL/install.php to migrate remote DB
  --remote-db          Try mysql client against REMOTE_* (often blocked by InfinityFree)
  --ftp-only           Only FTP upload (no build, no DB)
  --skip-ftp           Build/DB only, no upload
  --env FILE           Env file (default: scripts/deploy.env)
  -h, --help           Show help

Config: copy scripts/deploy.env.example → scripts/deploy.env
EOF
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --skip-build) SKIP_BUILD=1; shift ;;
    --local-db) FORCE_LOCAL_DB=1; shift ;;
    --remote-install) FORCE_REMOTE_INSTALL=1; shift ;;
    --remote-db) export RUN_REMOTE_DB=1; shift ;;
    --ftp-only) FTP_ONLY=1; SKIP_BUILD=1; shift ;;
    --skip-ftp) SKIP_FTP=1; shift ;;
    --env) ENV_FILE="$2"; shift 2 ;;
    -h|--help) usage; exit 0 ;;
    *) die "Unknown option: $1 (try --help)" ;;
  esac
done

# --- Load env ---
if [[ ! -f "$ENV_FILE" ]]; then
  if [[ -f "$EXAMPLE" ]]; then
    die "Missing $ENV_FILE — copy from deploy.env.example and fill credentials."
  fi
  die "Missing env file: $ENV_FILE"
fi
# shellcheck disable=SC1090
set -a
source "$ENV_FILE"
set +a

FTP_HOST="${FTP_HOST:-ftpupload.net}"
FTP_USER="${FTP_USER:-}"
FTP_PASS="${FTP_PASS:-}"
FTP_REMOTE_ROOT="${FTP_REMOTE_ROOT:-/htdocs}"
BUILD_FRONTEND="${BUILD_FRONTEND:-1}"
SMOKE_TEST="${SMOKE_TEST:-1}"
KEEP_REMOTE_ENV="${KEEP_REMOTE_ENV:-1}"
RUN_LOCAL_DB="${RUN_LOCAL_DB:-0}"
RUN_REMOTE_DB="${RUN_REMOTE_DB:-0}"
RUN_REMOTE_INSTALL="${RUN_REMOTE_INSTALL:-0}"
SITE_URL="${SITE_URL:-http://workforgemarketplace.gt.tc}"
INSTALL_KEY="${INSTALL_KEY:-workforge2026}"
COMPOSER_INSTALL="${COMPOSER_INSTALL:-1}"
ARTISAN_MIGRATE="${ARTISAN_MIGRATE:-1}"

[[ "$FORCE_LOCAL_DB" == "1" ]] && RUN_LOCAL_DB=1
[[ "$FORCE_REMOTE_INSTALL" == "1" ]] && RUN_REMOTE_INSTALL=1
[[ "$FTP_ONLY" == "1" ]] && { RUN_LOCAL_DB=0; RUN_REMOTE_DB=0; RUN_REMOTE_INSTALL=0; BUILD_FRONTEND=0; }

need_cmd() { command -v "$1" >/dev/null 2>&1 || die "Required command not found: $1"; }

# =============================================================================
# 1) Frontend build (Vite)
# =============================================================================
build_frontend() {
  if [[ "$SKIP_BUILD" == "1" || "$BUILD_FRONTEND" != "1" ]]; then
    info "Skipping frontend build"
    return
  fi
  need_cmd npm
  info "Building frontend with Vite..."
  (
    cd "$ROOT"
    npm run build
  )
  [[ -f "$ROOT/public/build/manifest.json" ]] || [[ -f "$ROOT/public/build/app.js" ]] || die "public/build missing after build"
  ok "Frontend built → public/build"
}

# =============================================================================
# 2) Laravel migrations (local)
# =============================================================================
run_laravel_migrate() {
  local host="$1" port="$2" user="$3" pass="$4" name="$5" label="$6"
  need_cmd php
  info "Laravel migrate ($label): applying migrations to ${name}@${host}:${port}"

  # Create .env for local testing if needed
  local env_file="$ROOT/.env.migrate"
  cat > "$env_file" <<EOF
DB_CONNECTION=mysql
DB_HOST=$host
DB_PORT=$port
DB_DATABASE=$name
DB_USERNAME=$user
DB_PASSWORD=$pass
EOF

  # Run artisan migrate
  (
    cd "$ROOT"
    DB_HOST="$host" DB_PORT="$port" DB_DATABASE="$name" DB_USERNAME="$user" DB_PASSWORD="$pass" \
    php artisan migrate --force 2>&1
  )

  rm -f "$env_file"
  ok "Laravel migrations applied ($label)"
}

local_db() {
  if [[ "$RUN_LOCAL_DB" != "1" ]]; then
    info "Skipping local DB (set RUN_LOCAL_DB=1 or pass --local-db)"
    return
  fi
  run_laravel_migrate \
    "${LOCAL_DB_HOST:-127.0.0.1}" \
    "${LOCAL_DB_PORT:-3306}" \
    "${LOCAL_DB_USER:-root}" \
    "${LOCAL_DB_PASS:-}" \
    "${LOCAL_DB_NAME:-workforge}" \
    "local"
}

remote_db_cli() {
  if [[ "$RUN_REMOTE_DB" != "1" ]]; then
    return
  fi
  info "Attempting remote MySQL CLI (often blocked off-host on InfinityFree)..."
  if run_laravel_migrate \
    "${REMOTE_DB_HOST}" \
    "${REMOTE_DB_PORT:-3306}" \
    "${REMOTE_DB_USER}" \
    "${REMOTE_DB_PASS}" \
    "${REMOTE_DB_NAME}" \
    "remote"; then
    ok "Remote MySQL CLI migrate OK"
  else
    echo "    ! Remote MySQL failed (expected on free hosts). Use --remote-install instead." >&2
  fi
}

# =============================================================================
# 3) FTP upload (Python for reliability / passive mode / caching)
# =============================================================================
ftp_upload() {
  if [[ "$SKIP_FTP" == "1" ]]; then
    info "Skipping FTP"
    return
  fi
  [[ -n "$FTP_USER" && -n "$FTP_PASS" ]] || die "FTP_USER / FTP_PASS required in deploy.env"
  need_cmd python3

  info "FTP upload → ${FTP_USER}@${FTP_HOST}:${FTP_REMOTE_ROOT}"
  KEEP_REMOTE_ENV="$KEEP_REMOTE_ENV" \
  FTP_HOST="$FTP_HOST" FTP_USER="$FTP_USER" FTP_PASS="$FTP_PASS" \
  FTP_REMOTE_ROOT="$FTP_REMOTE_ROOT" ROOT="$ROOT" \
  python3 <<'PY'
import os, sys, time, json, hashlib
from pathlib import Path
from ftplib import FTP, error_perm

ROOT = Path(os.environ["ROOT"])
HOST = os.environ["FTP_HOST"]
USER = os.environ["FTP_USER"]
PASS = os.environ["FTP_PASS"]
REMOTE = os.environ.get("FTP_REMOTE_ROOT", "/htdocs").rstrip("/") or "/htdocs"
KEEP_CFG = os.environ.get("KEEP_REMOTE_ENV", "1") == "1"

pairs = []

# === Laravel public directory → htdocs root ===
public_dir = ROOT / "public"
if public_dir.is_dir():
    for p in public_dir.rglob("*"):
        if p.is_file() and p.name != ".DS_Store":
            rel = p.relative_to(public_dir).as_posix()
            # Skip .env if KEEP_REMOTE_ENV
            if KEEP_CFG and rel == ".env":
                continue
            pairs.append((p, f"{REMOTE}/{rel}"))

# === Laravel app directory (routes, controllers, models, views) ===
app_dir = ROOT / "app"
if app_dir.is_dir():
    for p in app_dir.rglob("*"):
        if p.is_file() and p.name != ".DS_Store":
            rel = p.relative_to(ROOT).as_posix()
            pairs.append((p, f"{REMOTE}/{rel}"))

# === config directory ===
config_dir = ROOT / "config"
if config_dir.is_dir():
    for p in config_dir.rglob("*"):
        if p.is_file() and p.name != ".DS_Store":
            rel = p.relative_to(ROOT).as_posix()
            pairs.append((p, f"{REMOTE}/{rel}"))

# === database directory (migrations, seeders) ===
database_dir = ROOT / "database"
if database_dir.is_dir():
    for p in database_dir.rglob("*"):
        if p.is_file() and p.name != ".DS_Store":
            rel = p.relative_to(ROOT).as_posix()
            pairs.append((p, f"{REMOTE}/{rel}"))

# === resources directory (views, lang) ===
resources_dir = ROOT / "resources"
if resources_dir.is_dir():
    for p in resources_dir.rglob("*"):
        if p.is_file() and p.name != ".DS_Store":
            rel = p.relative_to(ROOT).as_posix()
            pairs.append((p, f"{REMOTE}/{rel}"))

# === routes directory ===
routes_dir = ROOT / "routes"
if routes_dir.is_dir():
    for p in routes_dir.rglob("*"):
        if p.is_file() and p.name != ".DS_Store":
            rel = p.relative_to(ROOT).as_posix()
            pairs.append((p, f"{REMOTE}/{rel}"))

# === bootstrap directory ===
bootstrap_dir = ROOT / "bootstrap"
if bootstrap_dir.is_dir():
    for p in bootstrap_dir.rglob("*"):
        if p.is_file() and p.name != ".DS_Store":
            rel = p.relative_to(ROOT).as_posix()
            pairs.append((p, f"{REMOTE}/{rel}"))

# === storage directory (writable) ===
storage_dir = ROOT / "storage"
if storage_dir.is_dir():
    for p in storage_dir.rglob("*"):
        if p.is_file() and p.name != ".DS_Store":
            rel = p.relative_to(ROOT).as_posix()
            pairs.append((p, f"{REMOTE}/{rel}"))

# === vendor directory (composer dependencies) ===
vendor_dir = ROOT / "vendor"
if vendor_dir.is_dir():
    for p in vendor_dir.rglob("*"):
        if p.is_file() and p.name != ".DS_Store":
            rel = p.relative_to(ROOT).as_posix()
            pairs.append((p, f"{REMOTE}/{rel}"))

# === Root files ===
for root_file in [".htaccess", "artisan", "composer.json", "composer.lock", "server.php", "vendor/autoload.php"]:
    fp = ROOT / root_file
    if fp.is_file():
        pairs.append((fp, f"{REMOTE}/{root_file}"))

print(f"  files: {len(pairs)}")

# Load deploy cache
cache_file = ROOT / "scripts" / ".deploy_cache.json"
cache = {}
if cache_file.is_file():
    try:
        with open(cache_file, "r") as f:
            cache = json.load(f)
    except:
        pass

def get_md5(path):
    h = hashlib.md5()
    with open(path, "rb") as f:
        for chunk in iter(lambda: f.read(65536), b""):
            h.update(chunk)
    return h.hexdigest()

def connect():
    ftp = FTP()
    ftp.connect(HOST, 21, timeout=120)
    ftp.login(USER, PASS)
    ftp.set_pasv(True)
    return ftp

def ensure_dir(ftp, path):
    parts = [x for x in path.split("/") if x]
    cur = ""
    for part in parts:
        cur += "/" + part
        try:
            ftp.cwd(cur)
        except error_perm:
            try:
                ftp.mkd(cur)
            except error_perm:
                try:
                    ftp.cwd(cur)
                except error_perm as e:
                    raise e

ftp = None
ok = 0
skipped = 0

for i, (local, remote) in enumerate(sorted(pairs, key=lambda x: x[1]), 1):
    local_hash = get_md5(local)
    if cache.get(remote) == local_hash:
        skipped += 1
        continue

    if ftp is None:
        ftp = connect()

    ensure_dir(ftp, remote.rsplit("/", 1)[0])
    for attempt in range(1, 4):
        try:
            with open(local, "rb") as f:
                ftp.storbinary(f"STOR {remote}", f, blocksize=8192)
            ok += 1
            cache[remote] = local_hash
            break
        except Exception as e:
            if attempt == 3:
                print(f"FAIL {remote}: {e}", file=sys.stderr)
                sys.exit(1)
            time.sleep(1.5)
            try:
                ftp.quit()
            except Exception:
                try:
                    ftp.close()
                except Exception:
                    pass
            ftp = connect()
            ensure_dir(ftp, remote.rsplit("/", 1)[0])

    if (ok + skipped) % 25 == 0 or (ok + skipped) == len(pairs):
        print(f"  [{ok + skipped}/{len(pairs)}]")

if ftp is not None:
    try:
        ftp.quit()
    except Exception:
        pass

# Save deploy cache
try:
    with open(cache_file, "w") as f:
        json.dump(cache, f, indent=2)
except:
    pass

print(f"  uploaded {ok}/{len(pairs)} (skipped {skipped} unchanged files)")
if KEEP_CFG:
    print("  (kept remote .env)")
PY
  ok "FTP upload finished"
}

# =============================================================================
# 4) Remote install via HTTP (schema + seed on InfinityFree)
# =============================================================================
remote_install() {
  if [[ "$RUN_REMOTE_INSTALL" != "1" ]]; then
    info "Skipping remote install (pass --remote-install or RUN_REMOTE_INSTALL=1)"
    return
  fi
  need_cmd python3
  need_cmd curl
  info "Remote DB install via ${SITE_URL}/install.php"

  # Ensure install.php exists on server (uploaded with public/)
  if [[ ! -f "$ROOT/public/install.php" ]]; then
    # generate minimal installer if missing
    cat > "$ROOT/public/install.php" <<'PHP'
<?php
declare(strict_types=1);
$key = $_GET['key'] ?? '';
if ($key !== 'workforge2026') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Forbidden']);
    exit;
}
header('Content-Type: application/json; charset=utf-8');

// Load Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    // Run migrations
    $kernel->call('migrate', ['--force' => true]);
    $output = $kernel->output();

    // Run seeder if needed
    // $kernel->call('db:seed', ['--force' => true]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Migrations completed successfully',
        'output' => $output
    ], JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
PHP
  fi

  # Re-upload install.php only (quick)
  KEEP_REMOTE_ENV=1 FTP_HOST="$FTP_HOST" FTP_USER="$FTP_USER" FTP_PASS="$FTP_PASS" \
  FTP_REMOTE_ROOT="$FTP_REMOTE_ROOT" ROOT="$ROOT" python3 <<'PY'
import os
from pathlib import Path
from ftplib import FTP
ROOT=Path(os.environ["ROOT"])
ftp=FTP(); ftp.connect(os.environ["FTP_HOST"],21,timeout=60)
ftp.login(os.environ["FTP_USER"], os.environ["FTP_PASS"]); ftp.set_pasv(True)
remote=os.environ.get("FTP_REMOTE_ROOT","/htdocs")
for rel in ["public/install.php"]:
    local=ROOT/rel
    rpath=f"{remote}/{rel}"
    # mkdirs
    cur=""
    for part in rpath.split("/")[:-1]:
        if not part: continue
        cur+="/"+part
        try: ftp.cwd(cur)
        except:
            try: ftp.mkd(cur)
            except: pass
    with open(local,"rb") as f:
        ftp.storbinary(f"STOR {rpath}", f)
    print("  up", rpath)
ftp.quit()
PY

  local url="${SITE_URL%/}/install.php?key=${INSTALL_KEY}"
  info "Calling $url"
  # InfinityFree anti-bot: use python that solves cookie if needed
  SITE_URL="$SITE_URL" INSTALL_KEY="$INSTALL_KEY" python3 <<'PY'
import os, re, json, http.client
from urllib.parse import urlparse
try:
    from Crypto.Cipher import AES
except ImportError:
    import subprocess, sys
    subprocess.check_call([sys.executable, "-m", "pip", "install", "pycryptodome", "-q"])
    from Crypto.Cipher import AES

site = os.environ["SITE_URL"].rstrip("/")
key = os.environ["INSTALL_KEY"]
parsed = urlparse(site)
host = parsed.hostname
port = parsed.port or 80

def get_cookie(ip_or_host):
    conn = http.client.HTTPConnection(ip_or_host, port, timeout=45)
    conn.request("GET", "/", headers={"Host": host, "User-Agent": "Mozilla/5.0"})
    html = conn.getresponse().read().decode("utf-8", "replace")
    conn.close()
    m = re.search(r'toNumbers\("([0-9a-f]+)"\).*toNumbers\("([0-9a-f]+)"\).*toNumbers\("([0-9a-f]+)"\)', html, re.S)
    if not m:
        return None
    a,b,c = [bytes.fromhex(x) for x in m.groups()]
    return AES.new(a, AES.MODE_CBC, b).decrypt(c).hex()

# Prefer forced InfinityFree IP if host is workforgemarketplace.gt.tc style
import socket
try:
    ip = socket.gethostbyname(host)
except Exception:
    ip = host
cookie = get_cookie(ip) or get_cookie(host)
headers = {"Host": host, "User-Agent": "Mozilla/5.0"}
if cookie:
    headers["Cookie"] = f"__test={cookie}"

path = f"/install.php?key={key}"
conn = http.client.HTTPConnection(ip, port, timeout=120)
conn.request("GET", path, headers=headers)
resp = conn.getresponse()
body = resp.read().decode("utf-8", "replace")
conn.close()
print(body[:2000])
try:
    data = json.loads(body)
    if data.get("status") != "success":
        raise SystemExit(1)
except json.JSONDecodeError:
    # maybe challenge still
    raise SystemExit("Install response not JSON — check SITE_URL / DNS")

# delete install.php via FTP
from ftplib import FTP
ftp=FTP(); ftp.connect(os.environ["FTP_HOST"],21,timeout=60)
ftp.login(os.environ["FTP_USER"], os.environ["FTP_PASS"]); ftp.set_pasv(True)
try:
    ftp.delete(os.environ.get("FTP_REMOTE_ROOT","/htdocs") + "/public/install.php")
    print("  deleted remote install.php")
except Exception as e:
    print("  warn: could not delete install.php:", e)
ftp.quit()
PY
  ok "Remote install finished (install.php removed if possible)"
}

# =============================================================================
# 5) Smoke test
# =============================================================================
smoke_test() {
  if [[ "$SMOKE_TEST" != "1" || "$SKIP_FTP" == "1" ]]; then
    return
  fi
  info "Smoke test ${SITE_URL}"
  SITE_URL="$SITE_URL" python3 <<'PY'
import os, re, json, http.client, socket
from urllib.parse import urlparse
try:
    from Crypto.Cipher import AES
except ImportError:
    import subprocess, sys
    subprocess.check_call([sys.executable, "-m", "pip", "install", "pycryptodome", "-q"])
    from Crypto.Cipher import AES

site = os.environ["SITE_URL"].rstrip("/")
host = urlparse(site).hostname
try:
    ip = socket.gethostbyname(host)
except Exception:
    ip = host

def cookie():
    conn = http.client.HTTPConnection(ip, 80, timeout=30)
    conn.request("GET", "/", headers={"Host": host, "User-Agent": "Mozilla/5.0"})
    html = conn.getresponse().read().decode()
    conn.close()
    m = re.search(r'toNumbers\("([0-9a-f]+)"\).*toNumbers\("([0-9a-f]+)"\).*toNumbers\("([0-9a-f]+)"\)', html, re.S)
    if not m:
        return None
    a,b,c = [bytes.fromhex(x) for x in m.groups()]
    return AES.new(a, AES.MODE_CBC, b).decrypt(c).hex()

c = cookie()
H = {"Host": host, "User-Agent": "Mozilla/5.0", "Content-Type": "application/json"}
if c:
    H["Cookie"] = f"__test={c}"

def get(path):
    conn = http.client.HTTPConnection(ip, 80, timeout=30)
    conn.request("GET", path, headers=H)
    r = conn.getresponse(); b = r.read(); conn.close()
    return r.status, b

st, body = get("/")
print(f"  / → {st}")
assert st == 200, "homepage failed"

st, body = get("/build/manifest.json")
print(f"  /build/manifest.json → {st}")

print("  smoke OK")
PY
  ok "Smoke test passed"
}

# =============================================================================
# Main
# =============================================================================
echo ""
echo "╔══════════════════════════════════════╗"
echo "║     WorkForge deploy                  ║"
echo "╚══════════════════════════════════════╝"
echo "  root: $ROOT"
echo "  env:  $ENV_FILE"
echo ""

build_frontend
local_db
remote_db_cli
ftp_upload
remote_install
smoke_test

echo ""
echo "All done."
echo "  Site: ${SITE_URL}"
echo ""
echo "Examples:"
echo "  ./scripts/deploy.sh                  # build + FTP + smoke"
echo "  ./scripts/deploy.sh --local-db       # also migrate local MySQL"
echo "  ./scripts/deploy.sh --remote-install # migrate live DB after upload"
echo "  ./scripts/deploy.sh --ftp-only       # upload only"
echo ""
