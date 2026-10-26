"""Empreinte du code livré, hors secrets, données et preuves modifiables."""
from pathlib import Path
import hashlib

root = Path(__file__).resolve().parents[1]
paths = []
for name in ('app', 'bootstrap', 'config', 'database', 'resources', 'routes'):
    paths.extend(p for p in (root / 'application' / name).rglob('*')
                 if p.is_file() and 'cache' not in p.relative_to(root / 'application' / name).parts)
paths.extend(p for p in (root / 'application' / 'public').rglob('*')
             if p.is_file() and p.relative_to(root / 'application' / 'public').parts[0] not in {'build', 'storage', 'hot'})
for name in ('composer.json', 'composer.lock', 'package.json', 'package-lock.json', 'vite.config.js', 'tailwind.config.js', 'phpunit.xml', 'artisan', 'compose.yaml', '.env.example'):
    p = root / 'application' / name
    if p.is_file():
        paths.append(p)
paths.extend(p for p in (root / 'scripts').glob('*') if p.is_file())
digest = hashlib.sha256()
for p in sorted(set(paths)):
    digest.update(p.relative_to(root).as_posix().encode() + b'\0' + p.read_bytes() + b'\0')
print('sha256:' + digest.hexdigest())
