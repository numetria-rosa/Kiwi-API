<?php
/**
 * Classe pour interagir avec l'API Tequila de Kiwi.com
 */
class Tequila {
    private $apiKey;
    private $apiUrl;

    /**
     * Constructeur
     */
    public function __construct() {
        $this->apiKey = TEQUILA_API_KEY;
        $this->apiUrl = TEQUILA_API_URL;
    }

    /**
     * Rechercher des vols
     *
     * @param array $params Paramètres de recherche
     * @return array Résultats de la recherche
     */
    public function searchFlights($params) {
        $endpoint = $this->apiUrl . TEQUILA_SEARCH_ENDPOINT;
        $defaults = [
            'v' => '3',
            'currency' => 'EUR',
            'locale' => 'fr',
            'limit' => 200
        ];

        $params = array_merge($defaults, $params);

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Rechercher des lieux (aéroports, villes)
     *
     * @param string $term Terme de recherche
     * @param string $locale Langue (ex: fr, en)
     * @return array Résultats de la recherche
     */
    public function searchLocations($term, $locale = 'fr') {
        $endpoint = $this->apiUrl . TEQUILA_LOCATIONS_ENDPOINT;
        $params = [
            'term' => $term,
            'locale' => $locale,
            'location_types' => 'airport,city',
            'limit' => 10
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Créer une réservation
     *
     * @param array $booking Données de la réservation
     * @return array Résultat de la création de réservation
     */
    public function createBooking($booking) {
        $endpoint = $this->apiUrl . TEQUILA_BOOKING_ENDPOINT;

        return $this->makeRequest($endpoint, $booking, 'POST');
    }

    /**
     * Effectuer une requête à l'API Tequila
     *
     * @param string $endpoint URL de l'endpoint
     * @param array $params Paramètres de la requête
     * @param string $method Méthode HTTP (GET ou POST)
     * @return array Réponse de l'API
     */
    private function makeRequest($endpoint, $params, $method = 'GET') {
        $ch = curl_init();

        $headers = [
            'apikey: ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_URL, $endpoint);
        } else {
            $query = http_build_query($params);
            curl_setopt($ch, CURLOPT_URL, $endpoint . '?' . $query);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new Exception('Erreur de requête : ' . $error);
        }

        if ($httpCode >= 400) {
            throw new Exception('Erreur API (' . $httpCode . '): ' . $response);
        }

        return json_decode($response, true);
    }

    /**
     * Formater les résultats de recherche de vols
     *
     * @param array $results Résultats bruts de l'API
     * @return array Résultats formatés
     */
    public function formatFlightResults($results) {
        $formatted = [];

        if (!isset($results['data']) || empty($results['data'])) {
            return $formatted;
        }

        foreach ($results['data'] as $flight) {
            $formatted[] = [
                'id' => $flight['id'],
                'price' => $flight['price'],
                'currency' => $results['currency'],
                'departure_airport' => $flight['flyFrom'],
                'departure_city' => $this->getCityName($flight['flyFrom'], $flight['route'][0]['cityFrom']),
                'arrival_airport' => $flight['flyTo'],
                'arrival_city' => $this->getCityName($flight['flyTo'], $flight['route'][count($flight['route']) - 1]['cityTo']),
                'departure_time' => date('H:i', $flight['dTime']),
                'arrival_time' => date('H:i', $flight['aTime']),
                'duration' => $this->formatDuration($flight['duration']['total']),
                'carrier' => $this->getCarrierName($flight['route'][0]['airline']),
                'carrier_code' => $flight['route'][0]['airline'],
                'stops' => count($flight['route']) - 1,
                'outbound_date' => date('Y-m-d', $flight['dTime']),
                'route' => $flight['route'],

                // Si c'est un vol aller-retour
                'return_flight' => isset($flight['return']) ? [
                    'departure_time' => isset($flight['returnDeparture']) ? date('H:i', $flight['returnDeparture']) : '',
                    'arrival_time' => isset($flight['returnArrival']) ? date('H:i', $flight['returnArrival']) : '',
                    'duration' => isset($flight['returnDuration']) ? $this->formatDuration($flight['returnDuration']) : '',
                    'carrier' => isset($flight['returnRoute'][0]['airline']) ? $this->getCarrierName($flight['returnRoute'][0]['airline']) : '',
                    'carrier_code' => isset($flight['returnRoute'][0]['airline']) ? $flight['returnRoute'][0]['airline'] : '',
                    'return_date' => isset($flight['returnDeparture']) ? date('Y-m-d', $flight['returnDeparture']) : ''
                ] : null
            ];
        }

        return $formatted;
    }

    /**
     * Formater une durée en minutes en format "HH:MM"
     *
     * @param int $minutes Durée en minutes
     * @return string Durée formatée
     */
    private function formatDuration($minutes) {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $mins);
    }

    /**
     * Obtenir le nom d'une ville à partir de son code d'aéroport
     *
     * @param string $airportCode Code IATA de l'aéroport
     * @param string $fallback Nom par défaut si le code n'est pas trouvé
     * @return string Nom de la ville
     */
    private function getCityName($airportCode, $fallback) {
        // Dans une implémentation réelle, nous pourrions interroger une base de données
        // ou l'API pour obtenir le nom de la ville. Pour cet exemple, nous utilisons
        // le nom fourni par défaut.
        return $fallback;
    }

    /**
     * Obtenir le nom d'une compagnie aérienne à partir de son code
     *
     * @param string $airlineCode Code IATA de la compagnie
     * @return string Nom de la compagnie
     */
    private function getCarrierName($airlineCode) {
        // Liste partielle des compagnies aériennes
        $airlines = [
            'FR' => 'Ryanair',
            'EZY' => 'EasyJet',
            'U2' => 'EasyJet',
            'AF' => 'Air France',
            'BA' => 'British Airways',
            'LH' => 'Lufthansa',
            'IB' => 'Iberia',
            'VY' => 'Vueling',
            'AZ' => 'Alitalia',
            'KL' => 'KLM'
        ];

        return isset($airlines[$airlineCode]) ? $airlines[$airlineCode] : $airlineCode;
    }
}
