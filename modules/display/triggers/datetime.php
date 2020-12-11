<?php

namespace EAddonsDisplay\Modules\Display\Triggers;

use EAddonsForElementor\Core\Utils;
use EAddonsForElementor\Base\Base_Trigger;
use Elementor\Controls_Manager;

/**
 * Description of DateTime
 *
 * @author fra
 */
class Datetime extends Base_Trigger {

    public function get_title() {
        return __('Date & Time', 'e-addons');
    }

    public function get_trigger_controls($element) {

        $element->add_control(
                'e_display_date_from', [
            'label' => __('Date FROM', 'elementor'),
            'type' => Controls_Manager::DATE_TIME,
            'description' => __('If set the element will appear after this date', 'elementor'),
            'separator' => 'before',
                ]
        );
        $element->add_control(
                'e_display_date_to', [
            'label' => __('Date TO', 'elementor'),
            'type' => Controls_Manager::DATE_TIME,
            'description' => __('If set the element will be visible until this date', 'elementor'),
                ]
        );

        $element->add_control(
                'e_display_period_from', [
            'label' => __('Period FROM', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'description' => __('If set the element will appear after this period', 'elementor'),
            'placeholder' => 'mm/dd',
            'separator' => 'before',
                ]
        );
        $element->add_control(
                'e_display_period_to', [
            'label' => __('Period TO', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'mm/dd',
            'description' => __('If set the element will be visible until this period', 'elementor'),
                ]
        );

        global $wp_locale;
        $element->add_control(
                'e_display_time_week', [
            'label' => __('Days of the WEEK', 'elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => $wp_locale->weekday,
            'description' => __('Select days in the week.', 'elementor'),
            'multiple' => true,
            'separator' => 'before',
                ]
        );

        $element->add_control(
                'e_display_time_month', [
            'label' => __('Months of the year', 'elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => $wp_locale->month,
            'description' => __('Select months of the year.', 'elementor'),
            'multiple' => true,
            'separator' => 'before',
                ]
        );


        $element->add_control(
                'e_display_time_from', [
            'label' => __('Time FROM', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'HH:mm',
            'description' => __('If setted (in H:m format) the element will appear after this time.', 'elementor'),
            'separator' => 'before',
                ]
        );
        $element->add_control(
                'e_display_time_to', [
            'label' => __('Time TO', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'HH:mm',
            'description' => __('If setted (in H:m format) the element will be visible until this time', 'elementor'),
                ]
        );
    }

    public function is_triggered($element, $settings) {
        // DATETIME

        if ($settings['e_display_date_from'] && $settings['e_display_date_to']) {

            $this->add_triggered('date');
            $this->add_triggered('e_display_date_from');
            $this->add_triggered('e_display_date_to');

            // between
            $dateTo = strtotime($settings['e_display_date_to']);
            $dateFrom = strtotime($settings['e_display_date_from']);
            if (current_time('timestamp') >= $dateFrom && current_time('timestamp') <= $dateTo) {
                $this->add_conditions('date');
            }
        } else {
            if ($settings['e_display_date_from']) {

                $this->add_triggered('e_display_date_from');

                $dateFrom = strtotime($settings['e_display_date_from']);
                if (current_time('timestamp') >= $dateFrom) {
                    $this->add_conditions('e_display_date_from');
                }
            }
            if ($settings['e_display_date_to']) {

                $this->add_triggered('e_display_date_to');

                $dateTo = strtotime($settings['e_display_date_to']);
                if (current_time('timestamp') <= $dateTo) {
                    $this->add_conditions('e_display_date_to');
                }
            }
        }

        if ($settings['e_display_period_from'] && $settings['e_display_period_to']) {

            $this->add_triggered('period');
            $this->add_triggered('e_display_period_from');
            $this->add_triggered('e_display_period_to');

            // between
            if (date_i18n('m/d') >= $settings['e_display_period_from'] && date_i18n('m/d') <= $settings['e_display_period_to']) {
                $this->add_conditions('period');
            }
        } else {
            if ($settings['e_display_period_from']) {

                $this->add_triggered('e_display_period_from');

                if (date_i18n('m/d') >= $settings['e_display_period_from']) {
                    $this->add_conditions('e_display_period_from');
                }
            }
            if ($settings['e_display_period_to']) {

                $this->add_triggered('e_display_period_to');

                if (date_i18n('m/d') <= $settings['e_display_period_to']) {
                    $this->add_conditions('e_display_period_to');
                }
            }
        }

        if ($settings['e_display_time_week'] && !empty($settings['e_display_time_week'])) {

            $this->add_triggered('e_display_time_week');

            if (in_array(current_time('w'), $settings['e_display_time_week'])) {
                $this->add_conditions('e_display_time_week');
            }
        }

        if ($settings['e_display_time_month'] && !empty($settings['e_display_time_month'])) {

            $this->add_triggered('e_display_time_month');

            if (in_array(current_time('m'), $settings['e_display_time_month'])) {
                $this->add_conditions('e_display_time_month');
            }
        }


        if ($settings['e_display_time_from'] && $settings['e_display_time_to']) {

            $this->add_triggered('time');
            $this->add_triggered('e_display_time_from');
            $this->add_triggered('e_display_time_to');

            $timeFrom = $settings['e_display_time_from'];
            $timeTo = ($settings['e_display_time_to'] == '00:00') ? '24:00' : $settings['e_display_time_to'];
            if (current_time('H:i') >= $timeFrom && current_time('H:i') <= $timeTo) {
                $this->add_conditions('time');
            }
        } else {
            if ($settings['e_display_time_from']) {

                $this->add_triggered('e_display_time_from');

                $timeFrom = $settings['e_display_time_from'];
                if (current_time('H:i') >= $timeFrom) {
                    $this->add_conditions('e_display_time_from');
                }
            }
            if ($settings['e_display_time_to']) {

                $this->add_triggered('e_display_time_to');

                $timeTo = ($settings['e_display_time_to'] == '00:00') ? '24:00' : $settings['e_display_time_to'];
                if (current_time('H:i') <= $timeTo) {
                    $this->add_conditions('e_display_time_to');
                }
            }
        }
    }

}
