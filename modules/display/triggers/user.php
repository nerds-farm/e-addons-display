<?php

namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Base\Base_Trigger;
use EAddonsForElementor\Core\Utils;
use Elementor\Controls_Manager;

/**
 * Description of User
 *
 * @author fra
 */
class User extends Base_Trigger {

    public function get_trigger_controls($element) {
        
        

        $element->add_control(
                'e_display_user_id', [
            'label' => __('Set User source', 'e-addons'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'current' => [
                    'title' => __('Current', 'e-addons'),
                    'icon' => 'fa fa-list',
                ],
                'author' => [
                    'title' => __('Author', 'e-addons'),
                    'icon' => 'fa fa-globe',
                ],
                'static' => [
                    'title' => __('Static', 'e-addons'),
                    'icon' => 'fa fa-pencil',
                ]
            ],
            'default' => 'current',
            'toggle' => false,
            'separator' => 'before',
                ]
        );
        /*$element->add_control(
                'e_display_user_id_static',
                [
                    'label' => __('Set User ID', 'elementor'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'min' => 1,
                    'condition' => [
                        'e_display_user_id' => 'static',
                    ],
                ]
        );*/
        $element->add_control(
                'e_display_user_id_static',
                [
                    'label' => __('Select User', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => __('Find User', 'e-addons'),
                    'query_type' => 'users',
                    'label_block' => true,
                    'condition' => [
                        'e_display_user_id' => 'static',
                    ],
                ]
        );
        
        $element->add_control(
                'e_display_user', [
            'label' => __('Selected Users', 'e-addons'),
            'type' => 'e-query',
            'placeholder' => __('Find Users', 'e-addons'),
            'query_type' => 'users',
            'multiple' => true,
            'label_block' => true,
            'description' => __('Triggered if the User is one of this selected Users.', 'e-addons'),
            'separator' => 'before',
                ]
        );
        
        $element->add_control(
                'e_display_user_logged',
                [
                    'label' => __('Logged', 'e-addons'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'in' => [
                            'title' => __('Logged IN User', 'e-addons'),
                            'icon' => 'eicon-person',
                        ],
                        'out' => [
                            'title' => __('Logged OUT Visitor', 'e-addons'),
                            'icon' => 'eicon-lock-user',
                        ]
                    ],
                    'separator' => 'before',
                    'condition' => [
                        'e_display_user_id' => 'current',
                    ],
                ]
        );

        $element->add_control(
                'e_display_user_role',
                [
                    'label' => __('Roles', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => __('Roles', 'e-addons'),
                    'label_block' => true,
                    'multiple' => true,
                    'query_type' => 'users',
                    'object_type' => 'role',
                    'description' => __('If you want limit visualization to specific user roles', 'e-addons'),
                ]
        );

        $element->add_control(
                'e_display_user_can', [
            'label' => __('User can', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'description' => __('Trigger by User capability, for example: "manage_options"', 'e-addons'),
            'label_block' => true,
                ]
        );

        $element->add_control(
                'e_display_user_field',
                [
                    'label' => __('User Field', 'e-addons'),
                    'type' => 'e-query',
                    'placeholder' => __('Meta key or Field name', 'e-addons'),
                    'label_block' => true,
                    'query_type' => 'fields',
                    'object_type' => 'user',
                    'description' => __('Triggered by a selected User Field value', 'e-addons'),
                    'label_block' => true,
                ]
        );


        $element->add_control(
                'e_display_user_field_status', [
            'label' => __('User Field Operator', 'e-addons'),
            'type' => Controls_Manager::SELECT,
            'options' => $this->operator_options,
            'default' => 'not_empty',
            'label_block' => true,
            'condition' => [
                'e_display_user_field!' => '',
            ],
                ]
        );
        $element->add_control(
                'e_display_user_field_value', [
            'label' => __('User Field Value', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'description' => __('The specific value of the User Field', 'e-addons'),
            'label_block' => true,
            'condition' => [
                'e_display_user_field!' => '',
                'e_display_user_field_status!' => ['not', 'isset'],
            ],
                ]
        );

        $element->add_control(
                'e_display_user_ip', [
            'label' => __('Remote IP', 'e-addons'),
            'type' => Controls_Manager::TEXT,
            'description' => __('Write here the list of IP who will be able to view this element.<br>Separate IPs by comma. (ex. "123.123.123.123, 8.8.8.8, 4.4.4.4")', 'e-addons')
            . '<br><b>' . __('Your current IP is: ', 'e-addons') . $_SERVER['REMOTE_ADDR'] . '</b>',
            'label_block' => true,
            'condition' => [
                'e_display_user_id' => 'current',
            ],
                ]
        );
        $element->add_control(
                'e_display_user_referrer', [
            'label' => __('Referrer', 'elementor'),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Triggered when previous page is a specific page.', 'e-addons'),
            'condition' => [
                'e_display_user_id' => 'current',
            ],
                ]
        );
        $element->add_control(
                'e_display_user_referrer_list', [
            'label' => __('Specific referral site authorized:', 'e-addons'),
            'type' => Controls_Manager::TEXTAREA,
            'placeholder' => 'facebook.com' . PHP_EOL . 'google.com',
            'description' => __('Only selected referral, once per line. If empty it is triggered for all external site.', 'e-addons'),
            'condition' => [
                'e_display_user_referrer' => 'yes',
            //'e_display_everyone' => '',
            ],
                ]
        );

        /* $element->add_control(
          'e_display_max_user',
          [
          'label' => __('Max per User', 'elementor'),
          'type' => \Elementor\Controls_Manager::NUMBER,
          'min' => 1,
          'separator' => 'before',
          ]
          ); */

        if (Utils::is_plugin_active('geoip-detect') && function_exists('geoip_detect2_get_info_from_current_ip')) {
            $geoinfo = geoip_detect2_get_info_from_current_ip();
            $countryInfo = new \YellowTree\GeoipDetect\Geonames\CountryInformation();
            if ($countryInfo) {
                $countries = $countryInfo->getAllCountries();
                $element->add_control(
                        'e_display_user_country', [
                    'label' => __('Country', 'e-addons'),
                    'type' => Controls_Manager::SELECT2,
                    'options' => $countries,
                    'description' => __('Trigger for a specific user country.', 'e-addons'),
                    'multiple' => true,
                    'label_block' => true,
                    'condition' => [
                        'e_display_user_id' => 'current',
                    ],
                        ]
                );
                $your_city = '';
                if (!empty($geoinfo) && !empty($geoinfo->city) && !empty($geoinfo->city->names)) {
                    $your_city = '<br>' . __('Actually you are in:', 'e-addons') . ' ' . implode(', ', $geoinfo->city->names);
                }
                $element->add_control(
                        'e_display_user_city', [
                    'label' => __('City', 'e-addons'),
                    'type' => Controls_Manager::TEXT,
                    'description' => __('Write here the name of the city which trigger the condition. Insert the city name translated in one of the supported language (preferable in EN) and don\'t worry about case sensitive. You can insert multiple cities, separated by comma.', 'e-addons') . $your_city,
                    'label_block' => true,
                    'condition' => [
                        'e_display_user_id' => 'current',
                    ],
                        ]
                );
            }
        } else {
            $element->add_control(
                    'e_display_geoip', [
                'label' => '<i class="eicon-map-pin"></i> <b>'.__('Geolocation IP Detection', 'e-addons').'</b>',
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('In order to enable User Localization triggers, you have to install and configure this 3rd parts free plugin: ', 'e-addons') . '<a href="https://wordpress.org/plugins/geoip-detect/" target="_blank">Geolocation IP Detection</a>',
                    ]
            );
        }
    }

    public function is_triggered($element, $settings) {
        // USER & ROLES
        
        global $user;
        $original_user = $user;


        $user = wp_get_current_user();
        $user_id = get_current_user_id();

        if (!empty($settings['e_display_user_id'])) {
            switch ($settings['e_display_user_id']) {
                case 'author':
                    $user_id = get_the_author_meta('ID');
                    if ($user_id) {
                        $user = get_user_by('ID', $user_id);
                    } else {
                        $queried_object = get_queried_object();
                        if ($queried_object && is_object($queried_object) && get_class($queried_object) == 'WP_User') {
                            $user = get_queried_object();
                        }
                    }
                    break;
                case 'static':
                    $user_tmp = get_user_by('ID', $settings['e_display_user_id_static']);
                    if ($user_tmp && is_object($user_tmp) && get_class($user_tmp) == 'WP_User') {
                        $user = $user_tmp;
                    }
                    break;
            }
        }

        if (!empty($settings['e_display_user_logged'])) {
            $this->add_triggered('e_display_user_logged');
            if ($settings['e_display_user_logged'] == 'in') {
                if (is_user_logged_in()) {
                    $this->add_conditions('e_display_user_logged');
                }
            }
            if ($settings['e_display_user_logged'] == 'out') {
                if (!is_user_logged_in()) {
                    $this->add_conditions('e_display_user_logged');
                }
            }
        }

        //roles
        if (!empty($settings['e_display_user_role'])) {

            $this->add_triggered('e_display_user_role');
            
            if ($user && $user->ID) {
                $user_roles = $user->roles; // possibile avere più ruoli
                if (!is_array($user_roles)) {
                    $user_roles = array($user_roles);
                }
                if (is_array($settings['e_display_user_role'])) {
                    $tmp_role = array_intersect($user_roles, $settings['e_display_user_role']);
                    if (!empty($tmp_role)) {
                        $this->add_conditions('e_display_user_role');
                    }
                }
            } else {
                if (in_array('visitor', $settings['e_display_user_role'])) {
                    $this->add_conditions('e_display_user_role');
                }
            }
        }

        // user
        if (!empty($settings['e_display_user'])) {

            $this->add_triggered('e_display_user');

            $users = Utils::explode($settings['e_display_user']);
            $is_user = false;
            if (!empty($users)) {
                $user = wp_get_current_user();
                foreach ($users as $key => $value) {
                    if (is_numeric($value)) {
                        if ($value == $user->ID) {
                            $is_user = true;
                        }
                    }
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        if ($value == $user->user_email) {
                            $is_user = true;
                        }
                    }
                    if ($value == $user->user_login) {
                        $is_user = true;
                    }
                }
            }
            //var_dump($is_user);
            if ($is_user) {
                $this->add_conditions('e_display_user');
                $everyonehidden = TRUE;
            }
        }

        if (isset($settings['e_display_user_can']) && $settings['e_display_user_can']) {

            $this->add_triggered('e_display_user_can');

            $user_can = false;
            if (user_can($user_id, $settings['e_display_user_can'])) {
                $user_can = true;
            }
            if ($user_can) {
                $this->add_conditions('e_display_user_can');
            }
        }

        if (!empty($settings['e_display_user_field'])) {

            $this->add_triggered('e_display_user_field');

            if (Utils::is_meta($settings['e_display_user_field'], 'user')) {
                $usermeta = get_user_meta($user->ID, $settings['e_display_user_field'], true); // false for visitor
            } else {
                $usermeta = $user->{$settings['e_display_user_field']};
            }
            $condition_result = self::check_condition($usermeta, $settings['e_display_user_field_status'], $settings['e_display_user_field_value']);
            if ($condition_result) {
                $this->add_conditions('e_display_user_field');
            }
        }



        // GEOIP
        if (Utils::is_plugin_active('geoip-detect') && function_exists('geoip_detect2_get_info_from_current_ip')) {
            if (!empty($settings['e_display_user_country'])) {
                $this->add_triggered('e_display_user_country');
                if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    $geoinfo = geoip_detect2_get_info_from_current_ip();
                    if (in_array($geoinfo->country->isoCode, $settings['e_display_user_country'])) {
                        $this->add_conditions('e_display_user_country');
                    }
                }
            }

            if (!empty($settings['e_display_user_city'])) {
                $this->add_triggered('e_display_user_city');
                if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    $geoinfo = geoip_detect2_get_info_from_current_ip();
                    $ucity = array_map('strtolower', $geoinfo->city->names);
                    $scity = Utils::explode($settings['e_display_user_city'], 'strtolower');
                    $icity = array_intersect($ucity, $scity);
                    if (!empty($icity)) {
                        $this->add_conditions('e_display_user_city');
                    }
                }
            }
        }


        // referrer
        if (isset($settings['e_display_user_referrer']) && $settings['e_display_user_referrer'] && $settings['e_display_user_referrer_list']) {

            $this->add_triggered('e_display_user_referrer');

            if ($_SERVER['HTTP_REFERER']) {
                $pieces = explode('/', $_SERVER['HTTP_REFERER']);
                $referrer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST); //$pieces[2];
                $referrers = explode(PHP_EOL, $settings['e_display_user_referrer_list']);
                $referrers = array_map('trim', $referrers);
                $ref_found = false;
                foreach ($referrers as $aref) {
                    if ($aref == $referrer || $aref == str_replace('www.', '', $referrer)) {
                        $ref_found = true;
                    }
                }
                if ($ref_found) {
                    $this->add_conditions('e_display_user_referrer');
                }
            }/* else {
              $everyonehidden = TRUE;
              } */
        }

        if (isset($settings['e_display_user_ip']) && $settings['e_display_user_ip']) {

            $this->add_triggered('e_display_user_ip');

            $ips = explode(',', $settings['e_display_user_ip']);
            $ips = array_map('trim', $ips);
            if (in_array($_SERVER['REMOTE_ADDR'], $ips)) {
                $this->add_conditions('e_display_user_ip');
            }
        }

        /*
          if (!empty($settings['e_display_max_user'])) {
          $this->add_triggered('e_display_max_user'] = __('Max per User', 'elementor');
          $user_id = get_current_user_id();
          if ($user_id) {
          $e_display_max_user = get_user_meta($user_id, 'e_display_max_user', true);
          $e_display_max_user_count = 0;
          if (!empty($e_display_max_user[$element->get_id()])) {
          $e_display_max_user_count = $e_display_max_user[$element->get_id()];
          }
          if ($settings['e_display_max_user'] >= $e_display_max_user_count) {
          $this->add_conditions('e_display_max_user'] = __('Max per User', 'elementor');
          }
          }
          }
         */
        
        $user = $original_user;
    }

}
