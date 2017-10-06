# Подбор продуктов на определенную сумму

### Status
[![Build Status](https://travis-ci.org/misikch/combine-products-php.svg?branch=master)](https://travis-ci.org/misikch/combine-products-php)

## Требования

* PHP >= 7.0

## Как использовать:
* `git clone https://github.com/misikch/combine-products-php.git`
* `composer install`
* список продуктов в формате `csv` (пример в директории `storage/products.csv`)
* прописать путь к файлу в `App/Config/main.php`
* `php console combine --sum=900`

## Как запускать тесты
* `wget http://codeception.com/codecept.phar`
* `php codecept.phar run`