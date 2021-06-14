<?php

	namespace Stoic\Tests\Web\Api;

	use PHPUnit\Framework\TestCase;

	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;
	use Stoic\Log\Logger;
	use Stoic\Web\Request;
	use Stoic\Web\Api\Response;
	use Stoic\Web\Api\Stoic;
	use Stoic\Web\Resources\HttpStatusCodes;
	use Stoic\Web\Resources\PageVariables;

	class AuthNode extends NodeBase {
		public $shouldAuth = true;

		public function __construct() {
			$this->setKey('AuthNode');
			$this->setVersion('1.0');

			return;
		}

		public function process($sender, DispatchBase &$dispatch) {
			if ($this->shouldAuth) {
				$dispatch->authorize();
			}

			$dispatch->consume();

			return;
		}
	}

	class StoicTest extends TestCase {
		protected function performOutputTest(Stoic &$stoic, $expected) {
			ob_start();
			$stoic->handle();
			$output = ob_get_contents();
			ob_end_clean();

			self::assertEquals($expected, $output);

			return;
		}

		protected function getStoicInstance() : Stoic {
			$stoic = Stoic::getInstance('./', PageVariables::fromGlobals(), new Logger());
			$stoic->registerEndpoint(null, null, [$this, 'defTest']);
			$stoic->registerEndpoint('GET', '/^test$/i', [$this, 'baseTest']);
			$stoic->registerEndpoint('*', '/^authTest$/i', [$this, 'authTest'], true);

			return $stoic;
		}

		public function defTest(Request $request, array $matches) : Response {
			return new Response(HttpStatusCodes::OK, 'DEFAULT');
		}

		public function baseTest(Request $request, array $matches) : Response {
			return new Response(HttpStatusCodes::OK, 'TEST1');
		}

		public function authTest(Request $request, array $matches) : Response {
			return new Response(HttpStatusCodes::OK, 'TEST2');
		}

		public function test_Handling() {
			$_SESSION = [];
			$_SERVER['REQUEST_METHOD'] = 'GET';

			$stoic = Stoic::getInstance('./', PageVariables::fromGlobals(), new Logger());
			$stoic->handle('');
			$this->performOutputTest($stoic, '"Invalid URL"');

			$_GET['url'] = 'someUrl';
			$stoic = Stoic::getInstance('./', PageVariables::fromGlobals(), new Logger());
			$this->performOutputTest($stoic, '"URL mis-match"');

			unset($_GET['url']);
			$stoic = $this->getStoicInstance();
			$this->performOutputTest($stoic, '"DEFAULT"');

			$_GET['url'] = 'someUrl';
			$stoic = $this->getStoicInstance();
			$this->performOutputTest($stoic, '"DEFAULT"');

			$_GET['url'] = 'test';
			$stoic = $this->getStoicInstance();
			$this->performOutputTest($stoic, '"TEST1"');

			$_GET['url'] = 'authTest';
			$stoic = $this->getStoicInstance();
			$this->performOutputTest($stoic, '"Unable to perform authorization"');

			$node = new AuthNode();
			$stoic->linkAuthorizationNode($node);
			$this->performOutputTest($stoic, '"TEST2"');

			$node->shouldAuth = false;
			$this->performOutputTest($stoic, '"Unauthorized access for auth-only endpoint"');

			unset($_SESSION);
			unset($_GET['url']);
			unset($_SERVER['REQUEST_METHOD']);

			return;
		}
	}
