<?php

/**
 * Description of Document
 *
 * @author Allen
 */
class Document extends Singleton {
	
	const TYPE_HTML = 1;
	
	
	public function __construct($properties = array()) {
		if(isset($properties['type'])) {
			switch(strtolower($properties['type'])) {
				case 'html':
					$this->type = self::TYPE_HTML;
				break;
			}
			unset($properties['type']);
		} else {
			$this->type = self::TYPE_HTML;
		}
		
		switch($this->type) {
			case self::TYPE_HTML:
				$this->Html = Node::create('html',
					Node::create('head',
						Node::create('meta')->property('charset', 'utf-8'),
						Node::create('title')->content('Allen\'s Website'),
						Node::create('link')->properties(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => 'css/layout.css'))
					),
					Node::create('body',
						Node::create('header',
							Node::create('div',
								Node::create('a',
									Node::create('h1')->content('Allen')
								)->properties(array('href' => '#', 'class' => 'brand')),
								Node::create('nav',
									Node::create('ul',
										Node::create('li',
											Node::create('a')->property('href', '?page=home')->content('Home')
										)->property('class', 'link'),
										Node::create('li',
											Node::create('a')->property('href', '?page=contact')->content('Contact')
										)->property('class', 'link')
									)
								)->property('class', 'primary')
							)->property('class', 'inner')
						)->property('class', 'header'),
						Node::create('article'),
						Node::create('aside')
					)
				);

			break;
		}
		
		parent::__construct($properties);
	}
	
	public function Title($value = false) {
		if(false !== $value) {
			$this->Title = $value;
			$this->Html->head()->title()->content($this->Title);
		}
		return $this->Title;
	}
	
	public function Body($value = false) {
		if(false !== $value) {
			$this->Html->content($value);
		}	
		return $this->Html;
	}
	
	public function __toString() {
		switch($this->type) {
			case self::TYPE_HTML:
				ob_start();
				
				echo Node::create('DOCTYPE');
				echo $this->Body();
				
				$return = ob_get_contents();
				ob_end_clean();
				return $return;
			break;
		}
	}
}

?>
