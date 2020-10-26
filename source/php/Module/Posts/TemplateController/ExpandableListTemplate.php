<?php

namespace Modularity\Module\Posts\TemplateController;

class ExpandableListTemplate
{
    protected $module;
    protected $args;

    public $data = array();

    public function __construct(\Modularity\Module\Posts\Posts $module, array $args, $data)
    {
        $this->module = $module;
        $this->args = $args;
        $this->data = $data;

        $fields = json_decode(json_encode(get_fields($this->module->ID)));
        $this->data['classes'] = implode(' ', apply_filters('Modularity/Module/Classes', array('box', 'box-panel'), $this->module->post_type, $this->args));

        $this->data['posts_list_column_titles'] = !empty($fields->posts_list_column_titles) && is_array($fields->posts_list_column_titles) ? $fields->posts_list_column_titles : null;
        $this->data['posts_hide_title_column'] = !isset($fields->posts_hide_title_column) || !$fields->posts_hide_title_column;
        $this->data['title_column_label'] = isset($fields->title_column_label) ? $fields->title_column_label : false;
        $this->data['allow_freetext_filtering'] = $fields->allow_freetext_filtering ?? null;

        $this->getColumnValues();
    }

    public function getColumnValues()
    {
        // if (empty($this->data['posts_list_column_titles'])) {
        //     return;
        // }
        $count = 0;
        foreach ($this->data['posts'] as $post) {
            $column_values = array();
            $tmp = "data_".$count."_post_content";
            if ($this->data['meta'][$tmp][0]) {
              $post->post_content = $this->data['meta'][$tmp][0];
            }
            if ($this->data['posts_data_source'] === 'input') {
              if ($post->column_values !== false && count($post->column_values) > 0) {
                  foreach ($post->column_values as $key => $columnValue) {
                      $column_values[sanitize_title($this->data['posts_list_column_titles'][$key]->column_header)] = $columnValue->value;
                  }
              }
            } else {
              $column_values = get_post_meta($post->ID, 'modularity-mod-posts-expandable-list', true);
            }
            $post->column_values = $column_values;
            ++$count;
        }
    }
}
