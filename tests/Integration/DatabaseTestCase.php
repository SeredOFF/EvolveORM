<?php

/** @noinspection PhpSameParameterValueInspection */

declare(strict_types=1);

namespace EvolveORM\Tests\Integration;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use PDO;
use Ramsey\Uuid\Uuid;

abstract class DatabaseTestCase extends TestCase
{
    protected static PDO $pdo;
    private static Generator $faker;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO('sqlite::memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->exec('PRAGMA journal_mode=WAL;');
        self::$pdo->exec('PRAGMA synchronous=OFF;');
        self::$faker = Factory::create();

        self::prepareRoutes();
        $driverIds = self::prepareDrivers(5);
        $busIds = self::prepareBuses(8, 10);
        $passengerIds = self::preparePassengers(11);
        self::prepareTrips(10, $driverIds, $busIds, $passengerIds);
    }

    /** Маршруты детерминированы, чтобы общая длительность каждого из них была константной */
    private static function prepareRoutes(): void
    {
        self::$pdo->exec(
            <<<SQL
                CREATE TABLE route
                (
                    id        TEXT PRIMARY KEY,
                    title     TEXT    NOT NULL
                );
            SQL
        );
        $routes = [
            ['id' => 'route1', 'title' => 'route 1'],
            ['id' => 'route2', 'title' => 'route 2'],
            ['id' => 'route3', 'title' => 'route 3'],
            ['id' => 'route4', 'title' => 'route 4'],
            ['id' => 'route5', 'title' => 'route 5'],
        ];
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO route (id, title) 
                VALUES(:id, :title);
            SQL
        );
        foreach ($routes as $route) {
            $sth->execute(
                [
                    'id' => $route['id'],
                    'title' => $route['title'],
                ]
            );
        }

        self::$pdo->exec(
            <<<SQL
                CREATE TABLE point
                (
                    id        TEXT PRIMARY KEY,
                    title     TEXT NOT NULL,
                    latitude  REAL NOT NULL,
                    longitude REAL NOT NULL
                );
            SQL
        );
        $points = [
            ['id' => 'point1', 'title' => 'point 1', 'latitude' => 1.0, 'longitude' => 1.0],
            ['id' => 'point2', 'title' => 'point 2', 'latitude' => 2.0, 'longitude' => 2.0],
            ['id' => 'point3', 'title' => 'point 3', 'latitude' => 3.0, 'longitude' => 3.0],
            ['id' => 'point4', 'title' => 'point 4', 'latitude' => 4.0, 'longitude' => 4.0],
            ['id' => 'point5', 'title' => 'point 5', 'latitude' => 5.0, 'longitude' => 5.0],
            ['id' => 'point6', 'title' => 'point 6', 'latitude' => 6.0, 'longitude' => 6.0],
            ['id' => 'point7', 'title' => 'point 7', 'latitude' => 7.0, 'longitude' => 7.0],
            ['id' => 'point8', 'title' => 'point 8', 'latitude' => 8.0, 'longitude' => 8.0],
            ['id' => 'point9', 'title' => 'point 9', 'latitude' => 9.0, 'longitude' => 9.0],
            ['id' => 'point10', 'title' => 'point 10', 'latitude' => 10.0, 'longitude' => 10.0],
        ];
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO point (id, title, latitude, longitude)
                VALUES(:id, :title, :latitude, :longitude);
            SQL
        );
        foreach ($points as $point) {
            $sth->execute(
                [
                    'id' => $point['id'],
                    'title' => $point['title'],
                    'latitude' => $point['latitude'],
                    'longitude' => $point['longitude'],
                ]
            );
        }

        self::$pdo->exec(
            <<<SQL
                CREATE TABLE route_points
                (
                    route_id      TEXT    NOT NULL,
                    from_point_id TEXT,
                    to_point_id   TEXT    NOT NULL,
                    arrival_time  DATETIME NOT NULL,
                    PRIMARY KEY (route_id, from_point_id, to_point_id),
                    FOREIGN KEY (route_id) REFERENCES route (id),
                    FOREIGN KEY (from_point_id) REFERENCES point (id),
                    FOREIGN KEY (to_point_id) REFERENCES point (id)
                );
            SQL
        );
        // считаем, что выезд с 1ой точки маршрута в 8:00
        $routePoints = [
            [
                'route_id' => 'route1',
                'from_point_id' => null,
                'to_point_id' => 'point1',
                'arrival_time' => '1970-01-01 08:00:00'
            ],
            [
                'route_id' => 'route1',
                'from_point_id' => 'point1',
                'to_point_id' => 'point2',
                'arrival_time' => '1970-01-01 08:20:00'
            ],
            [
                'route_id' => 'route1',
                'from_point_id' => 'point2',
                'to_point_id' => 'point3',
                'arrival_time' => '1970-01-01 09:10:00'
            ],
            [
                'route_id' => 'route1',
                'from_point_id' => 'point3',
                'to_point_id' => 'point4',
                'arrival_time' => '1970-01-01 09:40:00'
            ],
            [
                'route_id' => 'route1',
                'from_point_id' => 'point4',
                'to_point_id' => 'point5',
                'arrival_time' => '1970-01-01 10:00:00'
            ],
            // итого 1ый маршрут 2ч
            [
                'route_id' => 'route2',
                'from_point_id' => null,
                'to_point_id' => 'point6',
                'arrival_time' => '1970-01-01 08:00:00'
            ],
            [
                'route_id' => 'route2',
                'from_point_id' => 'point6',
                'to_point_id' => 'point7',
                'arrival_time' => '1970-01-01 09:25:00'
            ],
            [
                'route_id' => 'route2',
                'from_point_id' => 'point7',
                'to_point_id' => 'point8',
                'arrival_time' => '1970-01-01 09:40:00'
            ],
            [
                'route_id' => 'route2',
                'from_point_id' => 'point8',
                'to_point_id' => 'point9',
                'arrival_time' => '1970-01-01 10:10:00'
            ],
            [
                'route_id' => 'route2',
                'from_point_id' => 'point9',
                'to_point_id' => 'point10',
                'arrival_time' => '1970-01-01 10:35:00'
            ],
            // итого 2ой маршрут 2ч35мин
            [
                'route_id' => 'route3',
                'from_point_id' => null,
                'to_point_id' => 'point1',
                'arrival_time' => '1970-01-01 08:00:00'
            ],
            [
                'route_id' => 'route3',
                'from_point_id' => 'point1',
                'to_point_id' => 'point3',
                'arrival_time' => '1970-01-01 08:35:00'
            ],
            [
                'route_id' => 'route3',
                'from_point_id' => 'point3',
                'to_point_id' => 'point5',
                'arrival_time' => '1970-01-01 09:40:00'
            ],
            [
                'route_id' => 'route3',
                'from_point_id' => 'point5',
                'to_point_id' => 'point7',
                'arrival_time' => '1970-01-01 10:15:00'
            ],
            [
                'route_id' => 'route3',
                'from_point_id' => 'point7',
                'to_point_id' => 'point9',
                'arrival_time' => '1970-01-01 10:45:00'
            ],
            // итого 3ий маршрут 2ч45мин
            [
                'route_id' => 'route4',
                'from_point_id' => null,
                'to_point_id' => 'point2',
                'arrival_time' => '1970-01-01 08:00:00'
            ],
            [
                'route_id' => 'route4',
                'from_point_id' => 'point2',
                'to_point_id' => 'point4',
                'arrival_time' => '1970-01-01 08:40:00'
            ],
            [
                'route_id' => 'route4',
                'from_point_id' => 'point4',
                'to_point_id' => 'point6',
                'arrival_time' => '1970-01-01 09:40:00'
            ],
            [
                'route_id' => 'route4',
                'from_point_id' => 'point6',
                'to_point_id' => 'point8',
                'arrival_time' => '1970-01-01 10:20:00'
            ],
            [
                'route_id' => 'route4',
                'from_point_id' => 'point8',
                'to_point_id' => 'point10',
                'arrival_time' => '1970-01-01 11:00:00'
            ],
            // итого 4ый маршрут 3ч
            [
                'route_id' => 'route5',
                'from_point_id' => null,
                'to_point_id' => 'point1',
                'arrival_time' => '1970-01-01 08:00:00'
            ],
            [
                'route_id' => 'route5',
                'from_point_id' => 'point1',
                'to_point_id' => 'point2',
                'arrival_time' => '1970-01-01 08:20:00'
            ],
            [
                'route_id' => 'route5',
                'from_point_id' => 'point2',
                'to_point_id' => 'point3',
                'arrival_time' => '1970-01-01 09:10:00'
            ],
            [
                'route_id' => 'route5',
                'from_point_id' => 'point3',
                'to_point_id' => 'point4',
                'arrival_time' => '1970-01-01 09:40:00'
            ],
            [
                'route_id' => 'route5',
                'from_point_id' => 'point4',
                'to_point_id' => 'point5',
                'arrival_time' => '1970-01-01 10:00:00'
            ],
            [
                'route_id' => 'route5',
                'from_point_id' => 'point5',
                'to_point_id' => 'point6',
                'arrival_time' => '1970-01-01 10:30:00'
            ],
            [
                'route_id' => 'route5',
                'from_point_id' => 'point6',
                'to_point_id' => 'point7',
                'arrival_time' => '1970-01-01 11:25:00'
            ],
            [
                'route_id' => 'route5',
                'from_point_id' => 'point7',
                'to_point_id' => 'point8',
                'arrival_time' => '1970-01-01 11:40:00'
            ],
            [
                'route_id' => 'route5',
                'from_point_id' => 'point8',
                'to_point_id' => 'point9',
                'arrival_time' => '1970-01-01 12:10:00'
            ],
            [
                'route_id' => 'route5',
                'from_point_id' => 'point9',
                'to_point_id' => 'point10',
                'arrival_time' => '1970-01-01 12:35:00'
            ],
            // итого 5ый маршрут 4ч35мин
        ];
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO route_points (route_id, from_point_id, to_point_id, arrival_time) 
                VALUES(:route_id, :from_point_id, :to_point_id, :arrival_time);
            SQL
        );
        foreach ($routePoints as $routePoint) {
            $sth->execute(
                [
                    'route_id' => $routePoint['route_id'],
                    'from_point_id' => $routePoint['from_point_id'],
                    'to_point_id' => $routePoint['to_point_id'],
                    'arrival_time' => $routePoint['arrival_time'],
                ]
            );
        }
    }

    /** @return string[] */
    private static function prepareDrivers(int $count): array
    {
        self::$pdo->exec(
            <<<SQL
                CREATE TABLE driver
                (
                    id          TEXT PRIMARY KEY,
                    first_name  TEXT NOT NULL,
                    last_name   TEXT NOT NULL,
                    second_name TEXT,
                    phone       TEXT
                );
            SQL
        );
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO driver (id, first_name, last_name, second_name, phone) 
                VALUES(:id, :first_name, :last_name, :second_name, :phone);
            SQL
        );
        $ids = [];
        for ($i = 1; $i <= $count; $i++) {
            $id = Uuid::uuid4()->toString();
            $ids[] = $id;

            $sth->execute(
                [
                    'id' => $id,
                    'first_name' => self::$faker->firstName(),
                    'last_name' => self::$faker->lastName(),
                    'second_name' => self::$faker->boolean(60) ? self::$faker->lastName() : null,
                    'phone' => self::$faker->boolean(60) ? self::$faker->phoneNumber() : null,
                ]
            );
        }

        return $ids;
    }

    /** @return string[] */
    private static function prepareBuses(int $modelCnt, int $busCnt): array
    {
        self::$pdo->exec(
            <<<SQL
                CREATE TABLE bus_model
                (
                    id    TEXT PRIMARY KEY,
                    title TEXT    NOT NULL,
                    year  INTEGER NOT NULL
                );
            SQL
        );
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO bus_model (id, title, year) 
                VALUES(:id, :title, :year);
            SQL
        );
        $modelIds = [];
        for ($i = 1; $i <= $modelCnt; $i++) {
            $id = Uuid::uuid4()->toString();
            $modelIds[] = $id;

            $sth->execute(
                [
                    'id' => $id,
                    'title' => self::$faker->word(),
                    'year' => (int)self::$faker->year(),
                ]
            );
        }

        self::$pdo->exec(
            <<<SQL
                CREATE TABLE bus
                (
                    number   INTEGER PRIMARY KEY,
                    model_id TEXT NOT NULL,
                    FOREIGN KEY (model_id) REFERENCES bus_model (id)
                );
            SQL
        );
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO bus (number, model_id) 
                VALUES(:number, :model_id);
            SQL
        );
        $busIds = [];
        for ($i = 1; $i <= $busCnt; $i++) {
            $id = self::$faker->ean8();
            $busIds[] = $id;

            $sth->execute(
                [
                    'number' => $id,
                    'model_id' => $modelIds[self::$faker->numberBetween(0, $modelCnt - 1)],
                ]
            );
        }

        return $busIds;
    }

    /** @return string[] */
    private static function preparePassengers(int $count): array
    {
        self::$pdo->exec(
            <<<SQL
                CREATE TABLE ticket
                (
                    id          TEXT PRIMARY KEY,
                    price_cents INTEGER  NOT NULL,
                    date        DATETIME NOT NULL
                );
            SQL
        );
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO ticket (id, price_cents, date) 
                VALUES(:id, :price_cents, :date);
            SQL
        );
        $ticketIds = [];
        // два безбилетника
        for ($i = 1; $i <= ($count - 2); $i++) {
            $id = Uuid::uuid4()->toString();
            $ticketIds[$i] = $id;

            $sth->execute(
                [
                    'id' => $id,
                    'price_cents' => self::$faker->randomNumber(),
                    'date' => self::$faker->date('Y-m-d H:i:s'),
                ]
            );
        }

        self::$pdo->exec(
            <<<SQL
                CREATE TABLE passenger
                (
                    id          TEXT PRIMARY KEY,
                    ticket_id   TEXT,
                    seat_number INTEGER NOT NULL,
                    FOREIGN KEY (ticket_id) REFERENCES ticket (id)
                );
            SQL
        );
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO passenger (id, ticket_id, seat_number) 
                VALUES(:id, :ticket_id, :seat_number);
            SQL
        );
        $passengerIds = [];
        for ($i = 1; $i <= $count; $i++) {
            $id = Uuid::uuid4()->toString();
            $passengerIds[] = $id;

            $sth->execute(
                [
                    'id' => $id,
                    'ticket_id' => $ticketIds[$i] ?? null,
                    'seat_number' => self::$faker->randomNumber(),
                ]
            );
        }

        self::$pdo->exec(
            <<<SQL
                CREATE TABLE luggage
                (
                    id           TEXT PRIMARY KEY,
                    passenger_id TEXT NOT NULL,
                    type         TEXT NOT NULL,
                    height       REAL NOT NULL,
                    width        REAL NOT NULL,
                    length       REAL NOT NULL,
                    weight       REAL NOT NULL,
                    FOREIGN KEY (passenger_id) REFERENCES passenger (id)
                );
            SQL
        );
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO luggage (id, passenger_id, type, height, width, length, weight) 
                VALUES(:id, :passenger_id, :type, :height, :width, :length, :weight);
            SQL
        );
        for ($i = 1; $i <= (int)($count * 1.5); $i++) {
            $sth->execute(
                [
                    'id' => Uuid::uuid4()->toString(),
                    'passenger_id' => $passengerIds[self::$faker->numberBetween(0, $count - 1)],
                    'type' => self::$faker->randomElement(['large', 'medium', 'small']),
                    'height' => self::$faker->randomFloat(),
                    'width' => self::$faker->randomFloat(),
                    'length' => self::$faker->randomFloat(),
                    'weight' => self::$faker->randomFloat(),
                ]
            );
        }

        return $passengerIds;
    }

    /**
     * @param int $count
     * @param string[] $driverIds
     * @param string[] $busIds
     * @param string[] $passengerIds
     */
    private static function prepareTrips(int $count, array $driverIds, array $busIds, array $passengerIds): void
    {
        $routeIds = ['route1', 'route2', 'route3', 'route4', 'route5'];

        self::$pdo->exec(
            <<<SQL
                CREATE TABLE trip
                (
                    id        TEXT PRIMARY KEY,
                    date      DATETIME NOT NULL,
                    route_id  TEXT     NOT NULL,
                    bus_id    INTEGER  NOT NULL,
                    driver_id TEXT     NOT NULL,
                    FOREIGN KEY (route_id) REFERENCES route (id),
                    FOREIGN KEY (bus_id) REFERENCES bus (number),
                    FOREIGN KEY (driver_id) REFERENCES driver (id)
                );
            SQL
        );
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO trip (id, date, route_id, bus_id, driver_id) 
                VALUES(:id, :date, :route_id, :bus_id, :driver_id);
            SQL
        );
        $tripIds = [];
        for ($i = 1; $i <= $count; $i++) {
            $id = Uuid::uuid4()->toString();
            $tripIds[$i] = $id;
            $sth->execute(
                [
                    'id' => $id,
                    'date' => self::$faker->date('Y-m-d H:i:s'),
                    'route_id' => $routeIds[self::$faker->numberBetween(0, (count($routeIds) - 1))],
                    'bus_id' => $busIds[self::$faker->numberBetween(0, (count($busIds) - 1))],
                    'driver_id' => $driverIds[self::$faker->numberBetween(0, (count($driverIds) - 1))],
                ]
            );
        }

        self::$pdo->exec(
            <<<SQL
                CREATE TABLE trip_passengers
                (
                    trip_id      TEXT NOT NULL,
                    passenger_id TEXT NOT NULL,
                    PRIMARY KEY (trip_id, passenger_id),
                    FOREIGN KEY (trip_id) REFERENCES trip (id),
                    FOREIGN KEY (passenger_id) REFERENCES passenger (id)
                );
            SQL
        );
        $sth = self::$pdo->prepare(
            <<<SQL
                INSERT INTO trip_passengers (trip_id, passenger_id) 
                VALUES(:trip_id, :passenger_id);
            SQL
        );
        for ($i = 1; $i <= $count; $i++) {
            $passengersCnt = self::$faker->numberBetween(0, count($passengerIds) - 1);

            for ($ii = 0; $ii <= $passengersCnt; $ii++) {
                $sth->execute(
                    [
                        'trip_id' => $tripIds[$i],
                        'passenger_id' => $passengerIds[$ii],
                    ]
                );
            }
        }
    }
}
