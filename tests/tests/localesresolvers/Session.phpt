<?php declare(strict_types = 1);

/**
 * This file is part of the Contributte/Translation
 */

namespace Tests\LocalesResolvers;

use Contributte;
use Symfony;
use Nette;
use Mockery;
use Tester;
use Tests;

$container = require __DIR__ . '/../../bootstrap.php';

class Session extends Tests\TestAbstract
{

	public function test01(): void
	{
		Tester\Assert::null($this->resolve(null, []));
		Tester\Assert::null($this->resolve('cs', ['en']));
		Tester\Assert::same('en', $this->resolve('en', ['en']));
		Tester\Assert::same('en', $this->resolve('en', ['en_US']));
		Tester\Assert::same('cs', $this->resolve('cs', ['en_US', 'cs_CZ']));
	}

	/**
	 * @param string[] $availableLocales
	 * @internal
	 */
	private function resolve(?string $locale, array $availableLocales): ?string
	{
		$responseMock = Mockery::mock(Nette\Http\IResponse::class);
		$sessionMock = Mockery::mock(Nette\Http\Session::class);
		$sessionSection = new Nette\Http\SessionSection($sessionMock, Contributte\Translation\LocalesResolvers\Session::class);

		$sessionMock->shouldReceive('getSection')
			->once()
			->withArgs([Contributte\Translation\LocalesResolvers\Session::class])
			->andReturn($sessionSection);

		$resolver = new Contributte\Translation\LocalesResolvers\Session($responseMock, $sessionMock);
		$translatorMock = Mockery::mock(Contributte\Translation\Translator::class);

		$translatorMock->shouldReceive('getAvailableLocales')
			->once()
			->withNoArgs()
			->andReturn($availableLocales);

		$sessionMock->shouldReceive('isStarted')
			->once()
			->withNoArgs()
			->andReturn(true);

		$responseMock->shouldReceive('isSent')
			->once()
			->withNoArgs()
			->andReturn(true);

		$sessionMock->shouldReceive('start')
			->once()
			->withNoArgs();

		$sessionMock->shouldReceive('exists')
			->once()
			->withNoArgs()
			->andReturn(true);

		$resolver->setLocale($locale);

		return $resolver->resolve($translatorMock);
	}

}


(new Session($container))->run();
