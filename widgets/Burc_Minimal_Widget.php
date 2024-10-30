<?php
class Burc_Minimal_Widget extends WP_Widget
{
	function __construct()
	{
		parent::__construct(
			"burc_yorumlari_widget",
            "Burç Yorumları Widget"
		);
	}
	public function widget($args, $instance)
	{
		global $burcBot;
		echo $instance['before_custom_html'];
		$title = apply_filters( 'widget_title', $instance[ 'title' ] );
		echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];
		echo '<style>'.$instance['css_templates'].'</style>';
		$burcBot->render($instance);
  		echo $args['after_widget'];
		echo $instance['after_custom_html'];
	}
	public function form($instance)
	{
        if (class_exists('burcWidgetModule')) {
			global $burcBot;
			$exts = $burcBot->get_points();
			$options = array(
				array(
					'title' => 'Title',
					'text_name' => 'title',
					'name' => $this->get_field_name('title'),
					'id' => $this->get_field_id('title'),
					'type' => 'input',
					'default_value' => 'Medyum Burak Günlük Burç',
				),
				array(
					'title' => 'Give credits to author',
					'text_name' => 'give_credits',
					'name' => $this->get_field_name('give_credits'),
					'id' => $this->get_field_id('give_credits'),
					'type' => 'select',
					'default_value' => '',
					'options' => array(
						array("key" => "", "text" => "Yes"),
						array("key" => "-1", "text" => "No"),
					)
				),
				array(
					'title' => 'Style Templates',
					'text_name' => 'css_templates',
					'name' => $this->get_field_name('css_templates'),
					'id' => $this->get_field_id('css_templates'),
					'type' => 'textarea-only',
					'default_value' => '',
				),
				array(
					'title' => 'Before Widget Custom Html',
					'text_name' => 'before_custom_html',
					'name' => $this->get_field_name('before_custom_html'),
					'id' => $this->get_field_id('before_custom_html'),
					'type' => 'textarea-only',
					'default_value' => '',
				),
				array(
					'title' => 'After Widget Custom Html',
					'text_name' => 'after_custom_html',
					'name' => $this->get_field_name('after_custom_html'),
					'id' => $this->get_field_id('after_custom_html'),
					'type' => 'textarea-only',
					'default_value' => '',
				)
			);
			foreach($exts as $ext) {
				$options[] = array(
					"title" => $ext["name"]." Icon",
					'text_name' => $ext["slug"],
					'name' => $this->get_field_name($ext["slug"]),
					'id' => $this->get_field_id($ext["slug"]),
					'type' => 'input',
					'default_value' => $ext["icon"],
				);
			}
            $burcWidgetModule = new burcWidgetModule($options, $instance);
            $burcWidgetModule->render_form();
        }
	}
	public function update($new_instance, $old_instance)
	{
		foreach($new_instance as $key => $value) {
			$new_instance[$key] = stripslashes($value);
		}
		return $new_instance;
	}
}