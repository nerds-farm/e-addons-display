<?php

namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Trigger;
use Elementor\Controls_Manager;

/**
 * Description of Post
 *
 * @author fra
 */
class Post extends Base_Trigger {

    public function get_trigger_controls($element) {

        

        $element->add_control(
                'e_display_post_id', [
            'label' => __('Set Post source', 'elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'current' => [
                    'title' => __('Current', 'elementor'),
                    'icon' => 'fa fa-list',
                ],
                'queried' => [
                    'title' => __('Queried', 'elementor'),
                    'icon' => 'fa fa-globe',
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
                'e_display_post_id_static',
                [
                    'label' => __('Set Post ID', 'elementor'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'condition' => [
                        'e_display_post_id' => 'static',
                    ],
                ]
        );*/
        $element->add_control(
                'e_display_post_id_static',
                [
                    'label' => __('Select Post', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => __('Find Post', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'condition' => [
                        'e_display_post_id' => 'static',
                    ],
                ]
        );
        $element->add_control(
                'e_display_post_id_description', [
            'type' => Controls_Manager::RAW_HTML,
            'raw' => '<small>' . __('NOTE: Current Post and Queried Post may be different. For example if you put a Widget with a Loop in a Page then Queried will be Page and Current will be Post inside the Loop.', 'elementor') . '</small>',
            'separator' => 'after',
                ]
        );

        $element->add_control(
                'e_display_post',
                [
                    'label' => __('Page/Post', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => __('Find Posts', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'description' => __('Triggered if current post is one of this Page/Posts.', 'e-addons'),
                    'multiple' => true,
                    'separator' => 'before',
                ]
        );

        $element->add_control(
                'e_display_post_cpt',
                [
                    'label' => __('Post Type', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => __('Post Type', 'elementor'),
                    'label_block' => true,
                    'multiple' => true,
                    'query_type' => 'posts',
                    'object_type' => 'type',
                ]
        );


        $element->add_control(
                'e_display_post_tax', [
            'label' => __('Taxonomy', 'elementor'),
            'type' => 'e-query',
            'placeholder' => __('Taxonomy Name', 'elementor'),
            'label_block' => true,
            'query_type' => 'taxonomies',
            'description' => __('Triggered if current Post Type is related with this Taxonomy.', 'elementor'),
            'multiple' => true,
                ]
        );
        $element->add_control(
                'e_display_post_tax_term', [
            'label' => __('Taxonomy Term', 'elementor'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'description' => __('Triggered if current Post is related with any Term of this Taxonomy.', 'elementor'),
            'condition' => [
                'e_display_post_tax!' => '',
            ],
                ]
        );

        $element->add_control(
                'e_display_post_terms',
                [
                    'label' => __('Terms', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => __('Term Name', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'terms',
                    'description' => __('Visible if current post is related with this Terms.', 'elementor'),
                    'multiple' => true,
                ]
        );

        $element->add_control(
                'e_display_post_field',
                [
                    'label' => __('Post Field', 'elementor'),
                    'type' => 'e-query',
                    'placeholder' => __('Meta key or Name', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'fields',
                    'object_type' => 'post',
                    'description' => __('Triggered by a selected Post Field value', 'elementor'),
                ]
        );
        $element->add_control(
                'e_display_post_field_status', [
            'label' => __('Post Field Operator', 'elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => $this->operator_options,
            'default' => 'not_empty',
            'label_block' => true,
            'condition' => [
                'e_display_post_field!' => '',
            ],
                ]
        );
        $element->add_control(
                'e_display_post_field_value', [
            'label' => __('Post Field Value', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'description' => __('The specific value of the Post Field', 'elementor'),
            'label_block' => true,
            'condition' => [
                //'e_display_context' => '',
                'e_display_post_field!' => '',
                'e_display_post_field_status!' => ['not', 'isset'],
            ],
                ]
        );

        $element->add_control(
                'e_display_post_format', [
            'label' => __('Format', 'elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => get_post_format_strings(),
            'description' => __('Triggered if current post is setted as one of this format.', 'elementor') . '<br><a href="https://wordpress.org/support/article/post-formats/" target="_blank">' . __('Read more on Post Format.', 'elementor') . '</a>',
            'multiple' => true,
            'label_block' => true,
                ]
        );

        $element->add_control(
                'e_display_post_tree',
                [
                    'label' => __('Post Tree', 'elementor'),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => [
                        '' => __('Select', 'elementor'),
                        'parent' => __('Is Parent', 'elementor'),
                        'root' => __('Is Root', 'elementor'),
                        'leaf' => __('Is Leaf', 'elementor'),
                        'node' => __('Is Intermediate Node', 'elementor'),
                        'child' => __('Has Parent', 'elementor'),
                        'sibling' => __('Has Siblings', 'elementor'),
                        'buddy' => __('Has Term Buddies', 'elementor'),
                    ],
                    'label_block' => true,
                    'description' => __('Triggered if Post has relation in Posts Tree', 'elementor'),
                ]
        );
        $element->add_control(
                'e_display_node_level',
                [
                    'label' => __('Has Level', 'elementor'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'description' => __('Triggered for post has specific level.', 'elementor'),
                    'condition' => [
                        'e_display_post_tree' => 'child',
                    ],
                ]
        );
        $element->add_control(
                'e_display_buddy_term',
                [
                    'label' => __('Terms where find Buddies', 'elementor'), //.' '.$atax,
                    'type' => 'e-query',
                    'placeholder' => __('Term Name', 'elementor'),
                    'label_block' => true,
                    'query_type' => 'terms',
                    'description' => __('Specific a Term for current post has friends.', 'elementor'),
                    //'multiple' => true,
                    'label_block' => true,
                    'condition' => [
                        'e_display_post_tree' => 'buddy',
                    ],
                ]
        );

        $element->add_control(
                'e_display_post_conditional_tags', [
            'label' => __('Conditional Tags - Post', 'elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => [
                'is_sticky' => __('Is Sticky', 'elementor'),
                'is_post_type_hierarchical' => __('Is Hierarchical Post Type', 'elementor'),
                'is_post_type_archive' => __('Is Post Type Archive', 'elementor'),
                'comments_open' => __('Comments open', 'elementor'),
                'pings_open' => __('Pings open', 'elementor'),
                'has_tag' => __('Has Tags', 'elementor'),
                'has_term' => __('Has Terms', 'elementor'),
                'has_excerpt' => __('Has Excerpt', 'elementor'),
                'has_post_thumbnail' => __('Has Post Thumbnail', 'elementor'),
                'has_nav_menu' => __('Has Nav menu', 'elementor'),
            ],
            'multiple' => true,
            'label_block' => true,
            'condition' => [
                'e_display_post_id' => 'current',
            ],
                ]
        );
        $element->add_control(
                'e_display_special', [
            'label' => __('Conditonal Tags - Page', 'elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => [
                'is_front_page' => __('Front Page', 'elementor'),
                'is_home' => __('Home', 'elementor'),
                'is_404' => __('404 Not Found', 'elementor'),
                'is_single' => __('Single', 'elementor'),
                'is_page' => __('Page', 'elementor'),
                'is_attachment' => __('Attachment', 'elementor'),
                'is_preview' => __('Preview', 'elementor'),
                'is_admin' => __('Admin', 'elementor'),
                'is_page_template' => __('Page Template', 'elementor'),
                'is_comments_popup' => __('Comments Popup', 'elementor'),
                /*
                  'static' => __('Static', 'elementor'),
                  'login' => __('Login', 'elementor'),
                  'registration' => __('Registration', 'elementor'),
                  'profile' => __('Profile', 'elementor'),
                 */
                // woocommerce
                'is_woocommerce' => __('A Woocommerce Page', 'elementor'),
                'is_shop' => __('Shop', 'elementor'),
                'is_product' => __('Product', 'elementor'),
                'is_product_taxonomy' => __('Product Taxonomy', 'elementor'),
                'is_product_category' => __('Product Category', 'elementor'),
                'is_product_tag' => __('Product Tag', 'elementor'),
                'is_cart' => __('Cart', 'elementor'),
                'is_checkout' => __('Checkout', 'elementor'),
                'is_add_payment_method_page' => __('Add Payment method', 'elementor'),
                'is_checkout_pay_page' => __('Checkout Pay', 'elementor'),
                'is_account_page' => __('Account page', 'elementor'),
                'is_edit_account_page' => __('Edit Account', 'elementor'),
                'is_lost_password_page' => __('Lost password', 'elementor'),
                'is_view_order_page' => __('Order summary', 'elementor'),
                'is_order_received_page' => __('Order complete', 'elementor'),
            ],
            'multiple' => true,
            'label_block' => true,
            'condition' => [
                'e_display_post_id' => 'current',
            ],
                ]
        );
    }

    public function is_triggered($element, $settings) {
        // POST
        global $post;
        $original_post = $post;

        // GET POST
        if (!empty($settings['e_display_post_id'])) {
            switch ($settings['e_display_post_id']) {
                case 'queried':
                    $post_id = Utils::url_to_postid();
                    if ($post_id) {
                        $post = get_post($post_id);
                    } else {
                        $queried_object = get_queried_object();
                        //if ( $queried_object instanceof WP_Post ) {
                        if ($queried_object && is_object($queried_object) && get_class($queried_object) == 'WP_Post') {
                            $post = get_queried_object();
                        }
                    }
                    break;
                case 'static':
                    $post_tmp = get_post($settings['e_display_post_id_static']);
                    if ($post_tmp && is_object($post_tmp) && get_class($post_tmp) == 'WP_Post') {
                        $post = $post_tmp;
                    }
                    break;
            }
        }

        // cpt
        if (!empty($settings['e_display_post_cpt']) && is_array($settings['e_display_post_cpt'])) {
            $this->add_triggered('e_display_post_cpt');
            $cpt = get_post_type();
            //var_dump($cpt);
            if (in_array($cpt, $settings['e_display_post_cpt'])) {
                $this->add_conditions('e_display_post_cpt');
            }
        }

        // post
        //var_dump($settings['e_display_post']);
        if (!empty($settings['e_display_post']) && is_array($settings['e_display_post'])) {
            $this->add_triggered('e_display_post');
            if (in_array(get_the_ID(), $settings['e_display_post'])) {
                $this->add_conditions('e_display_post');
            }
        }

        // taxonomy
        /* if (!empty($settings['e_display_tax']) && is_array($settings['e_display_tax'])) {
          $tax = get_post_taxonomies();
          //return $tax;
          if (!array_intersect($tax, $settings['e_display_tax'])) {
          $this->add_conditions(] = __('Taxonomy', 'elementor');
          $contexthidden = true;
          }
          } */
        if (!empty($settings['e_display_post_tax'])) {

            $this->add_triggered('e_display_post_tax');

            if (!empty($settings['e_display_post_tax_term'])) {
                $this->add_triggered('e_display_post_tax_term');
            }

            //return $settings['e_display_tax'];
            $tax = get_post_taxonomies();
            $tax_sel = array_intersect($settings['e_display_post_tax'], $tax);
            //return $tax;
            if (!empty($tax_sel)) {
                if (empty($settings['e_display_post_tax_term'])) {
                    $this->add_conditions('e_display_post_tax');
                } else {
                    $terms = Utils::get_post_terms();
                    $tmp = array();
                    if (!empty($terms)) {
                        if (!is_object($terms) && is_array($terms)) {
                            // $terms = wp_list_pluck($terms, 'term_id');
                            foreach ($terms as $aterm) {
                                if ($aterm && is_object($aterm) && get_class($aterm) == 'WP_Term') {
                                    if (in_array($aterm->taxonomy, $settings['e_display_post_tax'])) {
                                        $this->add_conditions('e_display_post_tax_term');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!empty($settings['e_display_post_terms'])) {

            $this->add_triggered('e_display_post_terms');

            // term
            $terms = Utils::get_post_terms();
            //var_dump($terms); die();
            $tmp = array();
            if (!empty($terms)) {
                if (!is_object($terms) && is_array($terms)) {
                    // $terms = wp_list_pluck($terms, 'term_id');
                    foreach ($terms as $aterm) {
                        if ($aterm && is_object($aterm) && get_class($aterm) == 'WP_Term') {
                            $tmp[$aterm->term_id] = $aterm->term_id;
                        }
                    }
                }
                $terms = $tmp;
                $terms_sel = array_intersect($tmp, $settings['e_display_post_terms']);
                if (!empty($terms_sel)) {
                    $this->add_conditions('e_display_post_terms');
                }
            }
        }

        if (isset($settings['e_display_post_field']) && !empty($settings['e_display_post_field'])) {
            $this->add_triggered('e_display_post_field');
            //$postmeta = get_post_meta(, $settings['e_display_post_field'], true);
            $postmeta = Utils::get_post_field(get_the_ID(), $settings['e_display_post_field']);
            $condition_result = self::check_condition($postmeta, $settings['e_display_post_field_status'], $settings['e_display_post_field_value']);
            if ($condition_result) {
                $this->add_conditions('e_display_post_field');
            }
        }

        if (isset($settings['e_display_post_format']) && !empty($settings['e_display_post_format'])) {

            $this->add_triggered('e_display_post_format');

            $format = get_post_format() ? get_post_format() : 'standard';
            if (in_array($format, $settings['e_display_post_format'])) {
                $this->add_conditions('e_display_post_format');
            }
        }


        if (!empty($settings['e_display_post_tree'])) {
            $this->add_triggered('e_display_post_tree');
            switch ($settings['e_display_post_tree']) {
                case 'root':
                    if (!wp_get_post_parent_id($post)) {
                        $this->add_conditions('e_display_post_tree');
                    }
                    break;
                case 'parent':
                    $args = array(
                        'post_parent' => get_the_ID(),
                        'post_type' => get_post_type(),
                        'numberposts' => -1,
                        'post_status' => 'publish'
                    );
                    $children = get_children($args);
                    if (!empty($children) && count($children)) {
                        $this->add_conditions('e_display_post_tree');
                    }
                    break;
                case 'leaf':
                    $args = array(
                        'post_parent' => get_the_ID(),
                        'post_type' => get_post_type(),
                        'numberposts' => -1,
                        'post_status' => 'publish'
                    );
                    $children = get_children($args);
                    if (empty($children)) {
                        $this->add_conditions('e_display_post_tree');
                    }
                    break;
                case 'node':
                    if (wp_get_post_parent_id($post)) {
                        $args = array(
                            'post_parent' => get_the_ID(),
                            'post_type' => get_post_type(),
                            'numberposts' => -1,
                            'post_status' => 'publish'
                        );
                        $children = get_children($args);
                        if (!empty($children)) {

                            $parents = get_post_ancestors();
                            $node_level = count($parents) + 1;
                            if (empty($settings['e_display_node_level']) || $node_level == $settings['e_display_node_level']) {
                                $this->add_conditions('e_display_post_tree');
                            }
                        }
                    }
                    break;
                case 'child':
                    if ($post_parent_ID = wp_get_post_parent_id($post)) {
                        $parent_ids = Utils::explode($settings['e_display_child_parent']);
                        if (empty($settings['e_display_child_parent']) || in_array($post_parent_ID, $parent_ids)) {
                            $this->add_conditions('e_display_post_tree');
                        }
                    }
                    break;
                case 'sibling':
                    if ($post_parent_ID = wp_get_post_parent_id($post)) {
                        $args = array(
                            'post_parent' => $post_parent_ID,
                            'post_type' => get_post_type(),
                            'posts_per_page' => -1,
                            'post_status' => 'publish'
                        );
                        $children = get_children($args);
                        if (!empty($children) && count($children) > 1) {
                            $this->add_conditions('e_display_post_tree');
                        }
                    }
                    break;
                case 'buddy':
                    $posts_ids = array();
                    if ($settings['e_display_buddy_term']) {
                        $term = get_term($settings['e_display_buddy_term']);
                        $terms = array($term);
                    } else {
                        $terms = Utils::get_post_terms();
                    }
                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            $post_args = array(
                                'posts_per_page' => -1,
                                'post_type' => get_post_type(),
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => $term->taxonomy,
                                        'field' => 'term_id', // this can be 'term_id', 'slug' & 'name'
                                        'terms' => $term->term_id,
                                    )
                                )
                            );
                            $term_posts = get_posts($post_args);
                            if (!empty($term_posts) && count($term_posts) > 1) {
                                $posts_ids = wp_list_pluck($term_posts, 'ID');
                                if (in_array(get_the_ID(), $posts_ids)) {
                                    $this->add_conditions('e_display_post_tree');
                                    break;
                                }
                            }
                        }
                    }
                    break;
            }
        }


        if (!empty($settings['e_display_post_conditional_tags']) && is_array($settings['e_display_post_conditional_tags'])) {

            $this->add_triggered('e_display_post_conditional_tags');

            $context_conditional_tags = false;
            $post_type = get_post_type();
            foreach ($settings['e_display_post_conditional_tags'] as $conditional_tags) {
                if (!$context_conditional_tags) {
                    switch ($conditional_tags) {
                        case 'is_post_type_hierarchical':
                        case 'is_post_type_archive':
                            if (is_callable($conditional_tags)) {
                                $context_conditional_tags = call_user_func($conditional_tags, $post_type);
                            }
                            break;
                        case 'has_post_thumbnail':
                            if (is_callable($conditional_tags)) {
                                $context_conditional_tags = call_user_func($conditional_tags, get_the_ID());
                            }
                            break;
                        default:
                            if (is_callable($conditional_tags)) {
                                $context_conditional_tags = call_user_func($conditional_tags);
                            }
                    }
                }
            }
            if ($context_conditional_tags) {
                $this->add_conditions('e_display_post_conditional_tags');
            }
        }

        // specials
        if (!empty($settings['e_display_special']) && is_array($settings['e_display_special'])) {

            $this->add_triggered('e_display_special');

            $context_special = false;
            foreach ($settings['e_display_special'] as $special) {
                if (!$context_special) {
                    switch ($special) {
                        default:
                            if (is_callable($special)) {
                                $context_special = call_user_func($special);
                            }
                    }
                }
            }
            if ($context_special) {
                $this->add_conditions('e_display_special');
            }
        }

        $post = $original_post;
    }

}
