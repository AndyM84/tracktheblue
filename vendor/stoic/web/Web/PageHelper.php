<?php

	namespace Stoic\Web;

	use Stoic\Utilities\ParameterHelper;
	use Stoic\Utilities\StringHelper;
	use Stoic\Web\Resources\HeadersAlreadySentException;

	/**
	 * Class to hold basic information for a web page.
	 *
	 * @package Stoic\Web
	 * @version 1.0.1
	 */
	class PageHelper {
		/**
		 * Internal ParameterHelper instance containing $_GET collection.
		 *
		 * @var ParameterHelper
		 */
		protected $get = null;
		/**
		 * Collection of meta tags for the page.
		 *
		 * @var HtmlElementHelper[]
		 */
		protected $metaTags = [];
		/**
		 * Identifier for this page object.
		 *
		 * @var StringHelper
		 */
		protected $name = null;
		/**
		 * Internal ParameterHelper instance containing $_POST collection.
		 *
		 * @var ParameterHelper
		 */
		protected $post = null;
		/**
		 * Internal ParameterHelper instance containing $_REQUEST collection.
		 *
		 * @var ParameterHelper
		 */
		protected $request = null;
		/**
		 * Represents the root path for assets relative to this page.
		 *
		 * @var StringHelper
		 */
		protected $root = null;
		/**
		 * Page title string.
		 *
		 * @var StringHelper
		 */
		protected $title = null;
		/**
		 * Optional string for prefix of page title.
		 *
		 * @var StringHelper
		 */
		protected $titlePrefix = null;
		/**
		 * Optional separator for page title and prefix.
		 *
		 * @var StringHelper
		 */
		protected $titleSeparator = null;
		
		
		/**
		 * Static collection of PageHelper objects, keyed by name.
		 *
		 * @var PageHelper[]
		 */
		protected static $pages = [];


		/**
		 * Static method to retrieve (or create) a PageHelper object.
		 *
		 * @param string $pagePath Path for the page, used as identifier/name.
		 * @param ParameterHelper $get Optional ParameterHelper instance containing $_GET collection, defaults to contents of $_GET.
		 * @param ParameterHelper $post Optional ParameterHelper instance containing $_POST collection, defaults to contents of $_POST.
		 * @param ParameterHelper $request Optional ParameterHelper instance containing $_REQUEST collection, defaults to contents of $_REQUEST.
		 * @return PageHelper
		 */
		public static function &getPage(string $pagePath, ParameterHelper $get = null, ParameterHelper $post = null, ParameterHelper $request = null) : PageHelper {
			if (array_key_exists($pagePath, static::$pages) === false) {
				static::$pages[$pagePath] = new PageHelper($pagePath);
				static::$pages[$pagePath]->get = new ParameterHelper($_GET);
				static::$pages[$pagePath]->post = new ParameterHelper($_POST);
				static::$pages[$pagePath]->request = new ParameterHelper($_REQUEST);
			}

			if ($get !== null) {
				static::$pages[$pagePath]->get = $get;
			}

			if ($post !== null) {
				static::$pages[$pagePath]->post = $post;
			}

			if ($request !== null) {
				static::$pages[$pagePath]->request = $request;
			}

			return static::$pages[$pagePath];
		}

		/**
		 * Static method to return the root path by removing the given page path
		 * from $_SERVER['SCRIPT_NAME'].
		 *
		 * @param string $pagePath Path value for page, used to clean the server global value.
		 * @return StringHelper
		 */
		public static function getRootPath(string $pagePath) : StringHelper {
			$root = trim(str_replace($pagePath, '', $_SERVER['SCRIPT_NAME']));

			if (empty($root)) {
				$root = '/';
			}

			return new StringHelper($root);
		}


		/**
		 * Instantiates a new PageHelper object using the provided name as the root
		 * path.
		 *
		 * @param string $name String identifier for page name/root-path.
		 */
		protected function __construct(string $name) {
			$this->get = new ParameterHelper();
			$this->name = new StringHelper($name);
			$this->post = new ParameterHelper();
			$this->request = new ParameterHelper();
			$this->root = new StringHelper();
			$this->title = new StringHelper();
			$this->titlePrefix = new StringHelper();
			$this->titleSeparator = new StringHelper();

			$this->setRoot(static::getRootPath($name));

			return;
		}

		/**
		 * Adds a meta element onto the page's internal collection.
		 *
		 * @param string $name Value of 'name' attribute on meta element.
		 * @param string $content Value of 'content' attribute on meta element.
		 * @return void
		 */
		public function addMetaTag(string $name, string $content) {
			$tag = new HtmlElementHelper('meta');
			$tag->addAttribute('name', $name);
			$tag->addAttribute('content', $content);

			$this->metaTags[] = $tag;

			return;
		}

		/**
		 * Converts a given path, if it has '~' as its first character, to be
		 * relative to the root URL path for the page.
		 *
		 * @param string $path Asset path to convert.
		 * @param array $queryVars Optional array of query string items to append to the URL, in format `["key" => "val"]`.
		 * @param bool $includeDomain Optional toggle to include the current domain name in the converted path, defaults to `false`.
		 * @param int $flags Optional entity conversion flags for calls to htmlspecialchars(), defaults to `ENT_COMPAT | ENT_HTML401`.
		 * @param string $encoding Optional encoding for entity conversion, defaults to `'UTF-8'`.
		 * @param bool $doubleEncode Optional toggle for double-encoding entities during conversion, defaults to `false`.
		 * @throws \InvalidArgumentException
		 * @return StringHelper
		 */
		public function getAssetPath(string $path, array $queryVars = null, bool $includeDomain = false, int $flags = ENT_COMPAT | ENT_HTML401, string $encoding = 'UTF-8', bool $doubleEncode = false) : StringHelper {
			$path = new StringHelper(trim($path));

			if ($path->isEmptyOrNullOrWhitespace()) {
				throw new \InvalidArgumentException("Cannot provide empty asset path for conversion");
			}

			if ($this->root !== null && $path->firstChar() == '~') {
				$remaining = $path->subString(($path->at(1) == '/') ? 2 : 1);
				$path = $this->getRootUrlPath($includeDomain);
				$path->append($remaining);
			}

			$path = new StringHelper(htmlspecialchars($path->data(), $flags, $encoding, $doubleEncode));

			if ($queryVars !== null && count($queryVars) > 0) {
				$qs = [];

				foreach ($queryVars as $key => $val) {
					$qs[] = htmlspecialchars($key, $flags, $encoding, $doubleEncode) . "=" . htmlspecialchars($val, $flags, $encoding, $doubleEncode);
				}

				$path->append('?');
				$path->append(implode('&', array_values($qs)));
			}

			return $path;
		}

		/**
		 * Retrieves the internal collection of meta tags.
		 *
		 * @return HtmlElementHelper[]
		 */
		public function getMetaTags() : array {
			return $this->metaTags;
		}

		/**
		 * Retrieves the page identifier.
		 *
		 * @return StringHelper
		 */
		public function getName() : StringHelper {
			return $this->name->copy();
		}

		/**
		 * Retrieves the page root path.
		 *
		 * @return StringHelper
		 */
		public function getRoot() : StringHelper {
			return $this->root->copy();
		}

		/**
		 * Retrieves the compiled root URL path, optionally including the domain.
		 *
		 * @param boolean $includeDomain Optional flag to include the domain name with the root URL path.
		 * @return StringHelper
		 */
		public function getRootUrlPath(bool $includeDomain = false) : StringHelper {
			$path = $this->root->copy();

			if ($includeDomain) {
				$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
				$path = new StringHelper("{$protocol}://{$_SERVER['HTTP_HOST']}/" . $path->subString(($path->at(0) == '/') ? 1 : 0));
			}

			return $path;
		}

		/**
		 * Returns the page title, including prefix and separator if they are
		 * present.
		 *
		 * @return StringHelper
		 */
		public function getTitle() : StringHelper {
			if (!$this->titlePrefix->isEmptyOrNullOrWhitespace()) {
				return new StringHelper($this->titlePrefix . $this->titleSeparator . $this->title);
			}

			return $this->title === null ? new StringHelper() : $this->title->copy();
		}

		/**
		 * Sets the root path for assets relative to this page.
		 *
		 * @param string $root String value to set as root path.
		 * @throws \InvalidArgumentException
		 * @return void
		 */
		public function setRoot(string $root) {
			$root = trim($root);

			if (empty($root)) {
				throw new \InvalidArgumentException("Cannot provide empty root path to PageHelper object");
			}

			if (substr($root, -1) != '/') {
				$root .= '/';
			}

			$this->root = new StringHelper($root);

			return;
		}

		/**
		 * Sets the page title value.
		 *
		 * @param string $title String for page title value.
		 * @return void
		 */
		public function setTitle(string $title) {
			$this->title = new StringHelper($title);

			return;
		}

		/**
		 * Sets the page title prefix and separator values.
		 *
		 * @param string $prefix String for page title prefix value.
		 * @param string $separator Optional string for separator value, default is ' | '.
		 * @return void
		 */
		public function setTitlePrefix(string $prefix, string $separator = ' | ') {
			$this->titlePrefix = new StringHelper($prefix);
			$this->titleSeparator = new StringHelper($separator);

			return;
		}

		/**
		 * Attempts to send a redirect header response to the browser with optional
		 * replacement of root path via the '~' character.
		 *
		 * @codeCoverageIgnore
		 * @param string $destination String value of destination path for redirect (prepend with '~' to make relative to root path).
		 * @param bool $permanent 
		 * @param bool $includeDomain 
		 * @throws HeadersAlreadySentException
		 * @return void
		 */
		public function redirectTo(string $destination, bool $permanent = false, bool $includeDomain = false) {
			if ($this->root !== null && $destination[0] == '~') {
				$root = $this->getRootUrlPath($includeDomain);

				if ($root->endsWith('/')) {
					$root = $root->subString(0, $root->length() - 1);
				}

				$destination = $root . '/' . substr($destination, ($destination[1] == '/') ? 2 : 1);
			}

			if (headers_sent()) {
				throw HeadersAlreadySentException::newWithHeaders("Attempted to redirect to {$destination} after headers were already sent");
			}

			header("Location: {$destination}", true, ($permanent === true) ? 301 : 302);

			exit;
		}
	}
