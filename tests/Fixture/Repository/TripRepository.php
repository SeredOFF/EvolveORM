<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Repository;

use EvolveORM\Hydrator;
use EvolveORM\Tests\Fixture\Domain\Aggregate\Trip;
use PDO;
use ReflectionException;
use RuntimeException;

class TripRepository
{
    /** @var array<string, mixed> */
    private array $lookupValueMaps = [];

    public function __construct(
        private readonly PDO $pdo,
        private readonly Hydrator $hydrator,
    ) {
    }

    /** @return array<string, mixed> */
    public function getLookupValueMaps(): array
    {
        return $this->lookupValueMaps;
    }

    /**
     * @return Trip[]
     * @throws ReflectionException
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            <<<SQL
                SELECT 
                    trip.id AS id_value,
                    trip.date AS date,
                    
                    route.id AS route_id_value,
                    route.title AS route_title,
                    (
                        SELECT unixepoch(MAX(route_points.arrival_time)) - unixepoch(MIN(route_points.arrival_time))
                        FROM route_points
                        WHERE route_points.route_id = route.id
                    ) AS route_duration_seconds,
                    
                    bus.number AS bus_number,
                    bus_model.title AS bus_model_title,
                    bus_model.year AS bus_model_year,
                    
                    driver.id AS driver_id_value,
                    driver.first_name AS driver_name_firstName,
                    driver.last_name AS driver_name_lastName,
                    driver.second_name AS driver_name_secondName,
                    driver.phone AS driver_phone_value
                FROM trip
                    LEFT JOIN route ON trip.route_id = route.id
                    LEFT JOIN driver ON trip.driver_id = driver.id
                    LEFT JOIN bus ON trip.bus_id = bus.number
                    LEFT JOIN bus_model ON bus.model_id = bus_model.id
            SQL
        );
        if ($stmt === false) {
            throw new RuntimeException('Failed to prepare statement');
        }

        $trips = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), null, 'id_value');

        $points = $this->withPoints(
            ...array_unique(
                array_column($trips, 'route_id_value')
            )
        );

        $passengers = $this->withPassenger(...array_keys($trips));

        foreach ($trips as $id => $trip) {
            $trip['route_points'] = [];


            if (!isset($points[$trip['route_id_value']])) {
                continue;
            }

            $trips[$id]['route_points'] = $points[$trip['route_id_value']];
            $trips[$id]['passengers'] = $passengers[$id] ?? [];
        }

        $this->lookupValueMaps = $trips;

        return $this->hydrator->hydrateAll(Trip::class, $trips);
    }

    /** @return array<int|string, array<int, mixed>> */
    private function withPoints(string ...$routeIds): array
    {
        $placeholders = implode(',', array_fill(0, count($routeIds), '?'));
        $stmt = $this->pdo->prepare(
            <<<SQL
                SELECT 
                    route_points.route_id AS route_id_value,
                    point.title,
                    point.latitude,
                    point.longitude,
                    point.latitude,
                    route_points.arrival_time AS arrivalTime
                FROM route_points 
                    LEFT JOIN point ON route_points.to_point_id = point.id
                WHERE route_points.route_id IN ($placeholders)
                ORDER BY route_points.arrival_time
            SQL,
        );
        if ($stmt === false) {
            throw new RuntimeException('Failed to prepare statement');
        }
        $result = $stmt->execute($routeIds);
        if ($result === false) {
            throw new RuntimeException('Failed to execute statement');
        }

        $routePoints = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $routePoint) {
            $routeId = $routePoint['route_id_value'];
            unset($routePoint['route_id_value']);
            $routePoints[$routeId][] = $routePoint;
        }

        return $routePoints;
    }

    /** @return array<int|string, array<int, mixed>> */
    private function withPassenger(string ...$tripIds): array
    {
        $placeholders = implode(',', array_fill(0, count($tripIds), '?'));
        $stmt = $this->pdo->prepare(
            <<<SQL
                SELECT 
                    trip_passengers.trip_id,
                    passenger.id AS id_value,
                    passenger.seat_number AS seat_number,
                    ticket.price_cents AS ticket_price_cents,
                    ticket.date AS ticket_dateTime
                FROM trip_passengers 
                    LEFT JOIN passenger ON trip_passengers.passenger_id = passenger.id
                    LEFT JOIN ticket ON passenger.ticket_id = ticket.id
                WHERE trip_passengers.trip_id IN ($placeholders)
            SQL,
        );
        if ($stmt === false) {
            throw new RuntimeException('Failed to prepare statement');
        }
        $result = $stmt->execute($tripIds);
        if ($result === false) {
            throw new RuntimeException('Failed to execute statement');
        }

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $passengerIds = array_values(
            array_unique(
                array_column($data, 'id_value')
            )
        );

        $placeholders = implode(',', array_fill(0, count($passengerIds), '?'));
        $stmt = $this->pdo->prepare(
            <<<SQL
                SELECT 
                    luggage.passenger_id,
                    luggage.id AS id_value,
                    luggage.type AS type,
                    luggage.height AS overallDimensions_height,
                    luggage.width AS overallDimensions_width,
                    luggage.length AS overallDimensions_length,
                    luggage.weight AS overallDimensions_weight
                FROM luggage 
                WHERE luggage.passenger_id IN ($placeholders)
            SQL,
        );
        if ($stmt === false) {
            throw new RuntimeException('Failed to prepare statement');
        }
        $result = $stmt->execute($passengerIds);
        if ($result === false) {
            throw new RuntimeException('Failed to execute statement');
        }

        $luggage = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $passengerId = $row['passenger_id'];
            unset($row['passenger_id']);
            $luggage[$passengerId][] = $row;
        }

        $passengers = [];
        foreach ($data as $row) {
            $tripId = $row['trip_id'];
            unset($row['trip_id']);
            $row['luggage'] = $luggage[$row['id_value']] ?? [];
            $passengers[$tripId][] = $row;
        }

        return $passengers;
    }
}
