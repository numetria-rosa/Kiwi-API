<?php
/**
 * Modèle pour la gestion des aéroports
 */
class Airport {
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
     * Récupérer tous les aéroports
     *
     * @return array Liste des aéroports
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM airports ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des aéroports: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer un aéroport par son ID
     *
     * @param int $id ID de l'aéroport
     * @return array|false Données de l'aéroport ou false si non trouvé
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM airports WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'aéroport: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer un aéroport par son code IATA
     *
     * @param string $code Code IATA de l'aéroport
     * @return array|false Données de l'aéroport ou false si non trouvé
     */
    public function getByCode($code) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM airports WHERE code = :code");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'aéroport par code: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Rechercher des aéroports par terme
     *
     * @param string $term Terme de recherche (nom, ville ou pays)
     * @param int $limit Limite de résultats
     * @return array Liste des aéroports correspondants
     */
    public function search($term, $limit = 10) {
        try {
            $searchTerm = '%' . $term . '%';
            $stmt = $this->db->prepare("
                SELECT * FROM airports
                WHERE name LIKE :term
                OR city LIKE :term
                OR country LIKE :term
                OR code LIKE :term
                ORDER BY name ASC
                LIMIT :limit
            ");
            $stmt->bindParam(':term', $searchTerm, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la recherche d\'aéroports: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ajouter un nouvel aéroport
     *
     * @param array $data Données de l'aéroport
     * @return int|false ID du nouvel aéroport ou false en cas d'échec
     */
    public function add($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO airports (code, name, city, country, latitude, longitude)
                VALUES (:code, :name, :city, :country, :latitude, :longitude)
            ");
            $stmt->bindParam(':code', $data['code'], PDO::PARAM_STR);
            $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':city', $data['city'], PDO::PARAM_STR);
            $stmt->bindParam(':country', $data['country'], PDO::PARAM_STR);
            $stmt->bindParam(':latitude', $data['latitude'], PDO::PARAM_STR);
            $stmt->bindParam(':longitude', $data['longitude'], PDO::PARAM_STR);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log('Erreur lors de l\'ajout de l\'aéroport: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour un aéroport
     *
     * @param int $id ID de l'aéroport
     * @param array $data Nouvelles données
     * @return bool Succès ou échec
     */
    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE airports
                SET code = :code,
                    name = :name,
                    city = :city,
                    country = :country,
                    latitude = :latitude,
                    longitude = :longitude
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':code', $data['code'], PDO::PARAM_STR);
            $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindParam(':city', $data['city'], PDO::PARAM_STR);
            $stmt->bindParam(':country', $data['country'], PDO::PARAM_STR);
            $stmt->bindParam(':latitude', $data['latitude'], PDO::PARAM_STR);
            $stmt->bindParam(':longitude', $data['longitude'], PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Erreur lors de la mise à jour de l\'aéroport: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un aéroport
     *
     * @param int $id ID de l'aéroport
     * @return bool Succès ou échec
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM airports WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Erreur lors de la suppression de l\'aéroport: ' . $e->getMessage());
            return false;
        }
    }
}
