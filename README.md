# Trustpilot API

[![Software License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/edwinhoksberg/trustpilot-api.svg?style=flat-square)](https://packagist.org/packages/edwinhoksberg/trustpilot-api)


This library is a simple class for displaying [TrustPilot](http://trustpilot.com) reviews.

Requires a minimum PHP version of 5.4.

# How to install

## With composer

Execute this command in your terminal:

```
composer require edwinhoksberg/trustpilot-api:dev-master
```

Or add this line to your composer.json:

```
"require": {
    "edwinhoksberg/trustpilot-api": "dev-master"
}
```

and run:

```
composer update
```


## Without composer

Add this line add the top of your php application:

```
require 'src/TrustPilot.php';
```

# How to use

```php
// Initialize the API
$trustpilot = new \TrustPilot\Api('1234567');
```

```php
// Show site total score
$trustpilot->getRatingScore(); // Output: 88
```

```php
// Show site stars
$trustpilot->getRatingStars(); // Output: 4
```

```php
// Show number of stars for a specific star
$stars = $trustpilot->getReviewStarDistrubution();
echo 'This site has ' . $stars[5] . ' ratings of five stars.'; 

// Output: This site has 27 ratings of five stars. 
```

```php
// Dump the data from a random review
var_dump(
    $trustpilot->getRandomReview()
);

// Output:
array(10) {
  ["title"]=>
  string(22) "Very good and excellent prices"
  ["content"]=>
  string(22) "Very good and excellent prices, etc..."
  ["name"]=>
  string(21) "John Doe"
  ["url"]=>
  string(58) "http://www.trustpilot.nl/review/www.mysite.nl#1234567"
  ["language"]=>
  string(5) "en-US"
  ["score"]=>
  int(100)
  ["stars"]=>
  int(5)
  ["score_value"]=>
  string(10) "Excellent"
  ["timestamp"]=>
  int(1418866304)
  ["rating_images"]=>
  array(3) {
    ["small"]=>
    string(50) "//s.trustpilot.com/images/tpelements/stars/s/5.png"
    ["medium"]=>
    string(50) "//s.trustpilot.com/images/tpelements/stars/m/5.png"
    ["large"]=>
    string(50) "//s.trustpilot.com/images/tpelements/stars/l/5.png"
  }
}
```

```php
// Display all reviews
var_dump(
    $trustpilot->getAllReviews()
);

// Output:
array(1) {
  [0]=>
  array(10) {
    ["title"]=>
    string(22) "Very good and excellent prices"
    ["content"]=>
    string(22) "Very good and excellent prices, etc..."
    ["name"]=>
    string(21) "John Doe"
    ["url"]=>
    string(58) "http://www.trustpilot.nl/review/www.mysite.nl#1234567"
    ["language"]=>
    string(5) "en-US"
    ["score"]=>
    int(100)
    ["stars"]=>
    int(5)
    ["score_value"]=>
    string(10) "Excellent"
    ["timestamp"]=>
    int(1418866304)
    ["rating_images"]=>
    array(3) {
      ["small"]=>
      string(50) "//s.trustpilot.com/images/tpelements/stars/s/5.png"
      ["medium"]=>
      string(50) "//s.trustpilot.com/images/tpelements/stars/m/5.png"
      ["large"]=>
      string(50) "//s.trustpilot.com/images/tpelements/stars/l/5.png"
    }
  }
  [1]=>
  ...
  [2]=>
  ...
}
```
