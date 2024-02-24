<?php

namespace Kytoonlabs\LaravelHelm;

use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class Helm {
    /**
     * The path to the HELM binary
     */
    protected static $binary_path = env('HELM_BINARY_PATH', '/usr/local/bin/helm');

    /**
     * The Symfony Process instance
     */
    protected $process;

    /**
     * Create a new Helm instance
     */
    public function __construct(string $action, array $parameters = [], array $options = [], array $environments = [])
    {
        $command = array_merge([static::$binary_path, $action], $parameters, $this->parseOptions($options));
        $this->process = new Process($command, null, $this->parseEnvironments($environments));
    }

    /**
     * Install a new Helm chart
     */
    public static function install(string $name, string $chart, array $options = [], array $environments = []) : Helm
    {
        return static::execute('install', [$name, $chart], $options, $environments);
    }

    /**
     * Upgrade a Helm chart
     */
    public static function upgrade(string $name, string $chart, array $options = [], array $environments = []) : Helm
    {
        return static::execute('upgrade', [$name, $chart], $options, $environments);
    }

    /**
     * Delete a Helm chart
     */
    public static function delete(string $name, array $options = [], array $environments = []) : Helm
    {
        return static::execute('delete', [$name], $options, $environments);
    }

    /**
     * Get the Helm version
     */
    public static function version() : Helm
    {
        return static::execute('version');
    }

    /**
     * Execute the HELM command
     */
    public static function execute(string $action, array $parameters = [], array $options = [], array $environments = []) : Helm
    {
        return new static($action, $parameters, $options, $environments);
    }

    /**
     * Set the path to the HELM binary
     */
    public static function setPath(string $path) : void
    {
        static::$binary_path = $path;
    }

    /**
     * Parse the options
     */
    protected function parseOptions(array $options) : array
    {
        $flags = [];
        foreach ($options as $name => $value){
            if (is_int($name)){
                $flags[] = Str::startsWith($value, '--') ? $value : '--' . $value;
            } else {
                $flags[] = Str::startsWith($name, '--') ? $name . '=' . $value : '--set '.$name . '=' . $value;
            }
        }
        return $flags;
    }

    /**
     * Parse the environments
     */
    protected function parseEnvironments(array $options) : array
    {
        $envs = [];
        foreach ($options as $name => $value){
            if (is_int($name)) {
                continue;
            }
            $envs[$name] = $value;
        }
        return $envs;
    }

    /**
     * Dynamically call methods on the Symfony Process instance
     */
    public function __call(string $method, array $params)
    {
        return $this->process->{$method}(...$params);
    }
}
