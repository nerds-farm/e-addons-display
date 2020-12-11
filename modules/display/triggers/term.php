<?php

namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Trigger;
use Elementor\Controls_Manager;

/**
 * Description of Term
 *
 * @author fra
 */
class Term extends Base_Trigger {

    public function get_trigger_controls($element) {
        
        $element->add_control(
                'e_display_term_id', [
            'label' => __('Set Term source', 'elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'current' => [
                    'title' => __('Current', 'elementor'),
                    'icon' => 'fa fa-list',
                ],
                'static' => [
                    'title' => __('Static', 'elementor'),
                    'icon' => 'fa fa-pencil',
                ]
            ],
            'default' => 'current',
            'toggle' => false,
            'separator' => 'before',
                ]
        );
        /*$element->add_control(
                'e_display_term_id_static',
                [
                    'label' => __('Set Term ID', 'elementor'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'condition' => [
                        'e_display_term_id' => 'static',
                    ],
                ]
        );*/
        $element->add_control(
                'e_display_term_id_static',
                [
                    'label' => __('Select Term', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => __('Find Term', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'terms',
                    'condition' => [
                        'e_display_term_id' => 'static',
                    ],
                ]
        );

        $element->add_control(
                'e_display_term',
                [
                    'label' => __('Terms', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => __('Find Terms', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'terms',
                    //'object_type' => $tkey,
                    'description' => __('Triggered if Term is one of this.', 'e-addons'),
                    'multiple' => true,
                    'separator' => 'before',
                ]
        );


        $element->add_control(
                'e_display_term_tax', [
            'label' => __('Taxonomy', 'elementor'),
            'type' => 'e-query',
            'placeholder' => __('Taxonomy Name', 'elementor'),
            'label_block' => true,
            'query_type' => 'taxonomies',
            'multiple' => true,
            'description' => __('Triggered if Term is related with this Taxonomy.', 'elementor'),
           
                ]
        );
        
        
        $element->add_control(
                'e_display_term_field',
                [
                    'label' => __('Term Field', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => __('Meta key or Field name', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'fields',
                    'object_type' => 'term',
                    'description' => __('Triggered by a selected Term Field value', 'elementor'),
                    'label_block' => true,
                ]
        );
        $element->add_control(
                'e_display_term_field_status', [
            'label' => __('Term Field Operator', 'elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => $this->operator_options,
            'default' => 'not_empty',
            'label_block' => true,
            'condition' => [
                'e_display_term_field!' => '',
            ],
                ]
        );
        $element->add_control(
                'e_display_term_field_value', [
            'label' => __('Term Field Value', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'description' => __('The specific value of the Term Field', 'elementor'),
            'label_block' => true,
            'condition' => [
                'e_display_term_field!' => '',
                'e_display_term_field_status!' => ['not', 'isset'],
            ],
                ]
        );


        $element->add_control(
                'e_display_term_tree', [
            'label' => __('Term Tree', 'elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => __('Select', 'elementor'),
                'parent' => __('Is Parent', 'elementor'),
                'root' => __('Is Root', 'elementor'),
                'leaf' => __('Is Leaf', 'elementor'),
                'node' => __('Is Intermediate Node', 'elementor'),
                'child' => __('Has Parent', 'elementor'),
                'sibling' => __('Has Siblings', 'elementor'),
            ],
            'description' => __('Triggered for Term has this relation in Taxonomy Terms Tree.', 'elementor'),
                ]
        );

        $element->add_control(
                'e_display_term_count', [
            'label' => __('Has Posts', 'elementor'),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Triggered for terms has related Posts count.', 'elementor'),
                ]
        );
    }

    public function is_triggered($element, $settings) {
        // TERM

        $term = get_queried_object();
        if (!empty($settings['e_display_term_id'])) {
            switch ($settings['e_display_term_id']) {
                case 'static':
                    $term_tmp = get_term_by('term_id', $settings['e_display_term_id_static']);
                    if ($term_tmp && is_object($term_tmp) && get_class($term_tmp) == 'WP_Term') {
                        $term = $term_tmp;
                    }
                    break;
            }
        }
        
        if ($term && is_object($term) && get_class($term) == 'WP_Term') {


            if (!empty($settings['e_display_term'])) {
                $this->add_triggered('e_display_term');
                if (in_array($term->term_id, $settings['e_display_term'])) {
                    $this->add_conditions('e_display_term');
                }
            }

            if (!empty($settings['e_display_term_tax'])) {
                $this->add_triggered('e_display_term_tax');
                if (in_array($term->taxonomy, $settings['e_display_term_tax'])) {
                    $this->add_conditions('e_display_term_tax');
                }
            }
            
            
            if (!empty($settings['e_display_term_field'])) {

                $this->add_triggered('e_display_term_field');

                $current_term = wp_get_current_term();
                if (Utils::is_term_meta($settings['e_display_term_field'], true)) {
                    $termmeta = get_term_meta($current_term->ID, $settings['e_display_term_field'], true); // false for visitor
                } else {
                    $termmeta = $current_term->{$settings['e_display_term_field']};
                }
                
                $condition_result = self::check_condition($termmeta, $settings['e_display_term_field_status'], $settings['e_display_term_field_value']);
                if ($condition_result) {
                    $this->add_conditions('e_display_term_field');
                }
            }

            // TERMS
            if (!empty($settings['e_display_term_tree'])) {
                $this->add_triggered('e_display_term_tree');
                // is parent
                switch ($settings['e_display_term_tree']) {
                    case 'root':
                        if (!$term->parent) {
                            $this->add_conditions('e_display_term_tree');
                        }
                        break;
                    case 'parent':
                        $children = get_term_children($term->term_id, $term->taxonomy);
                        if (!empty($children) && count($children)) {
                            $this->add_conditions('e_display_term_tree');
                        }
                        break;
                    case 'leaf':
                        $children = get_term_children($term->term_id, $term->taxonomy);
                        if (empty($children)) {
                            $this->add_conditions('e_display_term_tree');
                        }
                        break;
                    case 'node':
                        if ($term->parent) {
                            $children = get_term_children($term->term_id, $term->taxonomy);
                            if (!empty($children)) {
                                $this->add_conditions('e_display_term_tree');
                            }
                        }
                        break;
                    case 'child':
                        if ($term->parent) {
                            $this->add_conditions('e_display_term_tree');
                        }
                        break;
                    case 'sibling':
                        if ($term->parent) {
                            $siblings = get_term_children($term->parent, $term->taxonomy);
                        } else {
                            $args = [
                                'taxonomy' => $term->taxonomy,
                                'parent' => 0,
                                'hide_empty' => false
                            ];
                            $siblings = get_terms($args);
                        }
                        if (!empty($siblings) && count($siblings) > 1) {
                            $this->add_conditions('e_display_term_tree');
                        }
                        break;
                }
            }


            if (!empty($settings['e_display_term_count'])) {
                $this->add_triggered('e_display_term_count');
                if ($term->count) {
                    $this->add_conditions('e_display_term_count');
                }
            }
        }
    }

}
