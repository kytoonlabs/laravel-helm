<?php

namespace Kytoonlabs\LaravelHelm;

use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class Helm {
    protected static $binary_path = '/usr/local/bin/helm';
    protected $process;

    public function __construct(string $action, array $parameters = [], array $options = [], array $environments = [])
    {
        $command = array_merge([static::$binary_path, $action], $parameters, $this->parseOptions($options));
        $this->process = new Process($command, null, $this->parseEnvironments($environments));
    }

    public static function install(string $name, string $chart, array $options = [], array $environments = []) : Helm
    {
        return static::execute('install', [$name, $chart], $options, $environments);
    }

    public static function upgrade(string $name, string $chart, array $options = [], array $environments = []) : Helm
    {
        return static::execute('upgrade', [$name, $chart], $options, $environments);
    }

    public static function delete(string $name, array $options = [], array $environments = []) : Helm
    {
        return static::execute('delete', [$name], $options, $environments);
    }

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

    public static function setPath(string $path) : void
    {
        static::$binary_path = $path;
    }

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

    public function __call(string $method, array $params)
    {
        return $this->process->{$method}(...$params);
    }
}
