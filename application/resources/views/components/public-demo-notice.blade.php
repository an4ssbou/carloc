@if(config('demo.enabled'))
<aside role="note" aria-label="Démonstration publique" style="padding:14px 18px;background:#0d2137;color:white;border-bottom:3px solid #feb234;font:14px/1.5 system-ui;overflow-wrap:anywhere">
 <strong>DÉMONSTRATION PUBLIQUE</strong> — Données fictives partagées, remises à zéro au redémarrage. Aucun paiement, email, GPS matériel ou document réel. Ne saisissez aucune donnée personnelle.
 <details><summary>Comptes d’essai et limites</summary>
 <p>Client : <code>client@demo.carloc.test</code> · Agence : <code>agence@demo.carloc.test</code> · Administration : <code>admin@demo.carloc.test</code><br>Mot de passe public commun : <code>Demo-Portfolio-2026!</code></p>
 <p>Réservez, confirmez ou annulez. Profil, mots de passe, inscriptions, envois, téléversements et modification du catalogue sont désactivés. Les visiteurs d’un même rôle partagent les modifications.</p>
 <a href="{{ route('login') }}" style="color:#ffddb2;text-decoration:underline">Se connecter</a> · <a href="{{ route('vehicles.index') }}" style="color:#ffddb2;text-decoration:underline">Essayer le catalogue</a>
 </details>
</aside>
@endif
