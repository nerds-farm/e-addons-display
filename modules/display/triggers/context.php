<?php

namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Trigger;
use Elementor\Controls_Manager;

/**
 * Description of Context
 *
 * @author fra
 */
class Context extends Base_Trigger {

    public function get_trigger_controls($element) {
        $element->add_control(
                'e_display_parameter', [
            'label' => __('Parameter', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'description' => __('Write here the name of the parameter passed in GET, COOKIE or POST method', 'elementor'),
            'label_block' => true,
            'separator' => 'before',
                ]
        );
        $element->add_control(
                'e_display_parameter_method', [
            'label' => __('Parameter Method', 'elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'GET' => __('GET', 'elementor'),
                'POST' => __('POST', 'elementor'),
                'REQUEST' => __('REQUEST', 'elementor'),
                'COOKIE' => __('COOKIE', 'elementor'),
                'SERVER' => __('SERVER', 'elementor'),
            ],
            'default' => 'REQUEST',
            'toggle' => false,
            'condition' => [
                //'e_display_context' => '',
                'e_display_parameter!' => '',
            ],
                ]
        );
        $element->add_control(
                'e_display_parameter_status', [
            'label' => __('Parameter Operator', 'elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => $this->operator_options,
            'default' => 'valued',
            'label_block' => true,
            'condition' => [
                //'e_display_context' => '',
                'e_display_parameter!' => '',
            ],
                ]
        );
        $element->add_control(
                'e_display_parameter_value', [
            'label' => __('Parameter Value', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'description' => __('The specific value of the parameter', 'elementor'),
            'condition' => [
                //'e_display_context' => '',
                'e_display_parameter!' => '',
                'e_display_parameter_status!' => ['not', 'isset'],
            ],
                ]
        );

        $element->add_control(
                'e_display_archive', [
            'label' => __('Archive Type', 'elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => [
                'is_blog' => __('Home blog (latest posts)', 'elementor'),
                'posts_page' => __('Posts page', 'elementor'),
                'is_tax' => __('Taxonomy', 'elementor'),
                'is_category' => __('Category', 'elementor'),
                'is_tag' => __('Tag', 'elementor'),
                'is_author' => __('Author', 'elementor'),
                'is_date' => __('Date', 'elementor'),
                'is_year' => __('Year', 'elementor'),
                'is_month' => __('Month', 'elementor'),
                'is_day' => __('Day', 'elementor'),
                'is_time' => __('Time', 'elementor'),
                'is_new_day' => __('New Day', 'elementor'),
                'is_search' => __('Search', 'elementor'),
                'is_paged' => __('Paged', 'elementor'),
                'is_main_query' => __('Main Query', 'elementor'),
                'in_the_loop' => __('In the Loop', 'elementor'),
            ],
            'separator' => 'before',
                ]
        );

        $element->add_control(
                'e_display_conditional_tags_site', [
            'label' => __('Site', 'elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => [
                'is_dynamic_sidebar' => __('Dynamic sidebar', 'elementor'),
                'is_active_sidebar' => __('Active sidebar', 'elementor'),
                'is_rtl' => __('RTL', 'elementor'),
                'is_multisite' => __('Multisite', 'elementor'),
                'is_main_site' => __('Main site', 'elementor'),
                'is_child_theme' => __('Child theme', 'elementor'),
                'is_customize_preview' => __('Customize preview', 'elementor'),
                'is_multi_author' => __('Multi author', 'elementor'),
                'is feed' => __('Feed', 'elementor'),
                'is_trackback' => __('Trackback', 'elementor'),
            ],
            'multiple' => true,
            'separator' => 'before',
                ]
        );

        /*
          $element->add_control(
          'e_display_max_user',
          [
          'label' => __('Max per User', 'elementor'),
          'type' => \Elementor\Controls_Manager::NUMBER,
          'min' => 0,
          ]
          );
          $element->add_control(
          'e_display_max_day',
          [
          'label' => __('Max per Day', 'elementor'),
          'type' => \Elementor\Controls_Manager::NUMBER,
          'min' => 1,
          'separator' => 'before',
          ]
          );
          $element->add_control(
          'e_display_max_total',
          [
          'label' => __('Max Total', 'elementor'),
          'type' => \Elementor\Controls_Manager::NUMBER,
          'min' => 1,
          'separator' => 'before',
          ]
          );
         */

        $select_lang = array();
        // WPML
        global $sitepress;
        if (!empty($sitepress)) {
            //$current_language = $sitepress->get_current_language();
            //$default_language = $sitepress->get_default_language();
            $langs = $sitepress->get_ls_languages();
            //var_dump($langs); die();
            if (!empty($langs)) {
                foreach ($langs as $lkey => $lvalue) {
                    $select_lang[$lkey] = $lvalue['native_name'];
                }
            }
        }
        // POLYLANG
        if (Utils::is_plugin_active('polylang') && function_exists('pll_languages_list')) {
            $translations = pll_languages_list();
            $translations_name = pll_languages_list(array('fields' => 'name'));
            //var_dump($translations); die();
            if (!empty($translations)) {
                foreach ($translations as $tkey => $tvalue) {
                    $select_lang[$tvalue] = $translations_name[$tkey];
                }
            }
        }
        // TRANSLATEPRESS
        if (Utils::is_plugin_active('translatepress-multilingual')) {
            $settings = get_option('trp_settings');
            if ($settings && is_array($settings) && isset($settings['publish-languages'])) {
                $languages = $settings['publish-languages'];
                $trp = \TRP_Translate_Press::get_trp_instance();
                $trp_languages = $trp->get_component('languages');
                $published_languages = $trp_languages->get_language_names($languages, 'english_name');
                $select_lang = $published_languages;
            }
        }
        // WEGLOT
        if (Utils::is_plugin_active('weglot')) {
            $select_lang_array = weglot_get_all_languages_configured();
            if (!empty($select_lang_array)) {
                foreach ($select_lang_array as $key => $value) {
                    $select_lang[$value] = $value;
                }
            }
        }

        if (!empty($select_lang)) {
            $element->add_control(
                    'e_display_lang', [
                'label' => __('Language', 'elementor'),
                'type' => Controls_Manager::SELECT2,
                'options' => $select_lang,
                'multiple' => true,
                'separator' => 'before',
                    ]
            );
        }
    }

    public function is_triggered($element, $settings) {
        // CONTEXT

        if (!empty($settings['e_display_archive'])) {
            $this->add_triggered('e_display_archive');
            if (is_callable($settings['e_display_archive'])) {
                $context_archive = call_user_func($settings['e_display_archive']);
            } else {
                $context_archive = is_archive();
            }
            if ($context_archive) {
                $this->add_conditions('e_display_archive');
            }
        }

        if (isset($settings['e_display_parameter']) && $settings['e_display_parameter']) {

            $this->add_triggered('e_display_parameter');

            $my_val = null;
            switch ($settings['e_display_parameter_method']) {
                case 'COOKIE':
                    if (isset($_COOKIE[$settings['e_display_parameter']])) {
                        $my_val = sanitize_textarea_field($_COOKIE[$settings['e_display_parameter']]);
                    }
                    break;
                case 'SERVER':
                    if (isset($_SERVER[$settings['e_display_parameter']])) {
                        $my_val = $_SERVER[$settings['e_display_parameter']];
                    }
                    break;
                case 'GET':
                case 'POST':
                case 'REQUEST':
                default:
                    if (isset($_REQUEST[$settings['e_display_parameter']])) {
                        $my_val = sanitize_textarea_field($_REQUEST[$settings['e_display_parameter']]);
                    }
            }
            $condition_result = self::check_condition($my_val, $settings['e_display_parameter_status'], $settings['e_display_parameter_value']);
            if ($condition_result) {
                $this->add_conditions('e_display_parameter');
            }
        }

        // LANGUAGES
        if (!empty($settings['e_display_lang'])) {
            $this->add_triggered('e_display_lang');

            $current_language = get_locale();
            // WPML
            global $sitepress;
            if (!empty($sitepress)) {
                $current_language = $sitepress->get_current_language(); // return lang code
            }
            // POLYLANG
            if (Utils::is_plugin_active('polylang') && function_exists('pll_languages_list')) {
                $current_language = pll_current_language();
            }
            // TRANSLATEPRESS
            global $TRP_LANGUAGE;
            if (!empty($TRP_LANGUAGE)) {
                $current_language = $TRP_LANGUAGE; // return lang code
            }
            // WEGLOT
            if (Utils::is_plugin_active('weglot')) {
                $current_language = weglot_get_current_language();
            }
            if (in_array($current_language, $settings['e_display_lang'])) {
                $this->add_conditions('e_display_lang');
            }
        }
        /*
          if (!empty($settings['e_display_max_day'])) {
          $this->add_triggered('e_display_max_day'] = __('Max Day', 'elementor');
          $e_display_max = get_option('e_display_max', array());
          //var_dump($e_display_max);echo $element->get_id();
          $today = date('Ymd');
          if (isset($e_display_max[$element->get_id()]) && isset($e_display_max[$element->get_id()]['day']) && isset($e_display_max[$element->get_id()]['day'][$today])) {
          //var_dump($e_display_max[$element->get_id()]['day'][$today]);
          if ($settings['e_display_max_day'] >= $e_display_max[$element->get_id()]['day'][$today]) {
          $this->add_conditions('e_display_max_day'] = __('Max per Day', 'elementor');
          }
          } else {
          $this->add_conditions('e_display_max_day'] = __('Max per Day', 'elementor');
          }
          }
          if (!empty($settings['e_display_max_total'])) {
          $this->add_triggered('e_display_max_total'] = __('Max Total', 'elementor');
          $e_display_max = get_option('e_display_max', array());
          if (isset($e_display_max[$element->get_id()]) && isset($e_display_max[$element->get_id()]['total'])) {
          //var_dump($e_display_max[$element->get_id()]['total']);
          if ($settings['e_display_max_total'] >= $e_display_max[$element->get_id()]['total']) {
          $this->add_conditions('e_display_max_total'] = __('Max Total', 'elementor');
          }
          } else {
          $this->add_conditions('e_display_max_total'] = __('Max Total', 'elementor');
          }
          }
         */
        if (!empty($settings['e_display_conditional_tags_site']) && is_array($settings['e_display_conditional_tags_site'])) {

            $this->add_triggered('e_display_conditional_tags_site');

            $context_conditional_tags = false;
            foreach ($settings['e_display_conditional_tags_site'] as $conditional_tags) {
                if (!$context_conditional_tags) {
                    switch ($conditional_tags) {
                        default:
                            if (is_callable($conditional_tags)) {
                                $context_conditional_tags = call_user_func($conditional_tags);
                            }
                    }
                }
            }
            if ($context_conditional_tags) {
                $this->add_conditions('e_display_conditional_tags_site');
            }
        }
    }

}
