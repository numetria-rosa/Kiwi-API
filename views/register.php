<?php
// Définir le titre et les styles spécifiques à la page
$page_title = 'Créer un compte';
$page_css = 'auth';
$page_js = 'auth';

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    redirect('home');
}

// Initialiser les variables
$error = '';
$success = '';
$first_name = '';
$last_name = '';
$email = '';
$phone = '';
$country = '';

// Traiter le formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $first_name = isset($_POST['first_name']) ? sanitize_input($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? sanitize_input($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $country = isset($_POST['country']) ? sanitize_input($_POST['country']) : '';
    $terms = isset($_POST['terms']) ? true : false;

    // Valider les données
    if (empty($first_name) || empty($last_name)) {
        $error = 'Veuillez saisir votre nom et prénom.';
    } elseif (empty($email)) {
        $error = 'Veuillez saisir votre adresse e-mail.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Veuillez saisir une adresse e-mail valide.';
    } elseif (empty($password)) {
        $error = 'Veuillez saisir un mot de passe.';
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (!$terms) {
        $error = 'Vous devez accepter les conditions générales.';
    } else {
        // Créer une instance du modèle User
        $userModel = new User($pdo);

        // Vérifier si l'email existe déjà
        if ($userModel->emailExists($email)) {
            $error = 'Cette adresse e-mail est déjà utilisée.';
        } else {
            // Préparer les données de l'utilisateur
            $userData = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'country' => $country,
                'date_of_birth' => null // Non demandé dans le formulaire
            ];

            // Tenter l'inscription
            $userId = $userModel->register($userData);

            if ($userId) {
                // Inscription réussie
                $success = 'Votre compte a été créé avec succès !';

                // Connexion automatique
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $first_name . ' ' . $last_name;

                // Rediriger après un court délai
                echo '<meta http-equiv="refresh" content="2;url=' . generate_url(isset($_GET['redirect']) ? $_GET['redirect'] : 'home') . '">';
            } else {
                $error = 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.';
            }
        }
    }
}

// Inclure l'en-tête
include 'includes/header.php';
?>

<div class="auth-container">
    <div class="container">
        <div class="auth-content">
            <h1 class="auth-title">Créer un compte</h1>

            <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <div class="loader">Redirection en cours...</div>
            </div>
            <?php else: ?>

            <form method="POST" action="<?php echo generate_url('register', isset($_GET['redirect']) ? ['redirect' => $_GET['redirect']] : []); ?>" class="auth-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Prénom</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required autocomplete="given-name">
                    </div>

                    <div class="form-group">
                        <label for="last_name">Nom</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required autocomplete="family-name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required autocomplete="email">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password" minlength="8">
                        <div class="form-hint">Minimum 8 caractères</div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password" minlength="8">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Téléphone (optionnel)</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $phone; ?>" autocomplete="tel">
                    </div>

                    <div class="form-group">
                        <label for="country">Pays (optionnel)</label>
                        <select id="country" name="country" autocomplete="country">
                            <option value="">Sélectionnez un pays</option>
                            <option value="France" <?php echo $country === 'France' ? 'selected' : ''; ?>>France</option>
                            <option value="Belgique" <?php echo $country === 'Belgique' ? 'selected' : ''; ?>>Belgique</option>
                            <option value="Suisse" <?php echo $country === 'Suisse' ? 'selected' : ''; ?>>Suisse</option>
                            <option value="Canada" <?php echo $country === 'Canada' ? 'selected' : ''; ?>>Canada</option>
                            <!-- Ajouter d'autres pays au besoin -->
                        </select>
                    </div>
                </div>

                <div class="form-group-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">J'accepte les <a href="#" target="_blank">conditions générales</a> et la <a href="#" target="_blank">politique de confidentialité</a></label>
                </div>

                <div class="form-group-checkbox">
                    <input type="checkbox" id="newsletter" name="newsletter">
                    <label for="newsletter">Je souhaite recevoir des offres et promotions par e-mail</label>
                </div>

                <div class="form-submit">
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Créer mon compte</button>
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

            <?php endif; ?>

            <div class="auth-footer">
                <p>Vous avez déjà un compte ? <a href="<?php echo generate_url('login', isset($_GET['redirect']) ? ['redirect' => $_GET['redirect']] : []); ?>">Se connecter</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
