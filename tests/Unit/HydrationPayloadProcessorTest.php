<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Unit;

use EvolveORM\Bundle\Payload\HydrationPayloadProcessor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EvolveORM\Bundle\Payload\HydrationPayloadProcessor
 */
class HydrationPayloadProcessorTest extends TestCase
{
    public function cases(): array
    {
        $lookupValueMapInput = [
            'one_two' => 'one_two',
            'three_four_five' => 'three_four_five',
            'three_four_six' => 'three_four_six',
            'three_four_six_seven_eight' => 'three_four_six_seven_eight',
            'three_four_six_seven_nine' => 'three_four_six_seven_nine',
            'ten' => 'ten',
        ];
        $lookupValueMapOutput = [
            "one" => [
                "two" => "one_two"
            ],
            "three" => [
                "four" => [
                    "five" => "three_four_five",
                    "six" => [
                        0 => "three_four_six",
                        "seven" => [
                            "eight" => "three_four_six_seven_eight",
                            "nine" => "three_four_six_seven_nine",
                        ],
                    ],
                ],
            ],
            "ten" => "ten",
        ];
        $hydrationPayload = [
            'prop1' => 'value1',
            'prop2' => 'value2',
            'propObj' => [
                'prop1' => 'value1',
                'prop2' => 'value2',
            ],
            'collection1' => [
                ['prop1' => 'value1', 'prop2' => 'value2'],
                ['prop1' => 'value1', 'prop2' => 'value2'],
                ['prop1' => 'value1', 'prop2' => 'value2'],
            ],
            'collection2' => [
                [
                    'prop1' => 'value1',
                    'prop2' => 'value2',
                    'collection1' => [
                        ['prop1' => 'value1', 'prop2' => 'value2'],
                        ['prop1' => 'value1', 'prop2' => 'value2'],
                    ],
                ],
                [
                    'prop1' => 'value1',
                    'prop2' => 'value2',
                    'collection1' => [
                        ['prop1' => 'value1', 'prop2' => 'value2'],
                        ['prop1' => 'value1', 'prop2' => 'value2'],
                    ],
                ],
            ],
        ];

        return [
            'FromLookupValueMap' => [
                'input' => $lookupValueMapInput,
                'output' => $lookupValueMapOutput,
            ],
            'FromHydrationPayload' => [
                'input' => $hydrationPayload,
                'output' => $hydrationPayload,
            ],
            'FromMixedDataStructure' => [
                'input' => array_merge($lookupValueMapInput, $hydrationPayload),
                'output' => array_merge($lookupValueMapOutput, $hydrationPayload),
            ],
        ];
    }

    /**
     * @dataProvider cases
     */
    public function testFromLookupValueMap(array $input, array $output): void
    {
        $this->assertSame($output, HydrationPayloadProcessor::process($input));
    }
}
