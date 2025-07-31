<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - AlerteUM</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('{{ asset('bg-um.webp') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            width: 100vw;
            color: white;
        }

        .container-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(0, 0, 0, 0.8);
            padding: 30px 40px;
            border-radius: 20px;
            text-align: center;
            max-width: 700px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.9);
        }

        .container h1 {
            font-size: 2.5em;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .container h1 span {
            color: #facc15; /* amarillo */
        }

        .logo {
            width: 150px;
            margin-bottom: 5px;
        }

        ul {
            text-align: left;
            margin: 30px auto;
            max-width: 500px;
            padding-left: 0;
            list-style: none;
        }

        ul li {
            margin-bottom: 15px;
            font-size: 1.1em;
            position: relative;
            padding-left: 25px;
        }

        ul li::before {
            content: '•';
            color: #60a5fa; /* azul claro */
            font-size: 1.5em;
            position: absolute;
            left: 0;
            top: 0;
        }

        .buttons {
            margin-top: 30px;
        }

        .buttons a {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            margin: 0 10px;
        }

        .buttons a.login {
            background-color: #6366f1;
            color: white;
        }

        .buttons a.login:hover {
            background-color: #4f46e5;
        }

        .buttons a.register {
            background-color: white;
            color: #374151;
        }

        .buttons a.register:hover {
            background-color: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container-wrapper">
        <div class="container">
            <img src="{{ asset('logo-um.webp') }}" alt="Logo" class="logo">
            <h1 style="margin-top: 10px;">Bienvenue sur la plateforme <span>AlerteUM</span></h1>
            <p>
                Plateforme numérique officielle pour signaler et suivre en temps réel les incidents 
                techniques, administratifs ou de sécurité de l’Université.
            </p>
            <ul>
                <li>Soumettre un incident rapidement et facilement</li>
                <li>Suivre l’état d’avancement des signalements</li>
                <li>Accéder à l’historique complet</li>
                <li>Améliorer la communication avec les services de l’UM</li>
            </ul>
            <div class="buttons">
                <a href="{{ route('login') }}" class="login">Se connecter</a>
                <a href="{{ route('register') }}" class="register">S’inscrire</a>
            </div>
        </div>
    </div>
</body>
</html>