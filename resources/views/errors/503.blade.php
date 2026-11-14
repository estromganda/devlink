<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>503 — Maintenance | DevColab</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 24px;
            background: #f9fafb;
            color: #1f2937;
            font-family: system-ui, -apple-system, BlinkMacSystemFont,
                "Segoe UI", sans-serif;
            text-align: center;
        }

        main {
            width: 100%;
            max-width: 520px;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .icon {
            display: flex;
            width: 96px;
            height: 96px;
            align-items: center;
            justify-content: center;
            margin: 48px auto 24px;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            background: white;
            font-size: 42px;
        }

        .code {
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 3px;
        }

        h1 {
            margin: 12px 0;
            font-size: clamp(28px, 5vw, 40px);
        }

        p {
            color: #6b7280;
            line-height: 1.7;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            border-radius: 12px;
            background: #111827;
            color: white;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        footer {
            margin-top: 56px;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <main>
        <div class="logo">DevColab</div>

        <div class="icon" aria-hidden="true">🔧</div>

        <div class="code">ERREUR 503</div>

        <h1>Nous revenons bientôt.</h1>

        <p>
            DevColab est temporairement indisponible pour maintenance.
            Merci de réessayer dans quelques instants.
        </p>

        <a href="/">Retour à l'accueil</a>

        <footer>
            © {{ date('Y') }} DevColab.
            Par les développeurs, pour les développeurs.
        </footer>
    </main>
</body>

</html>