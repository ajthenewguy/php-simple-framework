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
						Node::create('title'),
						Node::create('link')->properties(array('rel' => 'stylesheet', 'type' => 'text/css', 'href' => 'css/layout.css'))
					),
					Node::create('body',
						Node::create('header',
							Node::create('div',
								Node::create('nav')->property('class', 'primary')
							)->property('class', 'inner')
						)->property('class', 'header'),
						Node::create('article',
							Node::create('h1'),
							Node::create('div')->property('id', 'content')
						),
						Node::create('aside')
					)
				);
				
				if(!empty($this->pages)) {
					$this->Html->body()->header()->div(/*'.inner*/)->nav(/*'.primary'*/)->content(Node::create('ul'));
					foreach($this->pages as $page_slug => $page) {
						$this->Html->body()->header()->div(/*'.inner*/)->nav(/*'.primary'*/)->ul()->push(
							Node::create('li',
								Node::create('a')->property('href', '?page='.$page_slug)->content($page['Title'])
							)
						);
					}
				}
			break;
		}
		
		parent::__construct($properties);
	}
	
	public function Pages($pages = false) {
		if(false !== $pages) {
			$this->pages = $pages;
			if(!empty($this->pages)) {
				$this->Html->body()->header()->div(/*'.inner*/)->nav(/*'.primary'*/)->content(Node::create('ul'));
				foreach($this->pages as $page_slug => $page) {
					$this->Html->body()->header()->div(/*'.inner*/)->nav(/*'.primary'*/)->ul()->push(
						Node::create('li',
							Node::create('a')
							->property('href', '?page='.$page_slug)
							->content($page['Title'])
						)->property('class', (isset($this->slug) && $page_slug == $this->slug ? 'current' : 'page'))
					);
				}
			}
		}
		return $this->pages;
	}
	
	public function Title($value = false) {
		if(false !== $value) {
			$this->Title = $value;
			$this->Html->head()->title()->content($value);
			$this->Html->body()->article()->h1()->content(Node::create('h1', $value));
		}
		return $this->Title;
	}
	
	public function Body($value = false) {
		if(false !== $value) {
			$this->Html->content($value);
		}	
		return $this->Html;
	}
	
	public function Content($value = false) {
		if(false !== $value) {
			$this->Html->body()->article()->div()->push($value);
		}
		return $this->Html->body()->article()->div();
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
