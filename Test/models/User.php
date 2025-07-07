<?php
/**
 * Modèle pour la gestion des utilisateurs
 */
class User {
    private $db;

    /**
     * Constructeur
     *
     * @param PDO $db Instance de PDO
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupérer tous les utilisateurs
     *
     * @return array Liste des utilisateurs
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT id, email, first_name, last_name, phone, date_of_birth, country, created_at FROM users ORDER BY id ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des utilisateurs: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer un utilisateur par son ID
     *
     * @param int $id ID de l'utilisateur
     * @return array|false Données de l'utilisateur ou false si non trouvé
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, email, first_name, last_name, phone, date_of_birth, country, created_at FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'utilisateur: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un utilisateur par son email
     *
     * @param string $email Email de l'utilisateur
     * @return array|false Données de l'utilisateur ou false si non trouvé
     */
    public function getByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'utilisateur par email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Création d'un nouvel utilisateur
     *
     * @param array $data Données de l'utilisateur
     * @return int|false ID du nouvel utilisateur ou false en cas d'échec
     */
    public function register($data) {
        try {
            // Vérifier si l'email existe déjà
            $checkStmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
            $checkStmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                return false; // Email déjà utilisé
            }

            // Hasher le mot de passe
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("
                INSERT INTO users (email, password, first_name, last_name, phone, date_of_birth, country)
                VALUES (:email, :password, :first_name, :last_name, :phone, :date_of_birth, :country)
            ");

            $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':first_name', $data['first_name'], PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $data['last_name'], PDO::PARAM_STR);
            $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
            $stmt->bindParam(':date_of_birth', $data['date_of_birth'], PDO::PARAM_STR);
            $stmt->bindParam(':country', $data['country'], PDO::PARAM_STR);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log('Erreur lors de l\'inscription de l\'utilisateur: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Connexion d'un utilisateur
     *
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe
     * @return array|false Données de l'utilisateur ou false si échec
     */
    public function login($email, $password) {
        try {
            $user = $this->getByEmail($email);

            if (!$user) {
                return false; // Utilisateur non trouvé
            }

            // Vérifier le mot de passe
            if (password_verify($password, $user['password'])) {
                // Ne pas renvoyer le mot de passe
                unset($user['password']);
                return $user;
            }

            return false; // Mot de passe incorrect
        } catch (PDOException $e) {
            error_log('Erreur lors de la connexion de l\'utilisateur: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour un utilisateur
     *
     * @param int $id ID de l'utilisateur
     * @param array $data Nouvelles données
     * @return bool Succès ou échec
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $values = [];

            // Construire dynamiquement la requête en fonction des champs fournis
            foreach ($data as $field => $value) {
                if ($field !== 'id' && $field !== 'password') { // Exclure l'ID et traiter le mot de passe séparément
                    $fields[] = "$field = :$field";
                    $values[":$field"] = $value;
                }
            }

            // Traiter le mot de passe s'il est fourni
            if (isset($data['password']) && !empty($data['password'])) {
                $fields[] = "password = :password";
                $values[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            if (empty($fields)) {
                return false; // Aucun champ à mettre à jour
            }

            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
            $values[':id'] = $id;

            $stmt = $this->db->prepare($sql);

            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log('Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un utilisateur
     *
     * @param int $id ID de l'utilisateur
     * @return bool Succès ou échec
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Erreur lors de la suppression de l\'utilisateur: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifier si un email existe déjà
     *
     * @param string $email Email à vérifier
     * @return bool True si l'email existe, sinon false
     */
    public function emailExists($email) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Erreur lors de la vérification de l\'email: ' . $e->getMessage());
            return false;
        }
    }
}
