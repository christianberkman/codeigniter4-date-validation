# codeigniter4-date-validation
Custom validation library for the CodeIgniter4 framework to validate and compare dates.

## To Do
- [ ] Add rules:
    - [ ] date_before_today
    - [ ] date_ending_today
    - [ ] date_starting
    - [ ] date_after, date_before
    - [ ] date_ending
- [ ] Use CodeIgniter4 coding standard library
- [ ] Create unit tests
- [ ] Use languae files for errors

## Installation
### Installation via composer:  
`composer require christianberkman/codeigniter4-date-validation`

If auto discovery in your CodeIgniter app is enabled, the library will be discovered automatically.

### Manual installation
Copy `src/DateValidation.php` to `app/ThirdParty`, adjust namespace and register in `app/Config/Validation.php`

## Usage
The library offers rules for validating the value date to today or another field. All rules also validate the date so the built-in `valid_date` rule does not need to be called.
```
// any date before today's date
date_before_today[Y-m-d]    

// any on or after a given field date
date_starting[date_arrival,Y-m-d]
```
### Date Format
When comparing the value date to a field date, both dates are assumed to be in the same format. All format parameters are optional. If the format paremeter is specified, the dates will be creaed using `DateTime::createFromFormat`. If the format paremeter is omitted, the date is created using `strtotime()`. It is reccommended to always specify a format.

### Time information
All time information is discarded, all times will be set to `00:00:00 UTC`.

## Errors 
Errors are dynamically returned by each rule, e.g. `Date must be before last_available_date field`.

## Available Rules

| Rule                  | Parameters    | Discription                                                       | Example                               |
|-----------------------|---------------|-------------------------------------------------------------------|---------------------------------------|
| date_before_today     | format        | Validates if the value date is before today                       | date_before_today[Y-m-d]              |    
| date_ending_today     | format        | Validates if the value date is on or before today                 | date_ending_today[Y-m-d]              |
| date_starting_today   | format        | Validates if the value date is on or after today                  | date_starting_today[Y-m-d]            |
| date_after_today      | format        | Validates if the value date is after today                        | date_after_todat[Y-m-d]               |
| date_before           | field, format | Validates if the value date is before a field date                | date_before[departure_date,Y-m-d]     |
| date_ending           | field, format | Validates if the value date is on or before a field date          | date_ending[departure_date,Y-m-d]     |
| date_starting         | field, format | Validates if the value date is on or after a field date           | date_starting[arrival_date,Y-m-d]    |
| date_after            | field, format | Validates if the value date is after a field date                 | date_after[date_of_birth,Y-m-d]       |

## Full Example
```php
$data = [
    'arrival_date' => '2024-07-01',
    'excursion_date' => '2024-07-03',
    'depatrue_date' => '2024-07-10',
    'last_available_date' => '2027-08-09',
];

$rules = [
    // arrival needs to be at earliest tomorrow
    'arrival_date' => 'date_after_today', 
    // excursion needs to be between (and not on) departure and arrival
    'excursion_date' => 'date_after[arrival_date,Y-m-d]|date_before[depatrue_date,Y-m-d]', 
    // departure needs to be after (and not on) the arrival date and before (and not on) the last available date
    'departure_date' => 'date_after[arrival_date,Y-m-d]|date_before[last_available_Date]', 
];

$validation = $this->validateData($data, $rules); // returns false
$errors = $this->validator->getErrors();
/**
 * Result:
 * 'departure_date' => 'Date must be before last_available_date field'
 */
```
