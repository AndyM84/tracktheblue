<?php

	namespace Stoic\Utilities;

	use Stoic\Chain\DispatchBase;
	use Stoic\Log\AppenderBase;
	use Stoic\Log\MessageDispatch;
	use Stoic\Utilities\EnumBase;

	/**
	 * Enumerated output types used by the LogFileAppender
	 * class.
	 *
	 * @package Stoic\IO
	 * @version 1.0.1
	 */
	class LogFileOutputTypes extends EnumBase {
		const PLAIN = 1;
		const JSON = 2;
	}

	/**
	 * Appender that adds messages to a file in either
	 * plain text or JSON format.
	 *
	 * @package Stoic\IO
	 * @version 1.0.1
	 */
	class LogFileAppender extends AppenderBase {
		/**
		 * Internal FileHelper instance.
		 *
		 * @var FileHelper
		 */
		protected $fh = null;
		/**
		 * Path to the output file.
		 *
		 * @var string
		 */
		protected $outputFile = null;
		/**
		 * Type of output to add to the output
		 * file.
		 *
		 * @var LogFileOutputTypes
		 */
		protected $outputType = null;


		/**
		 * Instantiates a new LogFileAppender class.
		 *
		 * @param FileHelper $io FileHelper instance for use by object.
		 * @param string $outputFile Path to the output file to append messages into.
		 * @param integer $outputType Optional integer value representing the output type.
		 * @throws \InvalidArgumentException Thrown when an invalid output type is supplied.
		 */
		public function __construct(FileHelper $fh, string $outputFile, int $outputType = LogFileOutputTypes::PLAIN) {
			if (!LogFileOutputTypes::validValue($outputType)) {
				throw new \InvalidArgumentException("Invalid output type supplied");
			}

			$this->fh = $fh;
			$this->outputFile = $outputFile;
			$this->outputType = EnumBase::tryGetEnum($outputType, LogFileOutputTypes::class);

			if (!$this->fh->fileExists($this->outputFile)) {
				$this->fh->touchFile($this->outputFile);
			}

			$this->outputFile = realpath($this->fh->pathJoin($this->outputFile));
			$this->setKey('LogFileAppender');
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
				$output = new StringHelper();

				foreach (array_values($dispatch->messages) as $message) {
					switch ($this->outputType->getValue()) {
						case LogFileOutputTypes::JSON:
							$output->append($message->__toJson());

							break;
						case LogFileOutputTypes::PLAIN:
						default:
							$output->append($message->__toString());

							break;
					}

					$output->append(PHP_EOL);
				}

				$this->fh->putContents($this->outputFile, $output->data(), FILE_APPEND);
			}

			return;
		}
	}
