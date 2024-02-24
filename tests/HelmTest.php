<?php

namespace Kytoonlabs\LaravelHelm\Tests;

use Kytoonlabs\LaravelHelm\Helm;
use Kytoonlabs\LaravelHelm\Tests\TestCase;
use Illuminate\Support\Str;

/**
 * @covers Kytoonlabs\LaravelHelm\Helm
 */
class HelmTest extends TestCase
{
    public function testHelmVersion() : void
    {
        $version = Helm::version();
        $version->run();
        $this->assertTrue($version->isSuccessful());
        $this->assertStringContainsString('v3.', $version->getOutput());
    }

    public function testHelmInstall() : void
    {
        $helm = Helm::install(
            'testrelease-'.Str::lower(Str::random(8)),
            'oci://registry-1.docker.io/bitnamicharts/redis',
            [
                '--version' => '18.16.1'
            ]
        );
        $helm->run();
        $this->assertTrue($helm->isSuccessful());
        $this->assertStringContainsString('STATUS: deployed', $helm->getOutput());
    }

    public function testHelmUpgrade() : void
    {
        $helmName = 'testrelease-'.Str::lower(Str::random(8));
        $helm = Helm::upgrade(
            $helmName,
            'oci://registry-1.docker.io/bitnamicharts/redis',
            [
                '--version' => '18.16.1',
                '--install'
            ]
        );
        $helm->run();
        $this->assertTrue($helm->isSuccessful());
        $this->assertStringContainsString('STATUS: deployed', $helm->getOutput());

        $helm = Helm::upgrade(
            $helmName,
            'oci://registry-1.docker.io/bitnamicharts/redis',
            [
                '--version' => '18.16.1'
            ]
        );
        $helm->run();
        $this->assertTrue($helm->isSuccessful());
        $this->assertStringContainsString('STATUS: deployed', $helm->getOutput());
    }

    public function testHelmDelete() : void
    {
        $helm = Helm::delete('testrelease-'.Str::lower(Str::random(8)));
        $helm->run();
        $this->assertFalse($helm->isSuccessful());
        $this->assertStringContainsString('release: not found', $helm->getErrorOutput());

        $releaseName = 'testrelease-'.Str::lower(Str::random(8));
        $helm = Helm::install(
            $releaseName,
            'oci://registry-1.docker.io/bitnamicharts/redis',
            [
                '--version' => '18.16.1'
            ]
        );
        $helm->run();
        $this->assertTrue($helm->isSuccessful());

        $delete = Helm::delete($releaseName);
        $delete->run();
        $this->assertTrue($delete->isSuccessful());
        $this->assertStringContainsString('release "'.$releaseName.'" uninstalled', $delete->getOutput());
    }

    public function testParseEnvironments() : void
    {
        $envs = [
            'KUBECONFIG' => '/path/to/kubeconfig',
            'NAMESPACE' => 'default'
        ];
        $helm = Helm::delete('abc', [], $envs);
        $processEnvs = $helm->getEnv();
        $this->assertEquals($envs, $processEnvs);

        $badEnvs = [
            'KUBECONFIG' => '/path/to/kubeconfig',
            'NAMESPACE' => 'default',
            'BAD_ENVIRONMENT'
        ];
        $helm = Helm::delete('abc', [], $badEnvs);
        $processEnvs = $helm->getEnv();
        $this->assertEquals($envs, $processEnvs);
    }
}
