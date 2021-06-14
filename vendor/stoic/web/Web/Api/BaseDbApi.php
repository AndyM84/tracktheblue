<?php

	namespace Stoic\Web\Api;

	use Stoic\Pdo\BaseDbClass;
	use Stoic\Web\Request;
	use Stoic\Web\Resources\HttpStatusCodes;

	/**
	 * Abstract base class for API endpoints.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	abstract class BaseDbApi extends BaseDbClass {
		/**
		 * Creates a new Response object with a default OK HTTP status.
		 *
		 * @return Response
		 */
		protected function newResponse() : Response {
			return new Response(HttpStatusCodes::OK);
		}

		/**
		 * Checks the parameterized input from the given request to ensure it has
		 * the provided keys.  Array format:
		 *
		 * ['key1', 'key2', ...]
		 *
		 * @param Request $request Request object to check for the given variables.
		 * @param array $keysToFind Array of keys to check for in request.
		 * @return boolean
		 */
		protected function requestHasInputVars(Request $request, array $keysToFind) : bool {
			$params = $request->getInput();

			foreach (array_values($keysToFind) as $key) {
				if (!$params->has($key)) {
					return false;
				}
			}

			return true;
		}
	}
