<?php

	namespace Stoic\Web\Resources;

	use Stoic\Chain\DispatchBase;
	use Stoic\Utilities\ParameterHelper;

	/**
	 * Dispatch to allow processing of request authorization.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class ApiAuthorizationDispatch extends DispatchBase {
		/**
		 * Internal ParameterHelper instance containing the request input.
		 *
		 * @var ParameterHelper
		 */
		protected $input = null;
		/**
		 * Internal state determining if request is authorized.
		 *
		 * @var boolean
		 */
		protected $isAuthorized = false;
		/**
		 * The required roles (if any) that were required by the handler.
		 *
		 * @var null|boolean|string[]
		 */
		protected $requiredRoles = null;


		/**
		 * Authorizes the dispatch, setting internal state to `true`.
		 *
		 * @return void
		 */
		public function authorize() : void {
			$this->isAuthorized = true;

			return;
		}

		/**
		 * Retrieves the ParameterHelper instance containing the request input.
		 *
		 * @return ParameterHelper
		 */
		public function getInput() : ParameterHelper {
			return $this->input;
		}

		/**
		 * Retrieves roles (if any) required by the handler.
		 *
		 * @return boolean|null|string[]
		 */
		public function getRequiredRoles() {
			return $this->requiredRoles;
		}

		/**
		 * Initializes the dispatch so it can be processed.  Requires at minimum
		 * the `AuthorizationDispatchStrings::INDEX_INPUT` and
		 * `AuthorizationDispatchStrings::INDEX_ROLES` array values, with the
		 * `AuthorizationDispatchStrings::INDEX_CONSUMABLE` value being optional to
		 * specify if the dispatch should be marked as 'consumable'.
		 *
		 * @param mixed $input Input array variable, fails gracefully if in wrong format.
		 * @return void
		 */
		public function initialize($input) {
			if (!is_array($input) || count($input) < 2) {
				return;
			}

			if (array_key_exists(AuthorizationDispatchStrings::INDEX_INPUT, $input) === false || array_key_exists(AuthorizationDispatchStrings::INDEX_ROLES, $input) === false) {
				return;
			}

			if (!($input[AuthorizationDispatchStrings::INDEX_INPUT] instanceof ParameterHelper)) {
				return;
			}

			$this->input = $input[AuthorizationDispatchStrings::INDEX_INPUT];
			$this->requiredRoles = $input[AuthorizationDispatchStrings::INDEX_ROLES];

			if (array_key_exists(AuthorizationDispatchStrings::INDEX_CONSUMABLE, $input) !== false && $input[AuthorizationDispatchStrings::INDEX_CONSUMABLE]) {
				$this->makeConsumable();
			}

			$this->makeValid();

			return;
		}

		/**
		 * Returns the internal authorized state.
		 *
		 * @return boolean
		 */
		public function isAuthorized() : bool {
			return $this->isAuthorized;
		}
	}
