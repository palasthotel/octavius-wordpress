<?php

class grid_octavius_list_box extends grid_list_box
{
	public function __construct()
	{
		$this->content=new StdClass();
		$this->content->type = "";
		$this->content->limit = 5;
		$this->content->category = "";
	}

	public function type()
	{
		return 'octavius_list';
	}

	public function build($editmode) {
		if($editmode)
		{
			if($this->content->category=="")
			{
				return t("Octavius Top Lists");
			}
			$cat = get_category($this->content->category);
			if(is_a($cat,"WP_Error"))
			{
				return t("This box is broken - the category doesn't exist anymore it seems");
			}
			return t("Octavius Top Lists by")." ".$cat->name;
		}
		else
		{	
			if(!class_exists("PH_Octavius_Store"))
			{
				return "<p>Kein Octavius Service verfügbar</p>";
			}
			if($this->content->type == "")
			{
				return "<p>Keine Daten gefunden</p>";
			}
			$store = new PH_Octavius_Store();
			$list = $store->getTop($this->content->type);
			$posts = array();
			foreach ($list as $index => $value) {
				if($index > $this->content->limit) break;
				$post = get_post($value->pid, OBJECT);
				if(is_object($post)){
					$posts[] = "<li><a href='".get_the_permalink($value->pid)."'>".$post->post_title."</a></li>";
				}

			}
			return "<ul>".implode("", $posts)."</ul>";
			
		}
	}

	public function contentStructure() {
		$store = new PH_Octavius_Store();
		$types = $store->getServiceTypes();
		$arr_types = array();
		$arr_types[] = array(
			"key" => "",
			"text" => t("Choose a type"),
		);
		foreach ($types as $type) {
			$arr_types[] = array(
				"key" => $type,
				"text" => $type,
			);
		}
		$args = array(
			'orderby' => 'name',
			'order' => 'ASC'
		);
		$categories = get_categories($args);
		$arr_categories = array();
		$arr_categories[] = array(
						"key" => "",
						"text" => t("All"),
						);
		foreach($categories as $category) { 
			$arr_categories[] = array(
				"key" => $category->cat_ID,
				"text" => t($category->name),
			);
		}
		return array(
					array(
						'key' => 'type',
						'label' => t('Type'),
						'type' => 'select',
						'selections'=>$arr_types,
					),
					array(
						'key' => 'category',
						'label' => t('Category'),
						'type' => 'select',
						'selections'=>$arr_categories,
					),
					array(
						'key'=>'limit',
						'label'=>t('Item count'),
						'type'=>'number',
					),
				);
	}
}
