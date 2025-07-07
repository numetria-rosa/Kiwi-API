<?php
// Définir le titre et les styles spécifiques à la page
$page_title = 'Connexion';
$page_css = 'auth';
$page_js = 'auth';

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    redirect('home');
}

// Traiter le formulaire de connexion
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;

    // Valider les données
    if (empty($email)) {
        $error = 'Veuillez saisir votre adresse e-mail.';
    } elseif (empty($password)) {
        $error = 'Veuillez saisir votre mot de passe.';
    } else {
        // Créer une instance du modèle User
        $userModel = new User($pdo);

        // Tenter la connexion
        $user = $userModel->login($email, $password);

        if ($user) {
            // Connexion réussie, créer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

            // Si "Se souvenir de moi" est coché, créer un cookie de longue durée
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                // Dans une implémentation réelle, on stockerait ce token en base de données
                setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/');
            }

            // Rediriger vers la page d'accueil ou la page précédente
            $redirect_to = isset($_GET['redirect']) ? $_GET['redirect'] : 'home';
            redirect($redirect_to);
        } else {
            // Échec de la connexion
            $error = 'Adresse e-mail ou mot de passe incorrect.';
        }
    }
}

// Inclure l'en-tête
include 'includes/header.php';
?>

<div class="auth-container">
    <div class="container">
        <div class="auth-content">
            <h1 class="auth-title">Connexion</h1>

            <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo generate_url('login', isset($_GET['redirect']) ? ['redirect' => $_GET['redirect']] : []); ?>" class="auth-form">
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>

                <div class="form-group-inline">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    <a href="<?php echo generate_url('forgot_password'); ?>" class="link-forgot">Mot de passe oublié ?</a>
                </div>

                <div class="form-submit">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Se connecter</button>
                </div>
            </form>

            <div class="auth-separator">
                <span>ou</span>
            </div>

            <div class="social-login">
                <button type="button" class="btn btn-social btn-google">
                    <img src="assets/img/google-icon.svg" alt="Google">
                    Continuer avec Google
                </button>
                <button type="button" class="btn btn-social btn-facebook">
                    <img src="assets/img/facebook-icon.svg" alt="Facebook">
                    Continuer avec Facebook
                </button>
                <button type="button" class="btn btn-social btn-apple">
                    <img src="assets/img/apple-icon.svg" alt="Apple">
                    Continuer avec Apple
                </button>
            </div>

            <div class="auth-footer">
                <p>Vous n'avez pas de compte ? <a href="<?php echo generate_url('register', isset($_GET['redirect']) ? ['redirect' => $_GET['redirect']] : []); ?>">Créer un compte</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
