<?php

	namespace Stoic\Utilities;

	use Stoic\Chain\DispatchBase;
	use Stoic\Log\AppenderBase;
	use Stoic\Log\MessageDispatch;

	/**
	 * Appender that outputs messages to stdout.
	 *
	 * @package Stoic\IO
	 * @version 1.0.1
	 */
	class LogConsoleAppender extends AppenderBase {
		/**
		 * Internal ConsoleHelper instance.
		 *
		 * @var ConsoleHelper
		 */
		protected $ch = null;


		/**
		 * Instantiates a new LogConsoleAppender class.
		 *
		 * @param ConsoleHelper $ch ConsoleHelper instance for use by object.
		 */
		public function __construct(ConsoleHelper $ch) {
			$this->ch = $ch;

			$this->setKey('LogConsoleAppender');
			$this->setVersion('1.0');

			return;
		}

		/**
		 * Processes the MessageDispatch batch.
		 *
		 * @param mixed $sender Sender data, optional and thus can be 'null'.
		 * @param DispatchBase $dispatch Dispatch object to process.
		 * @return void
		 */
		public function process($sender, DispatchBase &$dispatch) : void {
			if (!($dispatch instanceof MessageDispatch)) {
				return;
			}

			if (count($dispatch->messages) > 0) {
				$output = [];

				foreach (array_values($dispatch->messages) as $message) {
					$output[] = $message->__toString();
				}

				$this->ch->putLine(implode(PHP_EOL, array_values($output)));
			}

			return;
		}
	}
