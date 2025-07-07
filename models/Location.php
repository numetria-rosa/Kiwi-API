<?php
/**
 * Modèle pour la gestion des lieux (villes, pays)
 */
class Location {
    private $tequila;

    /**
     * Constructeur
     *
     * @param Tequila $tequila Instance de la classe Tequila
     */
    public function __construct($tequila) {
        $this->tequila = $tequila;
    }

    /**
     * Rechercher des lieux par terme
     *
     * @param string $term Terme de recherche
     * @param string $locale Langue (ex: fr, en)
     * @param string $locationTypes Types de lieux à inclure (airport,city,country)
     * @param int $limit Nombre maximal de résultats
     * @return array Résultats de la recherche
     */
    public function search($term, $locale = 'fr', $locationTypes = 'airport,city', $limit = 10) {
        try {
            // Utiliser l'API Tequila pour rechercher des lieux
            $params = [
                'term' => $term,
                'locale' => $locale,
                'location_types' => $locationTypes,
                'limit' => $limit
            ];

            $results = $this->tequila->searchLocations($term, $locale);

            // Formater les résultats pour notre application
            $locations = [];

            if (isset($results['locations']) && !empty($results['locations'])) {
                foreach ($results['locations'] as $location) {
                    $locations[] = [
                        'id' => $location['id'],
                        'code' => isset($location['code']) ? $location['code'] : '',
                        'name' => $location['name'],
                        'city' => isset($location['city']) ? $location['city']['name'] : '',
                        'country' => isset($location['country']) ? $location['country']['name'] : '',
                        'type' => $location['type'],
                        'latitude' => $location['location']['lat'],
                        'longitude' => $location['location']['lon']
                    ];
                }
            }

            return $locations;
        } catch (Exception $e) {
            error_log('Erreur lors de la recherche de lieux: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir des informations sur un lieu par son ID
     *
     * @param string $id ID du lieu
     * @param string $locale Langue (ex: fr, en)
     * @return array|null Informations sur le lieu ou null si non trouvé
     */
    public function getById($id, $locale = 'fr') {
        try {
            // Pour l'API Tequila, on utilise la même fonction de recherche mais avec l'ID comme terme
            $results = $this->search($id, $locale, 'airport,city,country', 1);

            if (!empty($results)) {
                return $results[0];
            }

            return null;
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération du lieu: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtenir des informations sur un lieu par son code IATA
     *
     * @param string $code Code IATA du lieu
     * @param string $locale Langue (ex: fr, en)
     * @return array|null Informations sur le lieu ou null si non trouvé
     */
    public function getByCode($code, $locale = 'fr') {
        try {
            // Même approche que getById mais avec le code comme terme
            $results = $this->search($code, $locale, 'airport,city', 1);

            // Filtrer pour s'assurer que le code correspond exactement
            foreach ($results as $location) {
                if ($location['code'] === $code) {
                    return $location;
                }
            }

            return null;
        } catch (Exception $e) {
            error_log('Erreur lors de la récupération du lieu par code: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtenir les villes populaires
     *
     * @param string $locale Langue (ex: fr, en)
     * @param int $limit Nombre de villes à retourner
     * @return array Liste des villes populaires
     */
    public function getPopularCities($locale = 'fr', $limit = 10) {
        // Liste statique des villes populaires (dans un cas réel, ces données pourraient venir d'une base de données)
        $popularCities = [
            'Paris', 'Londres', 'New York', 'Rome', 'Barcelone',
            'Amsterdam', 'Berlin', 'Madrid', 'Lisbonne', 'Prague',
            'Vienne', 'Athènes', 'Tokyo', 'Bangkok', 'Istanbul'
        ];

        $results = [];

        // Limiter au nombre demandé
        $citiesToFetch = array_slice($popularCities, 0, $limit);

        // Récupérer les informations pour chaque ville
        foreach ($citiesToFetch as $city) {
            $cityInfo = $this->search($city, $locale, 'city', 1);
            if (!empty($cityInfo)) {
                $results[] = $cityInfo[0];
            }
        }

        return $results;
    }

    /**
     * Obtenir les aéroports populaires
     *
     * @param string $locale Langue (ex: fr, en)
     * @param int $limit Nombre d'aéroports à retourner
     * @return array Liste des aéroports populaires
     */
    public function getPopularAirports($locale = 'fr', $limit = 10) {
        // Liste statique des codes d'aéroports populaires
        $popularAirports = [
            'CDG', 'LHR', 'JFK', 'FCO', 'BCN',
            'AMS', 'TXL', 'MAD', 'LIS', 'PRG',
            'VIE', 'ATH', 'HND', 'BKK', 'IST'
        ];

        $results = [];

        // Limiter au nombre demandé
        $airportsToFetch = array_slice($popularAirports, 0, $limit);

        // Récupérer les informations pour chaque aéroport
        foreach ($airportsToFetch as $airport) {
            $airportInfo = $this->getByCode($airport, $locale);
            if ($airportInfo) {
                $results[] = $airportInfo;
            }
        }

        return $results;
    }
}
