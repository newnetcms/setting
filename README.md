# Newnet Setting Package

## Getting Started

### 1. Install  

Run the following command:

```bash
composer require newnet/setting
```

### 2. Database

Create table for database driver:

```bash
php artisan migrate
```

## Usage

You can either use the helper method like `setting('foo')` or the facade `Setting::get('foo')`

### Facade

```php
Setting::get('foo', 'default');
Setting::set('foo', 'bar');
Setting::forget('foo');
```

### Helper

```php
setting('foo', 'default');
setting(['foo' => 'bar']);
setting()->get('foo', 'default');
setting()->set('foo', 'bar');
setting()->forget('foo');
```

### Blade Directive

You can get the settings directly in your blade templates using the helper method or the blade directive like `@setting('foo')`
