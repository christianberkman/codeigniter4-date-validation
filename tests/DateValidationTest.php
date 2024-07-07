<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter-DEA-Rule.
 *
 * (c) 2023 Datamweb <pooya_parsa_dadashi@yahoo.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests;

use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Validation\Validation;
use Config\Services;
use ChristianBerkman\DateValidation\DateValidation;
use \DateTime;

/**
 * @internal
 */
final class DateValidationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    private Validation $validation;
    private array $config = [
        'ruleSets' => [
            DateValidation::class,
        ],
    ];
    
    private array $dates = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->validation = $this->validation = new Validation((object) $this->config, Services::renderer());
        $this->validation->reset();

        $this->dates = static::provideDates();
    }

    /**
     * Generate dates for yesterday, today and tomorrow
     * and pre-defined dates formatted as Y-m-d
     */
    public static function provideDates(): array
    {
        $today = new DateTime('today');
        $yesterday = new DateTime('yesterday');
        $tomorrow = new DateTime('tomorrow');

        return [
            'yesterday' => $yesterday->format('Y-m-d'),
            'today' => $today->format('Y-m-d'),
            'tomorrow' => $tomorrow->format('Y-m-d'),
            'arrival_date' => '2024-07-01',
            'excursion_date' => '2024-07-03',
            'depatrue_date' => '2024-07-10',
            'last_available_date' => '2027-08-09',
        ];
    }

    public function testCreateDate(): void
    {
        $this->assertInstanceOf('DateTime', DateValidation::createDate('2024-03-11', 'Y-m-d'));
    }

    public function testToday(): void
    {
        $this->assertInstanceOf('DateTime', DateValidation::today());
    }

    public function testSplitParams(): void
    {
        $params = 'fieldName,formatString';
        $data = ['fieldName' => 'fieldValue'];
        
        DateValidation::splitParams($params, $data, $field, $fieldValue, $format);

        $this->assertSame('fieldName', $field);
        $this->assertSame('fieldValue', $fieldValue);
        $this->assertSame('formatString', $format);
    }

    public function testDateBeforeToday(): void
    {        
        $rules = 'date_before_today[Y-m-d]';
        $data = static::provideDates();
        
        $this->validation->setRules(['yesterday' => $rules]);
        $this->assertTrue($this->validation->run($data), 'yesterday');

        $this->validation->reset();

        $this->validation->setRules(['today' => $rules]);
        $this->assertFalse($this->validation->run($data), 'today');

        $this->validation->reset();

        $this->validation->setRules(['tomorrow' => $rules]);
        $this->assertFalse($this->validation->run($data), 'tomorrow');
    }

    public function testDateEndingToday(): void
    {        
        $rules = 'date_ending_today[Y-m-d]';
        $data = static::provideDates();
        
        $this->validation->setRules(['yesterday' => $rules]);
        $this->assertTrue($this->validation->run($data), 'yesterday');

        $this->validation->reset();

        $this->validation->setRules(['today' => $rules]);
        $this->assertTrue($this->validation->run($data), 'today');

        $this->validation->reset();

        $this->validation->setRules(['tomorrow' => $rules]);
        $this->assertFalse($this->validation->run($data), 'tomorrow');
    }

    public function testDateStartingToday(): void
    {        
        $rules = 'date_starting_today[Y-m-d]';
        $data = static::provideDates();
        
        $this->validation->setRules(['yesterday' => $rules]);
        $this->assertFalse($this->validation->run($data), 'yesterday');

        $this->validation->reset();

        $this->validation->setRules(['today' => $rules]);
        $this->assertTrue($this->validation->run($data), 'today');

        $this->validation->reset();

        $this->validation->setRules(['tomorrow' => $rules]);
        $this->assertTrue($this->validation->run($data), 'tomorrow');
    }

    public function testDateAfterToday(): void
    {        
        $rules = 'date_after_today[Y-m-d]';
        $data = static::provideDates();
        
        $this->validation->setRules(['yesterday' => $rules]);
        $this->assertFalse($this->validation->run($data), 'yesterday');

        $this->validation->reset();

        $this->validation->setRules(['today' => $rules]);
        $this->assertFalse($this->validation->run($data), 'today');

        $this->validation->reset();

        $this->validation->setRules(['tomorrow' => $rules]);
        $this->assertTrue($this->validation->run($data), 'tomorrow');
    }
}