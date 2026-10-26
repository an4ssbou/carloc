# Réception GPS — contrat et essai synthétique

Activer GPS sur le véhicule, ouvrir son suivi propriétaire puis « Raccorder un boîtier GPS ». La création d'une clé remplace l'ancienne; seul son SHA256 est stocké. Après révélation unique, conserver le secret dans le futur émetteur, jamais dans une URL ou un fichier du dépôt. Pour réutiliser le bouton de simulation propriétaire, révoquer la clé.

`POST /api/gps/vehicles/{uuid}/positions`

En-têtes : `Authorization: Bearer <clé>` et `Content-Type: application/json`.

| Champ | Contrat |
|---|---|
| latitude | Nombre compris entre−90et90; zéro valide |
| longitude | Nombre compris entre−180et180; zéro valide |
| recorded_at | Date ISO8601 avec fuseau, entre maintenant−24h et maintenant+30s |
| source | `simulation` pour essais; `device` par défaut, label déclaratif sans authentification physique du matériel |

Dernière position seulement. Une date plus ancienne reçoit409; même date et coordonnées/source identiques sont idempotentes, sans changer l'heure de réception; même date différente reçoit409. Erreur de validation422, clé absente/invalide401, propriétaire/agence non autorisés403, GPSdésactivé409 et limitation60requêtes/minute429. Après désactivation, l'ancienne clé est révoquée : elle reçoit401 et ne redevient pas valide à la réactivation.

## Exemple local sans secret dans l'historique shell

Ce programme ne fait qu'envoyer une position **fictive**; il n'utilise pas la géolocalisation de l'ordinateur. Exécuter avec Python3, saisir l'UUID et la clé au moment demandé. La clé est masquée et n'est ni imprimée ni sauvegardée.

```python
import json
import uuid
from datetime import datetime, timezone
from getpass import getpass
from urllib.request import Request, urlopen

vehicle = str(uuid.UUID(input("UUID du véhicule : ").strip()))
key = getpass("Clé GPS : ")
payload = {
    "latitude": 33.5731,
    "longitude": -7.5898,
    "recorded_at": datetime.now(timezone.utc).isoformat(),
    "source": "simulation",
}
request = Request(
    f"http://127.0.0.1:8000/api/gps/vehicles/{vehicle}/positions",
    data=json.dumps(payload).encode(),
    headers={"Authorization": f"Bearer {key}", "Content-Type": "application/json"},
    method="POST",
)
with urlopen(request, timeout=10) as response:
    print("Position synthétique reçue, HTTP", response.status)
```

La page de suivi privée se met à jour automatiquement après réception. Pour connecter un appareil réel, cadrer son protocole et l'accès HTTPS : le serveur actuel reste local, sans publication. Aucun boîtier ni opérateur mobile n'a été testé.

## Carte et données

Leaflet1.9.4 est servi localement. Le fond OpenStreetMap se charge uniquement après clic, avec attribution visible. Pas de téléchargement massif ni de garantie de disponibilité; les coordonnées restent lisibles sans fond. [Politique officielle des tuiles consultée le25octobre2026](https://operations.osmfoundation.org/policies/tiles/).
