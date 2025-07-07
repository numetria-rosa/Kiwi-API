<?php
/**
 * Modèle pour la gestion des réservations
 */
class Booking {
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
     * Récupérer toutes les réservations
     *
     * @return array Liste des réservations
     */
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM bookings ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des réservations: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer une réservation par son ID
     *
     * @param int $id ID de la réservation
     * @return array|false Données de la réservation ou false si non trouvée
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de la réservation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer une réservation par sa référence
     *
     * @param string $reference Référence de la réservation
     * @return array|false Données de la réservation ou false si non trouvée
     */
    public function getByReference($reference) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM bookings WHERE booking_reference = :reference");
            $stmt->bindParam(':reference', $reference, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de la réservation par référence: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les réservations d'un utilisateur
     *
     * @param int $userId ID de l'utilisateur
     * @return array Liste des réservations
     */
    public function getByUserId($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM bookings WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des réservations de l\'utilisateur: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Créer une nouvelle réservation
     *
     * @param array $data Données de la réservation
     * @return int|false ID de la nouvelle réservation ou false en cas d'échec
     */
    public function create($data) {
        try {
            // Commencer une transaction
            $this->db->beginTransaction();

            // Générer une référence de réservation unique
            $bookingReference = $this->generateBookingReference();

            // Créer la réservation
            $stmt = $this->db->prepare("
                INSERT INTO bookings (user_id, booking_reference, total_price, currency, status, payment_method)
                VALUES (:user_id, :booking_reference, :total_price, :currency, :status, :payment_method)
            ");

            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':booking_reference', $bookingReference, PDO::PARAM_STR);
            $stmt->bindParam(':total_price', $data['total_price'], PDO::PARAM_STR);
            $stmt->bindParam(':currency', $data['currency'], PDO::PARAM_STR);
            $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);
            $stmt->bindParam(':payment_method', $data['payment_method'], PDO::PARAM_STR);

            if (!$stmt->execute()) {
                $this->db->rollBack();
                return false;
            }

            $bookingId = $this->db->lastInsertId();

            // Ajouter les vols
            foreach ($data['flights'] as $flight) {
                $flightStmt = $this->db->prepare("
                    INSERT INTO booked_flights (booking_id, airline_code, flight_number, departure_airport, arrival_airport,
                                              departure_datetime, arrival_datetime, cabin_class, is_return)
                    VALUES (:booking_id, :airline_code, :flight_number, :departure_airport, :arrival_airport,
                            :departure_datetime, :arrival_datetime, :cabin_class, :is_return)
                ");

                $flightStmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
                $flightStmt->bindParam(':airline_code', $flight['airline_code'], PDO::PARAM_STR);
                $flightStmt->bindParam(':flight_number', $flight['flight_number'], PDO::PARAM_STR);
                $flightStmt->bindParam(':departure_airport', $flight['departure_airport'], PDO::PARAM_STR);
                $flightStmt->bindParam(':arrival_airport', $flight['arrival_airport'], PDO::PARAM_STR);
                $flightStmt->bindParam(':departure_datetime', $flight['departure_datetime'], PDO::PARAM_STR);
                $flightStmt->bindParam(':arrival_datetime', $flight['arrival_datetime'], PDO::PARAM_STR);
                $flightStmt->bindParam(':cabin_class', $flight['cabin_class'], PDO::PARAM_STR);
                $flightStmt->bindParam(':is_return', $flight['is_return'], PDO::PARAM_BOOL);

                if (!$flightStmt->execute()) {
                    $this->db->rollBack();
                    return false;
                }
            }

            // Ajouter les passagers
            foreach ($data['passengers'] as $passenger) {
                $passengerStmt = $this->db->prepare("
                    INSERT INTO passengers (booking_id, first_name, last_name, date_of_birth,
                                         passport_number, passport_expiry, nationality, is_primary)
                    VALUES (:booking_id, :first_name, :last_name, :date_of_birth,
                            :passport_number, :passport_expiry, :nationality, :is_primary)
                ");

                $passengerStmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
                $passengerStmt->bindParam(':first_name', $passenger['first_name'], PDO::PARAM_STR);
                $passengerStmt->bindParam(':last_name', $passenger['last_name'], PDO::PARAM_STR);
                $passengerStmt->bindParam(':date_of_birth', $passenger['date_of_birth'], PDO::PARAM_STR);
                $passengerStmt->bindParam(':passport_number', $passenger['passport_number'], PDO::PARAM_STR);
                $passengerStmt->bindParam(':passport_expiry', $passenger['passport_expiry'], PDO::PARAM_STR);
                $passengerStmt->bindParam(':nationality', $passenger['nationality'], PDO::PARAM_STR);
                $passengerStmt->bindParam(':is_primary', $passenger['is_primary'], PDO::PARAM_BOOL);

                if (!$passengerStmt->execute()) {
                    $this->db->rollBack();
                    return false;
                }

                // Ajouter les options supplémentaires pour chaque passager
                if (isset($passenger['extras']) && is_array($passenger['extras'])) {
                    $passengerId = $this->db->lastInsertId();

                    foreach ($passenger['extras'] as $extra) {
                        $extraStmt = $this->db->prepare("
                            INSERT INTO booking_extras (booking_id, passenger_id, flight_id, extra_type, extra_details, price, currency)
                            VALUES (:booking_id, :passenger_id, :flight_id, :extra_type, :extra_details, :price, :currency)
                        ");

                        $extraStmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
                        $extraStmt->bindParam(':passenger_id', $passengerId, PDO::PARAM_INT);
                        $extraStmt->bindParam(':flight_id', $extra['flight_id'], PDO::PARAM_INT);
                        $extraStmt->bindParam(':extra_type', $extra['extra_type'], PDO::PARAM_STR);
                        $extraStmt->bindParam(':extra_details', $extra['extra_details'], PDO::PARAM_STR);
                        $extraStmt->bindParam(':price', $extra['price'], PDO::PARAM_STR);
                        $extraStmt->bindParam(':currency', $extra['currency'], PDO::PARAM_STR);

                        if (!$extraStmt->execute()) {
                            $this->db->rollBack();
                            return false;
                        }
                    }
                }
            }

            // Valider la transaction
            $this->db->commit();

            return $bookingId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Erreur lors de la création de la réservation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour le statut d'une réservation
     *
     * @param int $id ID de la réservation
     * @param string $status Nouveau statut
     * @return bool Succès ou échec
     */
    public function updateStatus($id, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE bookings SET status = :status WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Erreur lors de la mise à jour du statut de la réservation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Annuler une réservation
     *
     * @param int $id ID de la réservation
     * @return bool Succès ou échec
     */
    public function cancel($id) {
        return $this->updateStatus($id, 'cancelled');
    }

    /**
     * Obtenir les vols d'une réservation
     *
     * @param int $bookingId ID de la réservation
     * @return array Liste des vols
     */
    public function getFlights($bookingId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM booked_flights WHERE booking_id = :booking_id ORDER BY is_return ASC, departure_datetime ASC");
            $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des vols de la réservation: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir les passagers d'une réservation
     *
     * @param int $bookingId ID de la réservation
     * @return array Liste des passagers
     */
    public function getPassengers($bookingId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM passengers WHERE booking_id = :booking_id ORDER BY is_primary DESC");
            $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des passagers de la réservation: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir les options supplémentaires d'une réservation
     *
     * @param int $bookingId ID de la réservation
     * @return array Liste des options
     */
    public function getExtras($bookingId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM booking_extras WHERE booking_id = :booking_id");
            $stmt->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des options de la réservation: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Générer une référence de réservation unique
     *
     * @return string Référence de réservation
     */
    private function generateBookingReference() {
        // Format: KW-XXXXXX (où X est un caractère alphanumérique)
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $reference = 'KW-';

        for ($i = 0; $i < 6; $i++) {
            $reference .= $chars[rand(0, strlen($chars) - 1)];
        }

        // Vérifier si la référence existe déjà
        $stmt = $this->db->prepare("SELECT id FROM bookings WHERE booking_reference = :reference");
        $stmt->bindParam(':reference', $reference, PDO::PARAM_STR);
        $stmt->execute();

        // Si elle existe, en générer une nouvelle récursivement
        if ($stmt->rowCount() > 0) {
            return $this->generateBookingReference();
        }

        return $reference;
    }
}
