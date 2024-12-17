<?php

namespace EvolveORM\Tests\Integration;

use EvolveORM\Bundle\Cache\WeakRefReflectionCache;
use EvolveORM\Bundle\Exception\HydrationException;
use EvolveORM\Hydrator;
use EvolveORM\Tests\Fixture\Domain\Aggregate\Trip;
use EvolveORM\Tests\Fixture\Domain\Entity\Driver;
use EvolveORM\Tests\Fixture\Domain\Entity\Passenger;
use EvolveORM\Tests\Fixture\Domain\Entity\Route;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Point;
use EvolveORM\Tests\Fixture\Orm\ToLuggageCollectionMapper;
use EvolveORM\Tests\Fixture\Orm\ToNullablePhoneMapper;
use EvolveORM\Tests\Fixture\Orm\ToNullableTicketMapper;
use EvolveORM\Tests\Fixture\Orm\ToPassengerCollectionMapper;
use EvolveORM\Tests\Fixture\Orm\ToPointsCollectionMapper;
use EvolveORM\Tests\Fixture\Repository\TripRepository;
use ReflectionException;

class TripRepositoryTest extends DatabaseTestCase
{
    /** @throws HydrationException */
    public function cases(): array
    {
        $cache = new WeakRefReflectionCache();

        return [
            'WithAttributes' => [
                'hydrator' => new Hydrator($cache),
            ],
            'WithConfig' => [
                'hydrator' => new Hydrator(
                    $cache,
                    [
                        Driver::class => [
                            'phone' => ToNullablePhoneMapper::class,
                        ],
                        Passenger::class => [
                            'luggage' => ToLuggageCollectionMapper::class,
                            'ticket' => ToNullableTicketMapper::class,
                        ],
                        Route::class => [
                            'points' => ToPointsCollectionMapper::class,
                        ],
                        Trip::class => [
                            'passengers' => ToPassengerCollectionMapper::class,
                        ],
                    ],
                ),
            ],
        ];
    }

    /**
     * @dataProvider cases
     * @throws ReflectionException
     */
    public function testFindAll(Hydrator $hydrator): void
    {
        $repo = new TripRepository(self::$pdo, $hydrator);

        $trips = $repo->findAll();
        $this->assertCount(count($repo->getLookupValueMaps()), $trips);

        foreach ($trips as $trip) {
            $this->assertInstanceOf(Trip::class, $trip);

            $lookupValueMap = $repo->getLookupValueMaps()[$trip->id->value] ?? null;
            $this->assertIsArray($lookupValueMap);

            $this->assertEquals($lookupValueMap['id_value'] ?? null, $trip->id->value);
            $this->assertEquals($lookupValueMap['date'] ?? null, $trip->date->format('Y-m-d H:i:s'));

            $this->assertEquals($lookupValueMap['route_id_value'] ?? null, $trip->route->id->value);
            $this->assertEquals($lookupValueMap['route_title'] ?? null, $trip->route->title);
            $this->assertEquals($lookupValueMap['route_duration_seconds'] ?? null, $trip->route->duration->seconds);

            $this->assertIsArray($trip->route->points);
            $this->assertIsArray($lookupValueMap['route_points'] ?? null);
            $this->assertCount(count($lookupValueMap['route_points']), $trip->route->points);
            foreach ($trip->route->points as $i => $point) {
                $this->assertInstanceOf(Point::class, $point);
                $this->assertEquals($lookupValueMap['route_points'][$i]['title'] ?? null, $point->title);
                $this->assertEquals($lookupValueMap['route_points'][$i]['latitude'] ?? null, $point->latitude);
                $this->assertEquals($lookupValueMap['route_points'][$i]['longitude'] ?? null, $point->longitude);
                $this->assertEquals(
                    $lookupValueMap['route_points'][$i]['arrivalTime'] ?? null,
                    $point->arrivalTime->format('Y-m-d H:i:s'),
                );
            }

            $this->assertEquals($lookupValueMap['bus_number'] ?? null, $trip->bus->number);
            $this->assertEquals($lookupValueMap['bus_model_title'] ?? null, $trip->bus->model->title);
            $this->assertEquals($lookupValueMap['bus_model_year'] ?? null, $trip->bus->model->year);

            $this->assertEquals($lookupValueMap['driver_id_value'] ?? null, $trip->driver->id->value);
            $this->assertEquals($lookupValueMap['driver_name_firstName'] ?? null, $trip->driver->name->firstName);
            $this->assertEquals($lookupValueMap['driver_name_lastName'] ?? null, $trip->driver->name->lastName);
            $this->assertEquals($lookupValueMap['driver_name_secondName'] ?? null, $trip->driver->name->secondName);
            $trip->driver->phone === null
                ? $this->assertEquals($lookupValueMap['driver_phone_value'] ?? null, $trip->driver->phone)
                : $this->assertEquals($lookupValueMap['driver_phone_value'], $trip->driver->phone->value);

            $this->assertIsArray($trip->passengers);
            $this->assertIsArray($lookupValueMap['passengers'] ?? null);
            $this->assertCount(count($lookupValueMap['passengers']), $trip->passengers);
            foreach ($trip->passengers as $i => $passenger) {
                $this->assertInstanceOf(Passenger::class, $passenger);
                $this->assertEquals($lookupValueMap['passengers'][$i]['id_value'] ?? null, $passenger->id->value);
                $this->assertEquals($lookupValueMap['passengers'][$i]['seat_number'] ?? null, $passenger->seat->number);

                $this->assertIsArray($passenger->luggage);
                $this->assertIsArray($lookupValueMap['passengers'][$i]['luggage'] ?? null);
                $this->assertCount(count($lookupValueMap['passengers'][$i]['luggage']), $passenger->luggage);

                foreach ($passenger->luggage as $ii => $luggage) {
                    $this->assertEquals(
                        $lookupValueMap['passengers'][$i]['luggage'][$ii]['id_value'] ?? null,
                        $luggage->id->value,
                    );
                    $this->assertEquals(
                        $lookupValueMap['passengers'][$i]['luggage'][$ii]['type'] ?? null,
                        $luggage->type->value,
                    );
                    $this->assertEquals(
                        $lookupValueMap['passengers'][$i]['luggage'][$ii]['overallDimensions_height'] ?? null,
                        $luggage->overallDimensions->height,
                    );
                    $this->assertEquals(
                        $lookupValueMap['passengers'][$i]['luggage'][$ii]['overallDimensions_width'] ?? null,
                        $luggage->overallDimensions->width,
                    );
                    $this->assertEquals(
                        $lookupValueMap['passengers'][$i]['luggage'][$ii]['overallDimensions_length'] ?? null,
                        $luggage->overallDimensions->length,
                    );
                    $this->assertEquals(
                        $lookupValueMap['passengers'][$i]['luggage'][$ii]['overallDimensions_weight'] ?? null,
                        $luggage->overallDimensions->weight,
                    );
                }

                if ($passenger->ticket === null) {
                    $this->assertEquals(
                        $lookupValueMap['passengers'][$i]['ticket_price_cents'] ?? null,
                        $passenger->ticket
                    );
                    continue;
                }

                $this->assertEquals(
                    $lookupValueMap['passengers'][$i]['ticket_price_cents'] ?? null,
                    $passenger->ticket->price->cents,
                );
                $this->assertEquals(
                    $lookupValueMap['passengers'][$i]['ticket_dateTime'] ?? null,
                    $passenger->ticket->dateTime->format('Y-m-d H:i:s'),
                );
            }
        }
    }
}
