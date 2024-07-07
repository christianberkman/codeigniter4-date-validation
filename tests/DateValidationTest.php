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
use InvalidArgumentException;
use Psalm\Issue\InvalidArgument;

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
            'past_date' => '1989-03-11', // Saturday (6)
            'arrival_date' => '2024-07-01',
            'excursion_date' => '2024-07-03',
            'depatrue_date' => '2024-07-10',
            'last_available_date' => '2027-08-09',
            'far_future_date' => '2100-01-01', // Friday (5)
        ];
    }

    public function testCreateDate(): void
    {
        $this->assertInstanceOf('DateTime', DateValidation::createDate('2024-03-11', 'Y-m-d'), 'createDate');
    }

    public function testToday(): void
    {
        $this->assertInstanceOf('DateTime', DateValidation::today(), 'today');
    }

    public function testSplitParams(): void
    {
        $params = 'fieldName,formatString';
        $this->dates = ['fieldName' => 'fieldValue'];
        
        DateValidation::splitParams($params, $this->dates, $field, $fieldValue, $format);

        $this->assertSame('fieldName', $field, 'splitParams.fieldName');
        $this->assertSame('fieldValue', $fieldValue, 'splitParams.fieldValue');
        $this->assertSame('formatString', $format, 'splitParams.formatString');
    }

    public function testDateBeforeToday(): void
    {        
        $rules = 'date_before_today[Y-m-d]';
                
        $this->validation->setRules(['yesterday' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_before_today.yesterday');

        $this->validation->reset();

        $this->validation->setRules(['today' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_before_today.today');

        $this->validation->reset();

        $this->validation->setRules(['tomorrow' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_before_today.tomorrow');
    }

    public function testDateEndingToday(): void
    {        
        $rules = 'date_ending_today[Y-m-d]';
                
        $this->validation->setRules(['yesterday' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_ending_today.yesterday');

        $this->validation->reset();

        $this->validation->setRules(['today' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_ending_today.today');

        $this->validation->reset();

        $this->validation->setRules(['tomorrow' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_ending_today.tomorrow');
    }

    public function testDateStartingToday(): void
    {        
        $rules = 'date_starting_today[Y-m-d]';
                
        $this->validation->setRules(['yesterday' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_starting_today.yesterday');

        $this->validation->reset();

        $this->validation->setRules(['today' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_starting_today.today');

        $this->validation->reset();

        $this->validation->setRules(['tomorrow' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_starting_today.tomorrow');
    }

    public function testDateAfterToday(): void
    {        
        $rules = 'date_after_today[Y-m-d]';
                
        $this->validation->setRules(['yesterday' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_after_today.yesterday');

        $this->validation->reset();

        $this->validation->setRules(['today' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_after_today.today');

        $this->validation->reset();

        $this->validation->setRules(['tomorrow' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_after_today.tomorrow');
    }

    public function testDateBefore(): void{
        $rules = 'date_before[last_available_date,Y-m-d]';
        
        $this->validation->setRules(['arrival_date' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_before.arrival_date');

        $this->validation->reset();

        $this->validation->setRules(['far_future_date' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_before.far_future_date');

        $this->validation->reset();

        $this->validation->setRules(['last_available_date' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_before.far_future_date');
    }

    public function testDateEnding(): void{
        $rules = 'date_ending[last_available_date,Y-m-d]';
        
        $this->validation->setRules(['arrival_date' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_ending.arrival_date');

        $this->validation->reset();

        $this->validation->setRules(['last_available_date' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_ending.arrival_date');

        $this->validation->reset();

        $this->validation->setRules(['far_future_date' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_ending.far_future_date');
    }

    public function testDateStarting(): void{
        $rules = 'date_starting[arrival_date,Y-m-d]';
        
        $this->validation->setRules(['past_date' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_starting.past_date');
        
        $this->validation->reset();
        
        $this->validation->setRules(['arrival_date' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_ending.arrival_date');
        
        $this->validation->reset();

        $this->validation->setRules(['excursion_date' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_starting.arrival_date');
    }

    public function testDateAfter(): void{
        $rules = 'date_after[arrival_date,Y-m-d]';
        
        $this->validation->setRules(['past_date' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_after.past_date');
        
        $this->validation->reset();
        
        $this->validation->setRules(['arrival_date' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_after.arrival_date');
        
        $this->validation->reset();

        $this->validation->setRules(['excursion_date' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_after.excursion_date');
    }

    public function testDateOnDOW(): void
    {
        $rules = 'date_on_dow[Y-m-d,6]';
        $this->validation->setRules(['past_date' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_on_dow.single-true');
        
        $this->validation->reset();

        $rules = 'date_on_dow[Y-m-d,5,6]';
        $this->validation->setRules(['far_future_date' => $rules]);
        $this->assertTrue($this->validation->run($this->dates), 'date_on_dow.multi-true');
        
        $this->validation->reset();

        $rules = 'date_on_dow[Y-m-d,1,2,3,4,5,7]';
        $this->validation->setRules(['past_date' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_on_dow.multi-false');

        $this->validation->reset();

        $rules = 'date_on_dow[Y-m-d,7]';
        $this->validation->setRules(['past_date' => $rules]);
        $this->assertFalse($this->validation->run($this->dates), 'date_on_dow.single-false');

        $this->validation->reset();

        $rules = 'date_on_dow[Y-m-d]';
        $this->validation->setRules(['past_date' => $rules]);
        $this->expectException(InvalidArgumentException::class);
        $this->validation->run($this->dates);


    }
}