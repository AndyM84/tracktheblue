<?php

	namespace Stoic\Tests\Web\Resources;

	use PHPUnit\Framework\TestCase;

	use Stoic\Utilities\ParameterHelper;
	use Stoic\Web\Resources\ApiAuthorizationDispatch;
	use Stoic\Web\Resources\AuthorizationDispatchStrings;

	class DispatchesTest extends TestCase {
		public function test_AuthDispatch() {
			$disp = new ApiAuthorizationDispatch();
			
			$disp->initialize(null);
			self::assertFalse($disp->isValid());

			$disp->initialize([]);
			self::assertFalse($disp->isValid());

			$disp->initialize(['test', 'test2']);
			self::assertFalse($disp->isValid());

			$disp->initialize([
				AuthorizationDispatchStrings::INDEX_INPUT => null,
				AuthorizationDispatchStrings::INDEX_ROLES => true
			]);

			self::assertFalse($disp->isValid());

			$disp->initialize([
				AuthorizationDispatchStrings::INDEX_INPUT => new ParameterHelper(),
				AuthorizationDispatchStrings::INDEX_ROLES => true,
				AuthorizationDispatchStrings::INDEX_CONSUMABLE => true
			]);

			self::assertTrue($disp->isValid());
			self::assertTrue($disp->isConsumable());
			self::assertTrue($disp->getRequiredRoles());
			self::assertTrue($disp->getInput() !== null);
			self::assertFalse($disp->isAuthorized());

			$disp->authorize();
			self::assertTrue($disp->isAuthorized());

			return;
		}
	}
