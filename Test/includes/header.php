<?php
include('functions.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Projet Kiwi' : 'Projet Kiwi - Clone de Kiwi.com'; ?></title>

    <!-- Polices -->
    <!-- <link rel="stylesheet" href="/assets/fonts/circular-pro.css"> -->

    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">

    <!-- Styles spécifiques à la page -->
    <?php if (isset($page_css)): ?>
    <link rel="stylesheet" href="/assets/css/<?php echo $page_css; ?>.css">
    <?php endif; ?>
    

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
</head>
<body>
    <!-- En-tête principal -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <div class="logo">
                    <a href="<?php echo generate_url('home'); ?>">
                        <!-- <img src="/assets/img/kiwi-logo.svg" alt="Projet Kiwi" class="desktop-logo"> -->
                        <!-- <img src="/assets/img/kiwi-logo-mobile.svg" alt="Projet Kiwi" class="mobile-logo"> -->
                    </a>
                </div>

                <!-- Navigation principale -->
                <nav class="main-nav">
                    <ul>
                        <li class="active"><a href="<?php echo generate_url('home'); ?>">Vols</a></li>
                        <li><a href="#">Voitures</a></li>
                        <li><a href="#">Hébergements</a></li>
                        <li><a href="#">Magazine</a></li>
                        <li><a href="#">Astuces de voyage</a></li>
                        <li><a href="#">Offres</a></li>
                    </ul>
                </nav>

                <!-- Menu utilisateur -->
                <div class="user-menu">
                    <!-- Sélecteur de devise -->
                    <div class="currency-selector">
                        <span>EUR</span>
                    </div>

                    <!-- Aide et support -->
                    <div class="help-support">
                        <a href="#">Aide & support</a>
                    </div>

                    <!-- Connexion -->
                    <div class="signin">
                        <a href="#">Se connecter</a>
                    </div>

                    <!-- Menu hamburger pour mobile -->
                    <div class="menu-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <main>
