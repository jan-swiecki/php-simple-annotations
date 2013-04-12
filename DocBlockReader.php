<?php

namespace Common;

class DocBlockReaderException extends \Exception
{
}

class DocBlockReader
{
	private $rawDocBlock;
	private $parameters;
	private $keyPattern = "[A-z0-9\_\-]+";
	private $endPattern = "[ ]*(?:@|\r\n|\n)";
	private $parsedAll = FALSE;

	public function __construct($class, $method)
	{
		$reflection = new \ReflectionMethod($class, $method);
		$this->rawDocBlock = $reflection->getDocComment();

		$this->parameters = array();
		// $this->parse();
	}

	private function parseSingle($key)
	{
		if(isset($this->parameters[$key]))
		{
			return $this->parameters[$key];
		}
		else
		{
			if(preg_match("/@".preg_quote($key).$this->endPattern."/", $this->rawDocBlock, $match))
			{
				return TRUE;
			}
			else
			{
				preg_match_all("/@".preg_quote($key)." (.*)".$this->endPattern."/U", $this->rawDocBlock, $matches);
				$size = sizeof($matches[1]);

				// not found
				if($size === 0)
				{
					return NULL;
				}
				// found one, save as scalar
				elseif($size === 1)
				{
					return $this->parseValue($matches[1][0]);
				}
				// found many, save as array
				else
				{
					$this->parameters[$key] = array();
					foreach($matches[1] as $elem)
					{
						$this->parameters[$key][] = $this->parseValue($elem);
					}

					return $this->parameters[$key];
				}
			}
		}
	}

	private function parse()
	{
		$pattern = "/@(?=(.*)".$this->endPattern.")/U";

		preg_match_all($pattern, $this->rawDocBlock, $matches);

		foreach($matches[1] as $rawParameter)
		{
			if(preg_match("/^(".$this->keyPattern.") (.*)$/", $rawParameter, $match))
			{
				if(isset($this->parameters[$match[1]]))
				{
					$this->parameters[$match[1]] = array_merge((array)$this->parameters[$match[1]], (array)$match[2]);
				}
				else
				{
					$this->parameters[$match[1]] = $this->parseValue($match[2]);
				}
			}
			else if(preg_match("/^".$this->keyPattern."$/", $rawParameter, $match))
			{
				$this->parameters[$rawParameter] = TRUE;
			}
			else
			{
				$this->parameters[$rawParameter] = NULL;
			}
		}
	}

	public function getVariableDeclarations($name)
	{
		$declarations = (array)$this->getParameter($name);

		foreach($declarations as &$declaration)
		{
			$declaration = $this->parseVariableDeclaration($declaration, $name);
		}

		return $declarations;
	}

	private function parseVariableDeclaration($declaration, $name)
	{
		$type = gettype($declaration);

		if($type !== 'string')
		{
			throw new \InvalidArgumentException(
				"Raw declaration must be string, $type given. Key='$name'.");
		}

		if(strlen($declaration) === 0)
		{
			throw new \InvalidArgumentException(
				"Raw declaration cannot have zero length. Key='$name'.");
		}

		$declaration = explode(" ", $declaration);
		if(sizeof($declaration) == 1)
		{
			// string is default type
			array_unshift($declaration, "string");
		}

		// take first two as type and name
		$declaration = array(
			'type' => $declaration[0],
			'name' => $declaration[1]
		);

		return $declaration;
	}

	private function parseValue($originalValue)
	{
		if($originalValue && $originalValue !== 'null')
		{
			// try to json decode, if cannot then store as string
			if( ($json = json_decode($originalValue,TRUE)) === NULL)
			{
				$value = $originalValue;
			}
			else
			{
				$value = $json;
			}
		}
		else
		{
			$value = NULL;
		}

		return $value;
	}

	public function getParameters()
	{
		if(! $this->parsedAll)
		{
			$this->parse();
			$this->parsedAll = TRUE;
		}

		return $this->parameters;
	}

	public function getParameter($key)
	{
		return $this->parseSingle($key);
	}
}