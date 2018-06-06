## Overview

display all possible points in a football match

Points :

* Field goal: 3 points
* Touchdown: 6 points
* PAT (Point-after-touchdown): 1 point

## Install

 
```sh
composer install
```

## Usage


```php
./console football:score  20 23
```

Display debug scores by team :
```php
./console football:score  20 23 -v
```

