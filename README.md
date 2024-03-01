# Laravel Helm Package

[![GitHub License](https://img.shields.io/github/license/kytoonlabs/laravel-helm)](https://github.com/kytoonlabs/laravel-helm/blob/main/LICENSE)
[![Codecov](https://img.shields.io/codecov/c/github/kytoonlabs/laravel-helm)](https://app.codecov.io/gh/kytoonlabs/laravel-helm)
[![Packagist Version](https://img.shields.io/packagist/v/kytoonlabs/laravel-helm)](https://packagist.org/packages/kytoonlabs/laravel-helm)

This package provides a wrapper to invoke HELM commands using PHP code.

## Installing

Installing can be done through a variety of methods, although Composer is recommended.

### Composer (recommended)

Include the following snipped into the *composer.json* file.

```json
"require": {
  "kytoonlabs/laravel-helm": "^1.0"
}
```

or by using the `composer require` command:

```php
composer require kytoonlabs/laravel-helm
```

### Github

Releases are available on [Github](https://github.com/kytoonlabs/laravel-helm/releases).

## Configuration

In order to use the Laravel Helm package, is required to setup the right path where the HELM binary is located on the server.

Laravel Helm uses the path `/usr/local/bin/helm` by default, but it can be configured using an environment variable defined into the `.env`

```dotenv
HELM_BINARY_PATH=/path/to/helm/bynary
```

### Other configurations

```dotenv
# Set the internal `Process` timeout, default=3600
HELM_PROCESS_TIMEOUT=3600
```

## How to use

The current version of the package implements the following commands:

- helm version
- helm install
- helm upgrade
- helm delete

Also implements a method `rawCommand` where any other command can be executed.

### Helm::version

```php
use Kytoonlabs\LaravelHelm\Helm;
...
$helm = Helm::version();
$helm->run();
// Prints the command response for the command 'helm version'
$version = $helm->getOutput();
...
```

### Helm::install($name, $chart, $options, $envs)

Parameters:

- name: installation name (**required**)
- chart: helm chart (**required**)
- options: options array (**optional**)
- envs: environment variables array (**optional**)

```php
use Kytoonlabs\LaravelHelm\Helm;
...
$helm = Helm::install(
    'releasename',
    'oci://registry-1.docker.io/bitnamicharts/redis',
    [
        "--version" => '16.18.2'
    ]
);
$helm->run();
// To check the command was executed successfully
if ($helm->isSuccessful()) {
    // HELM app installed
    ...
}
...
```

### Helm::upgrade($name, $chart, $options, $envs)

Parameters:

- name: installation name (**required**)
- chart: helm chart (**required**)
- options: options array (**optional**)
- envs: environment variables array (**optional**)

```php
use Kytoonlabs\LaravelHelm\Helm;
...
$helm = Helm::upgrade(
    'releasename',
    'oci://registry-1.docker.io/bitnamicharts/redis',
    [
        "--version" => '16.18.2',
        '--install'
    ]
);
$helm->run();
// To check the command was executed successfully
if ($helm->isSuccessful()) {
    // HELM app upgraded
    ...
}
...
```

### Helm::delete($name, $options, $envs)

Parameters:

- name: installation name (**required**)
- options: options array (**optional**)
- envs: environment variables array (**optional**)

```php
use Kytoonlabs\LaravelHelm\Helm;
...
$helm = Helm::delete('releasename');
$helm->run();
// To check the command was executed successfully
if ($helm->isSuccessful()) {
    // HELM app uninstalled
    ...
}
...
```

### Helm::rawCommand($command, $options, $envs)

Parameters:

- command: string command to be executed (**required**)
- options: options array (**optional**)
- envs: environment variables array (**optional**)

```php
use Kytoonlabs\LaravelHelm\Helm;
...
// Example using simple commands
$helm = Helm::rawCommand('list');
$helm->run();

// Example using commands with parameters
$helm = Helm::rawCommand('list --all');
$helm->run();

// Example combining with $options
$helm = Helm::rawCommand('list --output json', ['--all']);
$helm->run();
...
```

## Parsing $options array

The most of the methods for the Helm object uses the `$options` array. This is an example how the option are being parsed:

```php
use Kytoonlabs\LaravelHelm\Helm;
...
// Define options array
$options = [
    '--version' => '1.0.0', // include parameters using --name=value
    '--create-namespace', // include single --parameters
    'app.host' => 'https://10.0.0.1', // values from values.yaml file
    '-n' => 'default', // -name value parameters
    '-A', // single - parameters
    'dry-run' // fix invalid inputs 
];
$helm = Helm::install(
    'myredis',
    'oci://registry-1.docker.io/bitnamicharts/redis',
    $options
);
$helm->run();
// the command parsed will be:
// helm install myredis oci://registry-1.docker.io/bitnamicharts/redis --version=1.0.0 
//   --create-namespace --set app.host=https://10.0.0.1 -n default -A --dry-run
...
```

## Injecting environment variables using $envs

If is required to include environment variables to the helm execution context, this can be done using the `$envs` array.

```php
use Kytoonlabs\LaravelHelm\Helm;
...
// List all helm applications pointing to a different cluster
$helm = Helm::rawCommand(
    'list --all',
    [],
    [
        'KUBECONFIG' => '/path/to/another/cluster/kubeconfig'
    ]
);
// Executing helm with the KUBECONFIG env vars enabled
$helm->run();
...
```

## Testing

In order to validate that the package is fully functional always we can run:

```js
composer test
```

## License

Laravel Helm package is licensed under the Apache 2.0. See the LICENSE file for details.

## Support

Issues can be opened directly in Github.
