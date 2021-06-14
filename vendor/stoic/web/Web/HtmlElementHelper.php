<?php

	namespace Stoic\Web;

	use Stoic\Utilities\StringHelper;

	/**
	 * Class to aid in generation of
	 * basic HTML elements.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class HtmlElementHelper {
		/**
		 * Collection of any attributes and their
		 * values associated with this element.
		 *
		 * @var array
		 */
		protected $attributes;
		/**
		 * Simple (optional) string of contents for
		 * this element.
		 *
		 * @var StringHelper
		 */
		protected $contents;
		/**
		 * Name of HTML element this object represents.
		 *
		 * @var string
		 */
		protected $tag;


		/**
		 * Instantiates a new HtmlElementHelper object.
		 *
		 * @param string $tag Element name this object will represent/render.
		 */
		public function __construct(string $tag) {
			$this->tag = $tag;
			$this->attributes = [];
			$this->contents = new StringHelper();

			return;
		}

		/**
		 * Adds an attribute onto the element stack.
		 *
		 * If the element already exists, it will be
		 * overwritten.
		 *
		 * @param string $name Attribute name; Must be a valid HTML attribute character set.
		 * @param string $value Value for attribute; Must be a valid HTML attribute character set.
		 * @return void
		 */
		public function addAttribute(string $name, string $value) {
			$this->attributes[$name] = $value;

			return;
		}

		/**
		 * Appends contents to the element's current
		 * contents.
		 *
		 * @param string $contents Content string to append to element contents.
		 * @return void
		 */
		public function appendContents(string $contents) {
			$this->contents->append($contents);

			return;
		}

		/**
		 * Retrieves any current attributes for the element.
		 *
		 * @return array
		 */
		public function getAttributes() : array {
			return $this->attributes;
		}

		/**
		 * Retrieves the current contents for the element.
		 *
		 * @return StringHelper
		 */
		public function getContents() : StringHelper {
			return $this->contents;
		}

		/**
		 * Sets the element's contents, overwriting
		 * any that may have been present.
		 *
		 * @param string|StringHelper $contents New contents for element.
		 * @return void
		 */
		public function setContents($contents) {
			if (!($contents instanceof StringHelper)) {
				$this->contents = new StringHelper($contents);
			} else {
				$this->contents = $contents;
			}

			return;
		}

		/**
		 * Renders the element and any attributes/content
		 * into a simple HTML tag.
		 *
		 * Any attributes including
		 * the ', <, and > characters will have them replaced
		 * with the appropriate HTML entities.
		 *
		 * @param boolean $return Toggles the return or output of the rendered HTML, output by default.
		 * @return StringHelper|void
		 */
		public function render($return = false) {
			$output = new StringHelper("<{$this->tag}");

			if (count($this->attributes) > 0) {
				foreach ($this->attributes as $name => $value) {
					$output->append(" {$name}=\"");
					$output->append(str_replace(array('"', '<', '>'), array('&#34;', '&lt;', '&gt;'), $value));
					$output->append("\"");
				}
			}

			if (!$this->contents->isEmptyOrNullOrWhitespace()) {
				$output->append('>');
				$output->append($this->contents);
				$output->append("</{$this->tag}>");
			} else {
				$output->append(" />");
			}

			if ($return === true) {
				return $output;
			}

			echo($output);

			return;
		}
	}
